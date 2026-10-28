<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function pageNotFound()
    {
        return $this->response
            ->setStatusCode(404)
            ->setBody(view('errors/html/error_404'));
    }

    public function index(): string
    {
        $featuredProducts = $this->getMarketplaceProducts();
        $featuredProducts = array_slice($featuredProducts, 0, 12);

        return view('index', [
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

    public function robotsTxt()
    {
        $siteUrl = rtrim(base_url(), '/');
        $basePath = rtrim((string) parse_url(base_url(), PHP_URL_PATH), '/');
        $appPath = static function (string $path) use ($basePath): string {
            return $basePath . '/' . ltrim($path, '/');
        };

        $robots = "User-agent: *\nAllow: /\n\n"
            . "Disallow: {$appPath('auth/')}\n"
            . "Disallow: {$appPath('upload-products')}\n"
            . "Disallow: {$appPath('edit-profile')}\n"
            . "Disallow: {$appPath('my-products')}\n"
            . "Disallow: {$appPath('my-bids')}\n"
            . "Disallow: {$appPath('transactions')}\n"
            . "Disallow: {$appPath('product/edit/')}\n"
            . "Disallow: {$appPath('product/bids/')}\n"
            . "Disallow: {$appPath('product/update/')}\n"
            . "Disallow: {$appPath('buy-credits')}\n"
            . "Disallow: {$appPath('payment-successful')}\n"
            . "Disallow: {$appPath('payment-failed')}\n\n"
            . "Sitemap: {$siteUrl}/sitemap.xml\n";

        return $this->response->setContentType('text/plain')->setBody($robots);
    }

    public function sitemap()
    {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        foreach ([
            '/' => date('Y-m-d'),
            '/marketplace' => date('Y-m-d'),
            '/how-it-works' => date('Y-m-d'),
        ] as $path => $lastModified) {
            $location = htmlspecialchars(site_url($path), ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $xml .= "  <url><loc>{$location}</loc><lastmod>{$lastModified}</lastmod><changefreq>daily</changefreq><priority>0.8</priority></url>\n";
        }

        $db = db_connect();
        if ($db->tableExists('products')) {
            $products = $db->table('products')
                ->select('id, updated_at, created_at')
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();

            foreach ($products as $product) {
                $timestamp = $product['updated_at'] ?? $product['created_at'] ?? null;
                $lastModified = $timestamp ? substr((string) $timestamp, 0, 10) : date('Y-m-d');
                $location = htmlspecialchars(site_url('product/' . (int) $product['id']), ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $xml .= "  <url><loc>{$location}</loc><lastmod>{$lastModified}</lastmod><changefreq>daily</changefreq><priority>0.7</priority></url>\n";
            }
        }

        $xml .= '</urlset>'; 
        return $this->response->setContentType('application/xml')->setBody($xml);
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

    public function transactions()
    {
        $user = session('logged_in_user');
        if (!$user || empty($user['id'])) {
            return redirect()->to('/')->with('error', 'Please login to view your transactions.');
        }

        $transactions = db_connect()->table('transactions')
            ->select('id, transactionid, payment_status, message, response, created_at')
            ->where('userid', (int) $user['id'])
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($transactions as &$transaction) {
            $gatewayResponse = json_decode((string) ($transaction['response'] ?? ''), true);
            $amount = is_array($gatewayResponse) ? ($gatewayResponse['amount'] ?? null) : null;
            $transaction['amount'] = is_numeric($amount) ? (float) $amount : null;
        }
        unset($transaction);

        return view('transactions', [
            'page' => 'transactions',
            'transactions' => $transactions,
        ]);
    }

    public function paymentSuccessful()
    {
        if (strtoupper($this->request->getMethod()) === 'POST') {
            $this->storePaymentGatewayResponse();
            return redirect()->to(base_url('payment-successful'), 303);
        }

        return view('payment_successful', [
            'page' => 'payment-successful',
        ]);
    }

    public function paymentFailed()
    {
        if (strtoupper($this->request->getMethod()) === 'POST') {
            $this->storePaymentGatewayResponse();
            return redirect()->to(base_url('payment-failed'), 303);
        }

        return view('payment_failed', [
            'page' => 'payment-failed',
        ]);
    }

    private function storePaymentGatewayResponse(): void
    {
        $gatewayResponse = $this->request->getPost();
        if (!is_array($gatewayResponse)) {
            log_message('warning', 'PayU callback received without transaction data.');
            return;
        }

        $transactionId = trim((string) ($gatewayResponse['txnid'] ?? ''));
        if ($transactionId === '') {
            log_message('warning', 'PayU callback received without a transaction ID.');
            return;
        }

        $message = '';
        foreach (['error_Message', 'field9', 'error'] as $messageField) {
            if (isset($gatewayResponse[$messageField]) && is_scalar($gatewayResponse[$messageField])) {
                $message = trim((string) $gatewayResponse[$messageField]);
                if ($message !== '') {
                    break;
                }
            }
        }

        $status = trim((string) ($gatewayResponse['status'] ?? 'unknown'));
        if ($message === '') {
            $message = 'Payment ' . $status . '.';
        }

        $transactionData = [
            'userid' => (int) ($gatewayResponse['udf1'] ?? 0),
            'transactionid' => $transactionId,
            'payment_status' => $status,
            'message' => $message,
            'response' => json_encode(
                $gatewayResponse,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
            ),
        ];

        $db = db_connect();
        $existing = $db->table('transactions')
            ->select('id')
            ->where('transactionid', $transactionId)
            ->get()
            ->getRowArray();

        if ($existing) {
            $db->table('transactions')
                ->where('id', $existing['id'])
                ->update($transactionData);
            return;
        }

        $db->table('transactions')->insert($transactionData);
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

        $amountInput = $this->request->getPost('amount');
        if (!is_string($amountInput) || !ctype_digit($amountInput)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enter a whole-number amount of at least ₹300.',
            ])->setStatusCode(400);
        }

        $amount = filter_var($amountInput, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 300],
        ]);
        if ($amount === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'The minimum purchase amount is ₹300.',
            ])->setStatusCode(400);
        }

        $merchantKey = env('PAYU_MERCHANT_KEY');
        $merchantSalt = env('PAYU_MERCHANT_SALT');
        
        if (empty($merchantKey) || empty($merchantSalt)) {
            log_message('error', 'PayU checkout requested without merchant credentials configured.');

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payments are not configured. Please try again later.',
            ])->setStatusCode(500);
        }

        $transactionId = bin2hex(random_bytes(12));
        $productInfo = env('APP_NAME', 'Art Bidster') . ' credits - ' . $amount;
        $firstName = trim((string) ($user['name'] ?? 'Customer'));
        $email = trim((string) ($user['email'] ?? ''));
        $phone = trim((string) ($user['phone'] ?? ''));
        $udf1 = (string) $user['id'];
        $udf2 = $udf3 = $udf4 = $udf5 = '';
        $hashString = implode('|', [
            $merchantKey,
            $transactionId,
            (string) $amount,
            $productInfo,
            $firstName,
            $email,
            $udf1,
            $udf2,
            $udf3,
            $udf4,
            $udf5,
        ]) . '||||||' . $merchantSalt;

        return $this->response->setJSON([
            'success' => true,
            'action' => env('PAYU_PAYMENT_URL', 'https://test.payu.in/_payment'),
            'fields' => [
                'key' => $merchantKey,
                'txnid' => $transactionId,
                'amount' => (string) $amount,
                'productinfo' => $productInfo,
                'firstname' => $firstName,
                'email' => $email,
                'phone' => $phone,
                'surl' => base_url('payment-successful'),
                'furl' => base_url('payment-failed'),
                'udf1' => $udf1,
                'udf2' => $udf2,
                'udf3' => $udf3,
                'udf4' => $udf4,
                'udf5' => $udf5,
                'hash' => strtolower(hash('sha512', $hashString)),
            ],
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
