<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $featuredProducts = $this->getMarketplaceProducts();
        $featuredProducts = array_slice($featuredProducts, 0, 12);

        return view('welcome_message', [
            'page' => 'home',
            'featuredProducts' => $featuredProducts,
        ]);
    }

    public function marketplace(): string
    {
        $searchTerm = trim((string) $this->request->getGet('q'));
        $products = $this->getMarketplaceProducts($searchTerm);
        $visibleProducts = array_slice($products, 0, 52);

        return view('marketplace', [
            'page' => 'marketplace',
            'products' => $visibleProducts,
            'totalProducts' => count($products),
            'searchTerm' => $searchTerm,
        ]);
    }

    public function loadMoreProducts()
    {
        $page = max(1, (int) $this->request->getGet('page'));
        $limit = 52;
        $offset = ($page - 1) * $limit;
        $searchTerm = trim((string) $this->request->getGet('q'));

        $products = $this->getMarketplaceProducts($searchTerm);
        $nextProducts = array_slice($products, $offset, $limit);

        $html = '';
        foreach ($nextProducts as $product) {
            $html .= view('partials/product_card', ['product' => $product]);
        }

        return $this->response->setJSON([
            'html' => $html,
            'page' => $page,
            'hasMore' => ($offset + count($nextProducts)) < count($products),
        ]);
    }

    protected function getMarketplaceProducts(?string $searchTerm = null): array
    {
        $db = db_connect();
        $products = [];

        if ($db->tableExists('products')) {
            $builder = $db->table('products')
                ->select('products.*, users.name as uploader_name')
                ->join('users', 'users.id = products.user_id', 'left');

            if ($searchTerm !== null && $searchTerm !== '') {
                $keyword = '%' . $searchTerm . '%';
                $builder->groupStart()
                    ->like('products.product_name', $keyword)
                    ->orLike('users.name', $keyword)
                    ->groupEnd();
            }

            $products = $builder
                ->orderBy('CASE
                    WHEN bidding_start_datetime IS NOT NULL AND bidding_end_datetime IS NOT NULL AND NOW() >= bidding_start_datetime AND NOW() <= bidding_end_datetime THEN 0
                    WHEN bidding_start_datetime IS NOT NULL AND NOW() < bidding_start_datetime THEN 1
                    WHEN bidding_end_datetime IS NOT NULL AND NOW() > bidding_end_datetime THEN 2
                    ELSE 0
                END', 'ASC')
                ->orderBy('products.id', 'DESC')
                ->get()
                ->getResultArray();
        }

        return $products;
    }

    public function productDetails($id): mixed
    {
        $db = db_connect();
        $product = null;

        if ($db->tableExists('products')) {
            $product = $db->table('products')
                ->select('products.*, users.name as uploader_name')
                ->join('users', 'users.id = products.user_id', 'left')
                ->where('products.id', (int) $id)
                ->get()
                ->getRowArray();
        }

        if (!$product) {
            return redirect()->to('/marketplace')->with('error', 'Product not found.');
        }

        $minimum_credits = $db->table('settings')
            ->select('value')
            ->where('option', 'minimum_bid_value')
            ->get()
            ->getRowArray();

        $imageList = json_decode($product['image_urls'] ?? '[]', true);
        $product['images'] = is_array($imageList) ? $imageList : [];

        $similarProducts = [];
        if ($db->tableExists('products') && !empty($product['user_id'])) {
            $similarProducts = $db->table('products')
                ->select('products.*, users.name as uploader_name')
                ->join('users', 'users.id = products.user_id', 'left')
                ->where('products.user_id', (int) $product['user_id'])
                ->where('products.id !=', (int) $product['id'])
                ->orderBy('products.id', 'DESC')
                ->limit(20)
                ->get()
                ->getResultArray();

            if (empty($similarProducts)) {
                $similarProducts = $db->table('products')
                    ->select('products.*, users.name as uploader_name')
                    ->join('users', 'users.id = products.user_id', 'left')
                    ->where('products.id !=', (int) $product['id'])
                    ->orderBy('RAND()')
                    ->limit(20)
                    ->get()
                    ->getResultArray();
            }
        }

        return view('product_details', [
            'page' => 'marketplace',
            'product' => $product,
            'similarProducts' => $similarProducts,
            'minimum_credits' => $minimum_credits['value'] ?? 300
        ]);
    }

    public function howItWorks(): string
    {
        return view('how_it_works', [
            'page' => 'how-it-works',
        ]);
    }

    public function buyCredits(): string
    {
        return view('buy_credits', [
            'page' => 'buy-credits',
        ]);
    }

    public function purchaseCredits()
    {
        $user = session('logged_in_user');
        if (empty($user['id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to buy credits.'
            ]);
        }

        $package = (int) ($this->request->getPost('package') ?? 0);
        $creditMap = [
            300 => 300,
            1200 => 1200,
            3000 => 3000,
        ];

        if (!isset($creditMap[$package])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid credit package.'
            ]);
        }

        $creditAmount = $creditMap[$package];
        $userId = (int) $user['id'];
        $db = db_connect();

        $db->table('credit_history')->insert([
            'userid' => $userId,
            'credit_purchased' => $creditAmount,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $existingCredit = $db->table('credits')
            ->where('userid', $userId)
            ->get()
            ->getRowArray();

        if ($existingCredit) {
            $db->table('credits')
                ->where('userid', $userId)
                ->update([
                    'credit' => (int) ($existingCredit['credit'] ?? 0) + $creditAmount,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            $db->table('credits')->insert([
                'userid' => $userId,
                'credit' => $creditAmount,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Credits purchased successfully.',
            'credit' => $creditAmount,
        ]);
    }

    public function uploadProducts(): string
    {
        $db = db_connect();
        $currencies = [];

        if ($db->tableExists('currencies')) {
            $currencies = $db->table('currencies')
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();
        }

        if (empty($currencies)) {
            $currencies = [[
                'code' => 'INR',
                'name' => 'Indian Rupee',
                'symbol' => '₹',
            ]];
        }

        return view('upload_products', [
            'page' => 'upload-products',
            'currencies' => $currencies,
        ]);
    }
}
