<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Marketplace | Live Painting Auctions | <?= esc(env('APP_NAME', 'Art Bidster')) ?></title>
    <?= view('templates/assets', [
        'metaDescription' => 'Browse original paintings and live art auctions. Search artworks by category or artist and place your bid on ' . env('APP_NAME', 'Art Bidster') . '.',
        'ogTitle' => 'Art Marketplace | Live Painting Auctions | ' . env('APP_NAME', 'Art Bidster'),
        'canonicalUrl' => base_url('marketplace'),
        'ogType' => 'website'
    ]) ?>
</head>
<body>
    <?= view('templates/header', ['page' => 'marketplace']) ?>

    <main class="page-shell">
        <section class="container marketplace-top">
            <form class="search-wrap" method="get" action="<?= base_url('marketplace') ?>">
                <input type="search" name="q" value="<?= esc($searchTerm ?? '') ?>" placeholder="Search paintings or artists" aria-label="Search marketplace">
                <button type="submit" class="search-icon-btn" style="background:black" aria-label="Search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </section>

        <section class="container">
            <div class="section-head">
                <h2>Marketplace</h2>
                <!-- <a href="#">View All</a> -->
            </div>

            <div class="cards-grid d-none">
                <article class="art-card painting-card">
                    <div class="art-image sunset-art"></div>
                    <div class="card-body">
                        <div class="card-title-wrap">
                            <div>
                                <h3>Sunset Valley</h3>
                                <p>By Emma Brooks</p>
                            </div>
                            <span class="card-tag">Live</span>
                        </div>
                        <div class="card-meta">
                            <span class="price">$1,890</span>
                            <span class="bids">12 bids</span>
                        </div>
                        <div class="card-actions">
                            <button class="primary-btn small">Bid Now</button>
                        </div>
                    </div>
                </article>
            </div>

            <?php if (!empty($products)): ?>
                <div class="cards-grid" id="marketplace-grid">
                    <?php foreach ($products as $product): ?>
                        <?= view('partials/product_card', ['product' => $product]) ?>
                    <?php endforeach; ?>
                </div>

                <div class="text-center mt-4 mb-5">
                    <button type="button" id="load-more-products" class="primary-btn" style="display: <?= ($totalProducts ?? 0) > 52 ? 'inline-block' : 'none' ?>;">Load More</button>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        const totalProducts = <?= (int) ($totalProducts ?? 0) ?>;
        const searchTerm = '<?= addslashes((string) ($searchTerm ?? '')) ?>';
        const loadMoreUrl = '<?= base_url('marketplace/load-more') ?>' + (searchTerm ? '?q=' + encodeURIComponent(searchTerm) : '');
        let currentPage = 1;
        let isLoading = false;
        let hasMore = totalProducts > 52;

        const grid = document.getElementById('marketplace-grid');
        const loadMoreButton = document.getElementById('load-more-products');

        function loadMoreProducts() {
            if (isLoading || !hasMore || !grid || !loadMoreButton) {
                return;
            }

            isLoading = true;
            loadMoreButton.disabled = true;
            loadMoreButton.textContent = 'Loading...';

            const nextPageUrl = loadMoreUrl + (loadMoreUrl.includes('?') ? '&' : '?') + 'page=' + (currentPage + 1);
            fetch(nextPageUrl)
                .then((response) => response.json())
                .then((data) => {
                    if (data.html && grid) {
                        grid.insertAdjacentHTML('beforeend', data.html);
                    }

                    currentPage = Number(data.page || (currentPage + 1));
                    hasMore = !!data.hasMore;

                    if (!hasMore) {
                        loadMoreButton.style.display = 'none';
                    }
                })
                .catch(() => {
                    hasMore = false;
                    loadMoreButton.textContent = 'Unable to load more';
                })
                .finally(() => {
                    isLoading = false;
                    if (hasMore) {
                        loadMoreButton.disabled = false;
                        loadMoreButton.textContent = 'Load More';
                    }
                });
        }

        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', loadMoreProducts);
        }
    </script>
</body>
</html>
