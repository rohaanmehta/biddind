<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Art auction homepage built in CodeIgniter 4.">
    <title>ArtBid | Discover Unique Art</title>
    <?= view('templates/assets') ?>
</head>
<body>
    <?= view('templates/header', ['page' => 'home']) ?>

    <main class="page-shell">
        <section class="hero container">
            <div class="hero-copy">
                <p class="mini-label">ONLINE PAINTING AUCTION</p>
                <h1>Discover Unique Art.<br>Bid on Your Favorites.</h1>
                <p class="lead">
                    Explore a world of original paintings from talented artists around the globe.
                    Place bids, track your favorites, and own a one-of-a-kind masterpiece.
                </p>

                <div class="hero-actions">
                    <a href="<?= base_url('marketplace') ?>" class="primary-btn">Browse Painting</a>
                    <a href="<?= base_url('how-it-works') ?>" class="secondary-btn">See Works</a>
                </div>
            </div>

            <div class="hero-visual">
                <div class="art-card large-art-card">
                    <div class="art-image hero-art"></div>
                    <div class="art-badge">View In Gallery</div>
                </div>
            </div>
        </section>

        <section class="featured container">
            <div class="section-head">
                <h2>Featured Paintings</h2>
                <a href="<?= base_url('marketplace') ?>">View All</a>
            </div>

            <div class="cards-grid">
                <?php if (!empty($featuredProducts)): ?>
                    <?php foreach (array_slice($featuredProducts, 0, 12) as $product): ?>
                        <?= view('partials/product_card', ['product' => $product]) ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-4 text-muted">No featured products available.</div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-wrap">
            <div class="footer-brand">
                <div class="brand">
                    <span class="brand-mark">A</span>
                    <span class="brand-name">ArtBid</span>
                </div>
                <p>Discover the finest works of art from contemporary creators.</p>
            </div>

            <div class="footer-links">
                <div>
                    <h4>Explore</h4>
                    <a href="<?= base_url('marketplace') ?>">Browse Art</a>
                    <a href="#">Artists</a>
                    <a href="#">Auctions</a>
                </div>

                <div>
                    <h4>Company</h4>
                    <a href="<?= base_url('how-it-works') ?>">About</a>
                    <a href="#">Press</a>
                    <a href="#">Support</a>
                </div>

                <div>
                    <h4>Follow</h4>
                    <a href="#">Instagram</a>
                    <a href="#">Twitter</a>
                    <a href="#">Pinterest</a>
                </div>
            </div>
        </div>

        <div class="container copyright">
            <span>© 2026 ArtBid. All rights reserved.</span>
        </div>
    </footer>

    <?= view('templates/login_modal') ?>
    <?= view('templates/register_modal') ?>

    <script src="<?= base_url('public/assets/js/modal.js') ?>"></script>
</body>
</html>

