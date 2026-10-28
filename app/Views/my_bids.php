<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bids | <?= esc(env('APP_NAME', 'Art Bidster')) ?></title>
    <?= view('templates/assets') ?>
</head>

<body>
    <?= view('templates/header', ['page' => 'my-bids']) ?>

    <main class="page-shell">
        <section class="container py-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 product-table-card">
                <div class="section-head simple-head">
                    <div>
                        <p class="mini-label">MY ACTIVITY</p>
                        <h2>My Bids</h2>
                    </div>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <div class="table-responsive product-table-wrap">
                    <table class="table table-striped products-table align-middle mb-0" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Min Price</th>
                                <th>Your Bid</th>
                                <th>Auction Status</th>
                                <th>Bid Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bids)): ?>
                                <?php foreach ($bids as $bid): ?>
                                    <?php
                                    $currencyCode = strtoupper($bid['currency'] ?? 'INR');
                                    $currencySymbols = [
                                        'INR' => '₹',
                                        'USD' => '$',
                                    ];
                                    $currencySymbol = $currencySymbols[$currencyCode] ?? $currencyCode;

                                    $auctionStatus = 'Live';
                                    if (!empty($bid['bidding_end_datetime'])) {
                                        $endDate = new DateTime($bid['bidding_end_datetime']);
                                        $now = new DateTime();
                                        if ($now > $endDate) {
                                            $auctionStatus = 'Ended';
                                        }
                                    }

                                    $bidStatus = 'Not Declared';
                                    if ($auctionStatus === 'Ended') {
                                        $currentBidOwner = (int) ($bid['current_bid_owner'] ?? 0);
                                        $loggedInUserId = (int) (session('logged_in_user')['id'] ?? 0);
                                        $currentBidValue = (float) ($bid['current_bid_price'] ?? 0);
                                        $rowBidValue = (float) ($bid['bidprice'] ?? 0);

                                        if ($currentBidOwner === $loggedInUserId && $rowBidValue === $currentBidValue) {
                                            $bidStatus = 'Bid Won';
                                        } else {
                                            $bidStatus = 'Bid Lost';
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="product-name"><?= esc($bid['product_name']) ?></div>
                                        </td>
                                        <td><?= esc($bid['category']) ?></td>
                                        <td><?= esc($currencySymbol) ?><?= number_format((float) ($bid['minimum_price'] ?? 0), 0, '.', ',') ?></td>
                                        <td><?= esc($currencySymbol) ?><?= number_format((float) ($bid['bidprice'] ?? 0), 0, '.', ',') ?></td>
                                        <td>
                                            <span style="font-size: 13px;padding: 7px 15px 7px 15px;" class="badge <?= $auctionStatus === 'Ended' ? 'bg-secondary' : 'bg-success' ?> rounded-pill">
                                                <?= esc($auctionStatus) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-size: 13px;padding: 7px 15px 7px 15px;" class="badge <?= $bidStatus === 'Bid Won' ? 'bg-success' : ($bidStatus === 'Bid Lost' ? 'bg-danger' : 'bg-warning text-dark') ?> rounded-pill">
                                                <?= esc($bidStatus) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="product-table-actions">
                                                <a href="<?= site_url('product/' . ($bid['product_id'] ?? $bid['product_id_value'] ?? $bid['id'])) ?>" class="btn btn-sm btn-outline-dark">View Product</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No bids placed yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

</body>

</html>