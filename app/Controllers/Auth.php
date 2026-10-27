<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function login()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/')->with('info', 'Please use the login form on the homepage.');
        }

        $email = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');

        if ($email === '' || $password === '') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Email and password are required.']);
            }

            return redirect()->back()->with('error', 'Email and password are required.');
        }

        $db = db_connect();
        $user = $db->table('users')->where('email', $email)->get()->getRowArray();
        $hashedPassword = md5($password);
        $storedPassword = $user['password'] ?? $user['password_hash'] ?? null;

        if (!$user || $storedPassword !== $hashedPassword) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid email or password.']);
            }

            return redirect()->back()->with('error', 'Invalid email or password.');
        }

        session()->set('logged_in_user', [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
        ]);

        $redirectUrl = $this->request->getServer('HTTP_REFERER') ?: base_url('/');
        if (strpos($redirectUrl, 'auth/login') !== false || strpos($redirectUrl, 'auth/register') !== false) {
            $redirectUrl = base_url('/');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Login successful.',
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect()->to($redirectUrl)->with('success', 'Login successful.');
    }

    public function logout()
    {
        session()->remove('logged_in_user');

        return redirect()->to('/')->with('success', 'Logged out successfully.');
    }

    public function editProfile()
    {
        $loggedInUser = session('logged_in_user');

        if (empty($loggedInUser['id'])) {
            return redirect()->to('/')->with('error', 'Please login to edit your profile.');
        }

        $db = db_connect();
        $user = $db->table('users')->where('id', $loggedInUser['id'])->get()->getRowArray();

        if (!$user) {
            return redirect()->to('/')->with('error', 'User not found.');
        }

        return view('edit_profile', [
            'page' => 'edit-profile',
            'user' => $user,
        ]);
    }

    public function updateProfile()
    {
        $loggedInUser = session('logged_in_user');

        if (empty($loggedInUser['id'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Please login to edit your profile.']);
            }

            return redirect()->to('/')->with('error', 'Please login to edit your profile.');
        }

        if (!$this->request->is('post')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Please submit the profile form.']);
            }

            return redirect()->to('/edit-profile')->with('error', 'Please submit the profile form.');
        }

        $name = trim($this->request->getPost('name'));
        $email = trim($this->request->getPost('email'));
        $phone = trim($this->request->getPost('phone'));

        if ($name === '' || $email === '' || $phone === '') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Name, email and phone are required.']);
            }

            return redirect()->back()->with('error', 'Name, email and phone are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Please enter a valid email address.']);
            }

            return redirect()->back()->with('error', 'Please enter a valid email address.');
        }

        $db = db_connect();
        $existingUser = $db->table('users')
            ->where('email', $email)
            ->where('id !=', $loggedInUser['id'])
            ->get()
            ->getRowArray();

        if ($existingUser) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'This email is already registered to another user.']);
            }

            return redirect()->back()->with('error', 'This email is already registered to another user.');
        }

        $updated = $db->table('users')
            ->where('id', $loggedInUser['id'])
            ->update([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if (!$updated) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unable to update profile. Please try again.']);
            }

            return redirect()->back()->with('error', 'Unable to update profile. Please try again.');
        }

        session()->set('logged_in_user', [
            'id' => $loggedInUser['id'],
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Profile updated successfully.'
            ]);
        }

        return redirect()->to('/edit-profile')->with('success', 'Profile updated successfully.');
    }

    public function register()
    {

        if (!$this->request->is('post')) {
            return redirect()->to('/')->with('info', 'Please use the registration form on the homepage.');
        }

        $name = trim($this->request->getPost('name'));
        $email = trim($this->request->getPost('email'));
        $phone = trim($this->request->getPost('phone'));
        $password = $this->request->getPost('password');

        if ($name === '' || $email === '' || $phone === '' || $password === '') {
            return redirect()->back()->with('error', 'Please fill in all registration fields.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Please enter a valid email address.']);
            }

            return redirect()->back()->with('error', 'Please enter a valid email address.');
        }

        try {
            $this->ensureDatabaseExists();
            $db = db_connect();
            $this->ensureUsersTable($db);

            $existingUser = $db->table('users')->where('email', $email)->get()->getRowArray();
            if ($existingUser) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'This email is already registered.']);
                }

                return redirect()->back()->with('error', 'This email is already registered.');
            }

            $hashedPassword = md5($password);

            $inserted = $db->table('users')->insert([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => $hashedPassword,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if (!$inserted) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Unable to create account. Please try again.']);
                }

                return redirect()->back()->with('error', 'Unable to create account. Please try again.');
            }

            $user = $db->table('users')->where('email', $email)->get()->getRowArray();

            session()->set('logged_in_user', [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
            ]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true, 'message' => 'Account created successfully.']);
            }

            return redirect()->to('/')->with('success', 'Account created successfully.');
        } catch (\Throwable $e) {
            log_message('error', 'Register AJAX failed: ' . $e->getMessage());

            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['success' => false, 'message' => 'Registration failed due to a server error. Message: ' . $e->getMessage()]);
            }

            return redirect()->back()->with('error', 'Registration failed due to a server error.');
        }
    }

    protected function ensureDatabaseExists(): void
    {
        $conn = @new \mysqli('localhost', 'root', '', 'mysql');

        if ($conn->connect_error) {
            throw new \RuntimeException('MySQL connection failed: ' . $conn->connect_error);
        }

        $result = $conn->query("CREATE DATABASE IF NOT EXISTS bidding CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        if ($result === false) {
            throw new \RuntimeException('Failed to create bidding database: ' . $conn->error);
        }

        $conn->close();
    }

    protected function ensureUsersTable($db)
    {
        $db->query("
            CREATE TABLE IF NOT EXISTS users (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                phone VARCHAR(20) NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $columns = $db->query("SHOW COLUMNS FROM users")->getResultArray();
        $hasPasswordColumn = false;
        $hasPasswordHashColumn = false;

        foreach ($columns as $column) {
            if (($column['Field'] ?? '') === 'password') {
                $hasPasswordColumn = true;
            }
            if (($column['Field'] ?? '') === 'password_hash') {
                $hasPasswordHashColumn = true;
            }
        }

        if (!$hasPasswordColumn && $hasPasswordHashColumn) {
            $db->query('ALTER TABLE users ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT "" AFTER phone');
            $db->query('UPDATE users SET password = password_hash WHERE password = "" OR password IS NULL');
        }
    }
}
