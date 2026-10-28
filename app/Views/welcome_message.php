<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Discover original artworks, explore live painting auctions, and place secure bids on <?= esc(env('APP_NAME', 'Art Bidster')) ?>.">
    <title><?= esc(env('APP_NAME', 'Art Bidster')) ?> | Discover and Bid on Unique Art</title>
    <?= view('templates/assets', [
        'metaDescription' => 'Discover original artworks, explore live painting auctions, and place secure bids on ' . env('APP_NAME', 'Art Bidster') . '.',
        'ogTitle' => env('APP_NAME', 'Art Bidster') . ' | Discover and Bid on Unique Art',
        'canonicalUrl' => base_url(),
        'ogType' => 'website'
    ]) ?>
    <script type="application/ld+json">
    <?= json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => base_url('#organization'),
                'name' => env('APP_NAME', 'Art Bidster'),
                'url' => base_url(),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => base_url('public/img/logo.png')
                ]
            ],
            [
                '@type' => 'WebSite',
                '@id' => base_url('#website'),
                'url' => base_url(),
                'name' => env('APP_NAME', 'Art Bidster'),
                'publisher' => ['@id' => base_url('#organization')],
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => base_url('marketplace?q={search_term_string}'),
                    'query-input' => 'required name=search_term_string'
                ]
            ]
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
    </script>
</head>

<body>
    <?= view('templates/header', ['page' => 'home']) ?>

    <main class="page-shell">
        <section class="hero container">
            <div class="hero-copy">
                <!-- <p class="mini-label">ONLINE PAINTING AUCTION</p> -->
                <h1>Discover Art That Speaks.<br>Bid on What You Love.</h1>
                <p class="lead">
                    Explore original paintings from talented artists and discover art that matches your style. Place your bids, compete for your favorites, and make a masterpiece yours.
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

        <section class="seller-auction-section">
            <div class="container seller-auction-inner">
                <div class="seller-auction-copy">
                    <p class="mini-label">FOR ARTISTS &amp; SELLERS</p>
                    <h2>Your art deserves more than a fixed price.</h2>
                    <p>Upload your product and let interested buyers place bids. Create healthy competition for your artwork and discover the best price it can achieve.</p>
                    <h3>Why take the lowest?</h3>
                    <a href="<?= base_url('upload-products') ?>" class="primary-btn seller-auction-btn">Upload Your Product <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>

                <div class="seller-auction-benefits" aria-label="Auction benefits">
                    <div class="seller-benefit">
                        <span class="seller-benefit-number">01</span>
                        <div>
                            <h4>Set your starting price</h4>
                            <p>Choose a minimum price that reflects the value of your artwork.</p>
                        </div>
                    </div>
                    <div class="seller-benefit">
                        <span class="seller-benefit-number">02</span>
                        <div>
                            <h4>Let demand decide</h4>
                            <p>Collectors compete openly and bids move in real time.</p>
                        </div>
                    </div>
                    <div class="seller-benefit">
                        <span class="seller-benefit-number">03</span>
                        <div>
                            <h4>Reach your best price</h4>
                            <p>Close the auction with more confidence and stronger value.</p>
                        </div>
                    </div>
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
                    <!-- <span class="brand-mark">A</span> -->
                    <span class="brand-name text-light"><?= esc(env('APP_NAME', 'Art Bidster')) ?></span>
                </div>
                <p>Discover unique artwork, place your bids, and bring home masterpieces you love.
                    Where creativity meets competition, every bid tells a story.</p>
            </div>

            <div class="footer-links">
                <div>
                    <h4>Explore</h4>
                    <a href="<?= base_url('marketplace') ?>">Browse Art</a>
                    <a href="<?= base_url('buy-credits') ?>">Buy Credits</a>
                    <!-- <a href="#">Auctions</a> -->
                </div>

                <div>
                    <h4>Company</h4>
                    <a href="<?= base_url('how-it-works') ?>">How It Works</a>
                    <!-- <a href="#">Press</a> -->
                    <a href="#">Support</a>
                </div>

                <div>
                    <h4>Follow</h4>
                    <a href="#">Instagram</a>
                    <a href="#">Twitter</a>
                    <!-- <a href="#">Pinterest</a> -->
                </div>
            </div>
        </div>

        <div class="container copyright">
            <span>© <?= date('Y'); ?> <?= esc(env('APP_NAME', 'Art Bidster')) ?>. All rights reserved.</span>
        </div>
    </footer>

</body>

</html>