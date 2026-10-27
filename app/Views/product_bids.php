<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Bids | ArtBid</title>
    <?= view('templates/assets') ?>
</head>

<body>
    <?= view('templates/header', ['page' => 'my-products']) ?>

    <main class="page-shell">
        <section class="container py-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 product-table-card">
                <div class="section-head simple-head">
                    <div>
                        <p class="mini-label">AUCTION ACTIVITY</p>
                        <h2><?= esc($product['product_name'] ?? 'Product Bids') ?></h2>
                    </div>
                    <a href="<?= site_url('my-products') ?>" class="primary-btn text-light">Back to My Products</a>
                </div>

                <?php
                $currencySymbol = $currency_symbol ?? ''; 
                ?>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light h-100">
                            <div class="text-muted small">Current Bid</div>
                            <div class="fw-bold fs-5 mt-1">
                                <?= esc($currencySymbol) ?> <?= number_format((float) ($product['current_bid_price'] ?? $product['minimum_price'] ?? 0), 0, '.', ',') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light h-100">
                            <div class="text-muted small">Minimum Price</div>
                            <div class="fw-bold fs-5 mt-1">
                                <?= esc($currencySymbol) ?> <?= number_format((float) ($product['minimum_price'] ?? 0), 0, '.', ',') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light h-100">
                            <div class="text-muted small">Total Bids</div>
                            <div class="fw-bold fs-5 mt-1"><?= count($bids) ?></div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive product-table-wrap">
                    <table class="table table-striped products-table align-middle mb-0" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Bidder Name</th>
                                <th>Bid Amount</th>
                                <th>Bid Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bids)): ?>
                                <?php foreach ($bids as $index => $bid): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= esc($bid['bidder_name'] ?? 'Unknown User') ?></td>
                                        <td><?= esc($currencySymbol) ?> <?= number_format((float) ($bid['bidprice'] ?? 0), 0, '.', ',') ?></td>
                                        <td><?= esc(date('d M Y, H:i', strtotime($bid['createdat']))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No bids placed yet for this product.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('public/assets/js/modal.js') ?>"></script>
</body>

</html>
