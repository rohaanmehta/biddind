<?php
    $currentPage = $page ?? 'home';
    $loggedInUser = session('logged_in_user');
    $creditBalance = 0;

    if (!empty($loggedInUser['id'])) {
        $db = db_connect();
        $creditRow = $db->table('credits')->where('userid', $loggedInUser['id'])->get()->getRowArray();
        $creditBalance = (int) ($creditRow['credit'] ?? 0);
    }
?>
<header class="topbar">
    <div class="container nav-wrap">
        <a class="brand" href="<?= site_url('/') ?>" aria-label="Home">
            <span class="brand-mark">A</span>
            <span class="brand-name">ArtBid</span>
        </a>

        <nav class="main-nav" aria-label="Main navigation">
            <a href="<?= base_url('/') ?>" class="<?= $currentPage === 'home' ? 'active' : '' ?>">Home</a>
            <a href="<?= base_url('marketplace') ?>" class="<?= $currentPage === 'marketplace' ? 'active' : '' ?>">Marketplace</a>
            <a href="<?= base_url('how-it-works') ?>" class="<?= $currentPage === 'how-it-works' ? 'active' : '' ?>">How It Works</a>
            <a href="<?= base_url('buy-credits') ?>" class="<?= $currentPage === 'buy-credits' ? 'active' : '' ?>">Buy Credits</a>
        </nav>

        <div class="nav-actions">
            <?php if ($loggedInUser): ?>
                <div class="profile-menu-wrap">
                    <button type="button" class="profile-trigger" aria-label="Open profile menu" aria-expanded="false">
                        <span class="profile-avatar"><?= strtoupper(substr(esc($loggedInUser['name']), 0, 1)) ?></span>
                    </button>

                    <div class="profile-dropdown" role="menu" aria-label="Profile menu">
                        <a href="<?= site_url('edit-profile') ?>" class="profile-item">Edit Profile</a>
                        <a href="<?= site_url('upload-products') ?>" class="profile-item">Upload Products</a>
                        <a href="<?= site_url('my-products') ?>" class="profile-item">My Products</a>
                        <a href="<?= site_url('my-bids') ?>" class="profile-item">My Bids</a>
                        <a href="<?= site_url('buy-credits') ?>" class="profile-item profile-item-credit">
                            <span>Buy Credits</span>
                            <span class="credit-badge"><?= number_format($creditBalance) ?> credits</span>
                        </a>
                        <hr class="profile-divider">
                        <a href="<?= site_url('auth/logout') ?>" class="profile-item danger">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <button type="button" class="sell-btn" data-open-modal="loginModal">Login</button>
                <button type="button" class="primary-btn" data-open-modal="registerModal">Register</button>
            <?php endif; ?>
        </div>
    </div>
</header>
