<?php

namespace App\Controllers;

class Product extends BaseController
{
    public function myProducts()
    {
        $user = session('logged_in_user');

        if (!$user || empty($user['id'])) {
            return redirect()->to('/')->with('error', 'Please login to view your products.');
        }

        $db = db_connect();
        $productQuery = $db->table('products p')
            ->where('p.user_id', $user['id'])
            ->orderBy('p.created_at', 'DESC');

        if ($db->tableExists('currencies')) {
            $productQuery->select('p.*, c.symbol AS currency_symbol')
                ->join('currencies c', 'c.code = p.currency', 'left');
        } else {
            $productQuery->select('p.*, p.currency AS currency_symbol');
        }

        $products = $productQuery->get()->getResultArray();

        return view('my_products', [
            'page' => 'my-products',
            'products' => $products,
        ]);
    }

    public function myBids()
    {
        $user = session('logged_in_user');

        if (!$user || empty($user['id'])) {
            return redirect()->to('/')->with('error', 'Please login to view your bids.');
        }

        $db = db_connect();
        $bids = $db->table('bid_history bh')
            ->select('bh.id, bh.product_id, bh.user_id, bh.bidprice, bh.createdat, p.id AS product_id_value, p.product_name, p.category, p.currency, p.minimum_price, p.bid_raise_price, p.bidding_start_datetime, p.bidding_end_datetime, p.current_bid_price, p.current_bid_by AS current_bid_owner')
            ->join('products p', 'p.id = bh.product_id', 'left')
            ->where('bh.user_id', $user['id'])
            ->orderBy('bh.createdat', 'DESC')
            ->get()
            ->getResultArray();

        return view('my_bids', [
            'page' => 'my-bids',
            'bids' => $bids,
        ]);
    }

    public function productBids($productId = null)
    {
        $user = session('logged_in_user');

        if (!$user || empty($user['id'])) {
            return redirect()->to('/')->with('error', 'Please login to view product bids.');
        }

        if ($productId === null || !is_numeric($productId)) {
            return redirect()->to('my-products')->with('error', 'Product not found.');
        }

        $db = db_connect();
        $product = $db->table('products')
            ->select('id, product_name, minimum_price, current_bid_price, currency, user_id')
            ->where('id', (int) $productId)
            ->where('user_id', $user['id'])
            ->get()
            ->getRowArray();

        if (!$product) {
            return redirect()->to('my-products')->with('error', 'Product not found.');
        }

        $currency = $db->table('currencies')
            ->select('symbol')
            ->where('code', $product['currency'])
            ->get()
            ->getRowArray();

        $bids = $db->table('bid_history bh')
            ->select('bh.id, bh.product_id, bh.user_id, bh.bidprice, bh.createdat, u.name AS bidder_name, u.email AS bidder_email')
            ->join('users u', 'u.id = bh.user_id', 'left')
            ->where('bh.product_id', (int) $productId)
            ->orderBy('bh.createdat', 'DESC')
            ->get()
            ->getResultArray();

        return view('product_bids', [
            'page' => 'my-products',
            'product' => $product,
            'currency_symbol' => $currency ? $currency['symbol'] : strtoupper($product['currency']),
            'bids' => $bids,
        ]);
    }

    public function edit($id = null)
    {
        $user = session('logged_in_user');
        if (!$user || empty($user['id'])) {
            return redirect()->to('/')->with('error', 'Please login to edit products.');
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to('my-products')->with('error', 'Product not found.');
        }

        $db = db_connect();
        $product = $db->table('products')
            ->where('id', (int) $id)
            ->where('user_id', $user['id'])
            ->get()
            ->getRowArray();

        if (!$product) {
            return redirect()->to('my-products')->with('error', 'Product not found.');
        }

        $currencies = $db->table('currencies')->orderBy('id')->get()->getResultArray();

        return view('edit_product', [
            'page' => 'my-products',
            'product' => $product,
            'currencies' => $currencies,
        ]);
    }

    public function update($id = null)
    {
        $user = session('logged_in_user');
        if (!$user || empty($user['id'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Please login to update products.']);
            }

            return redirect()->to('/')->with('error', 'Please login to update products.');
        }

        if ($id === null || !is_numeric($id)) {
            return redirect()->to('my-products')->with('error', 'Invalid product.');
        }

        $productName = trim($this->request->getPost('product_name'));
        $category = trim($this->request->getPost('category'));
        $description = trim($this->request->getPost('description'));
        $currency = trim($this->request->getPost('currency'));
        $minimumPrice = trim($this->request->getPost('minimum_price'));
        $bidRaisePrice = trim($this->request->getPost('bid_raise_price'));
        $biddingStartDate = trim($this->request->getPost('bidding_start_datetime'));
        $biddingEndDate = trim($this->request->getPost('bidding_end_datetime'));
        $removeImages = $this->request->getPost('remove_images') ?? [];

        if ($productName === '' || $category === '' || $description === '' || $currency === '' || $minimumPrice === '' || $bidRaisePrice === '' || $biddingStartDate === '' || $biddingEndDate === '') {
            return redirect()->back()->with('error', 'Please complete all fields.');
        }

        $db = db_connect();
        $product = $db->table('products')
            ->where('id', (int) $id)
            ->where('user_id', $user['id'])
            ->get()
            ->getRowArray();

        if (!$product) {
            return redirect()->to('my-products')->with('error', 'Product not found.');
        }

        $existingImages = [];
        $storedImages = json_decode($product['image_urls'] ?? '[]', true);
        if (is_array($storedImages)) {
            $existingImages = $storedImages;
        }

        $removeImageKeys = [];
        if (is_array($removeImages)) {
            foreach ($removeImages as $key => $value) {
                $removeImageKeys[(int) $key] = true;
            }
        }

        $finalImages = [];
        foreach ($existingImages as $index => $path) {
            if (isset($removeImageKeys[$index])) {
                $cleanPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
                $filePath = FCPATH . ltrim($cleanPath, DIRECTORY_SEPARATOR);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
                continue;
            }
            $finalImages[] = $path;
        }

        $images = $this->request->getFiles();
        if (!empty($images['product_images'])) {
            foreach ($images['product_images'] as $image) {
                if ($image && $image->isValid() && !$image->hasMoved()) {
                    $newName = $image->getRandomName();
                    $uploadPath = FCPATH . 'public/product_img';

                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    $image->move($uploadPath, $newName);
                    $finalImages[] = 'public/product_img/' . $newName;
                }
            }
        }

        $db->table('products')
            ->where('id', (int) $id)
            ->where('user_id', $user['id'])
            ->update([
                'product_name' => $productName,
                'category' => $category,
                'description' => $description,
                'currency' => $currency,
                'minimum_price' => $minimumPrice,
                'current_bid_price' => $minimumPrice,
                'bid_raise_price' => $bidRaisePrice,
                'bidding_start_datetime' => $biddingStartDate,
                'bidding_end_datetime' => $biddingEndDate,
                'image_urls' => json_encode($finalImages),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('my-products')->with('success', 'Product updated successfully.');
    }

    public function placeBid()
    {
        $user = session('logged_in_user');
        if (empty($user['id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to place a bid.'
            ]);
        }

        $json = $this->request->getJSON(true);
        $productId = (int) ($json['product_id'] ?? $this->request->getPost('product_id') ?? 0);
        $finalBid = (float) ($json['bid_value'] ?? $this->request->getPost('bid_value') ?? 0);

        if ($productId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid product id.'
            ]);
        }

        $db = db_connect();
        $product = $db->table('products')
            ->where('id', $productId)
            ->get()
            ->getRowArray();

        if (!$product) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product not found.'
            ]);
        }

        $creditRow = $db->table('credits')
            ->where('userid', (int) $user['id'])
            ->get()
            ->getRowArray();
        $availableCredits = (int) ($creditRow['credit'] ?? 0);

        if ($availableCredits <= 300) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You need 300 credits to place a bid.',
                'redirect' => base_url('buy-credits'),
            ]);
        }

        $currentBid = (float) ($product['minimum_price'] ?? 0);
        $bidRaise = (float) ($product['bid_raise_price'] ?? 0);
        $minimumAllowed = $currentBid + ($bidRaise > 0 ? $bidRaise : 0);

        if ($finalBid < $minimumAllowed) {
            $finalBid = $minimumAllowed;
        }


        $minimum_credits = $db->table('settings')
            ->select('value')
            ->where('option', 'minimum_bid_value')
            ->get()
            ->getRowArray();

        $db->table('credits')
            ->where('userid', (int) $user['id'])
            ->update([
                'credit' => $availableCredits - $minimum_credits['value'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $db->table('bid_history')
            ->insert([
                'product_id' => $productId,
                'user_id' => (int) $user['id'],
                'bidprice' => $finalBid,
                'createdat' => date('Y-m-d H:i:s'),
            ]);

        $db->table('products')
            ->where('id', $productId)
            ->update([
                'current_bid_price' => $finalBid,
                'current_bid_by' => (int) $user['id'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return $this->response->setJSON([
            'success' => true,
            'new_value' => $finalBid,
            'message' => 'Bid placed successfully.'
        ]);
    }

    public function delete($id = null)
    {
        $user = session('logged_in_user');
        if (!$user || empty($user['id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to delete products.'
            ]);
        }

        $productId = $this->request->getPost('product_id') ?? $id;
        if ($productId === null || !is_numeric($productId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid product id.'
            ]);
        }

        $db = db_connect();
        $product = $db->table('products')
            ->where('id', (int) $productId)
            ->where('user_id', $user['id'])
            ->get()
            ->getRowArray();

        if (!$product) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product not found.'
            ]);
        }

        $imageJson = $product['image_urls'] ?? '[]';
        $imagePaths = json_decode($imageJson, true);
        if (is_array($imagePaths)) {
            foreach ($imagePaths as $path) {
                $normalizedPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
                $filePath = FCPATH . ltrim($normalizedPath, DIRECTORY_SEPARATOR);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
        }

        $db->table('products')
            ->where('id', (int) $productId)
            ->where('user_id', $user['id'])
            ->delete();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }

    public function upload()
    {
        if (!$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method.'
            ]);
        }

        $user = session('logged_in_user');
        if (!$user || empty($user['id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login before uploading a product.'
            ]);
        }

        $productName = trim($this->request->getPost('product_name'));
        $category = trim($this->request->getPost('category'));
        $description = trim($this->request->getPost('description'));
        $currency = trim($this->request->getPost('currency'));
        $minimumPrice = trim($this->request->getPost('minimum_price'));
        $bidRaisePrice = trim($this->request->getPost('bid_raise_price'));
        $biddingStartDate = trim($this->request->getPost('bidding_start_datetime'));
        $biddingEndDate = trim($this->request->getPost('bidding_end_datetime'));

        if ($productName === '' || $category === '' || $description === '' || $currency === '' || $minimumPrice === '' || $bidRaisePrice === '' || $biddingStartDate === '' || $biddingEndDate === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please fill in all product fields.'
            ]);
        }

        try {
            $db = db_connect();
            $this->ensureProductsTable($db);

            $imagePaths = [];
            $images = $this->request->getFiles();

            if (!empty($images['product_images'])) {
                foreach ($images['product_images'] as $image) {
                    if ($image && $image->isValid() && !$image->hasMoved()) {
                        $newName = $image->getRandomName();
                        $uploadPath = FCPATH . 'public/product_img';

                        if (!is_dir($uploadPath)) {
                            mkdir($uploadPath, 0777, true);
                        }

                        $image->move($uploadPath, $newName);
                        $imagePaths[] = 'public/product_img/' . $newName;
                    }
                }
            }

            $inserted = $db->table('products')->insert([
                'user_id' => $user['id'],
                'product_name' => $productName,
                'category' => $category,
                'description' => $description,
                'currency' => $currency,
                'minimum_price' => $minimumPrice,
                'current_bid_price' => $minimumPrice,
                'bid_raise_price' => $bidRaisePrice,
                'bidding_start_datetime' => $biddingStartDate,
                'bidding_end_datetime' => $biddingEndDate,
                'image_urls' => json_encode($imagePaths),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if (!$inserted) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unable to save the product. Please try again.'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Product uploaded successfully.'
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Product upload failed: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Product upload failed due to a server error.'
                ]);
        }
    }

    protected function ensureProductsTable($db)
    {
        $db->query("
            CREATE TABLE IF NOT EXISTS products (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id INT UNSIGNED NOT NULL,
                product_name VARCHAR(255) NOT NULL,
                category VARCHAR(100) NOT NULL,
                description TEXT NOT NULL,
                currency VARCHAR(10) NOT NULL DEFAULT 'INR',
                minimum_price DECIMAL(10,2) NOT NULL,
                bid_raise_price DECIMAL(10,2) NOT NULL,
                bidding_start_datetime DATETIME NOT NULL,
                bidding_end_datetime DATETIME NOT NULL,
                purchased_by_id INT UNSIGNED NULL DEFAULT NULL,
                image_urls LONGTEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_user_id (user_id),
                KEY idx_purchased_by_id (purchased_by_id),
                KEY idx_bidding_dates (bidding_start_datetime, bidding_end_datetime)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $columns = $db->query("SHOW COLUMNS FROM products")->getResultArray();
        $hasCurrencyColumn = false;

        foreach ($columns as $column) {
            if (($column['Field'] ?? '') === 'currency') {
                $hasCurrencyColumn = true;
                break;
            }
        }

        if (!$hasCurrencyColumn) {
            $db->query("ALTER TABLE products ADD COLUMN currency VARCHAR(10) NOT NULL DEFAULT 'INR' AFTER description");
        }
    }
}
