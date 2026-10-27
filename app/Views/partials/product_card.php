<?php
$product = $product ?? [];
$imageList = json_decode($product['image_urls'] ?? '[]', true);
$firstImage = is_array($imageList) && !empty($imageList)
    ? base_url(str_replace('\\', '/', $imageList[0]))
    : 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?auto=format&fit=crop&w=600&q=80';
$currencyCode = strtoupper($product['currency'] ?? 'INR');
$currencySymbol = ['INR' => '₹', 'USD' => '$'][$currencyCode] ?? $currencyCode;

$db = db_connect();
$totalBids = 0;
if ($db && $db->tableExists('bid_history')) {
    $totalBids = (int) $db->table('bid_history')
        ->where('product_id', (int) ($product['id'] ?? 0))
        ->countAllResults();
}

$now = new DateTime();
$startDate = !empty($product['bidding_start_datetime']) ? new DateTime($product['bidding_start_datetime']) : null;
$endDate = !empty($product['bidding_end_datetime']) ? new DateTime($product['bidding_end_datetime']) : null;
$currentBidValue = (float) ($product['current_bid_price'] ?? $product['minimum_price'] ?? 0);

if ($startDate && $endDate) {
    if ($now < $startDate) {
        $statusLabel = 'Upcoming';
    } elseif ($now > $endDate) {
        $statusLabel = 'Ended';
    } else {
        $statusLabel = 'Live';
    }
} else {
    $statusLabel = 'Live';
}
?>
<article class="art-card painting-card" onclick="window.location.href='<?= base_url('product/' . (int) ($product['id'] ?? 0)) ?>'" style="cursor: pointer;">
    <img src="<?= esc($firstImage) ?>" alt="<?= esc($product['product_name']) ?>" class="art-image" style="width: 100%; height: 260px; object-fit: cover; display: block;">
    <div class="card-body">
        <div class="card-title-wrap">
            <div>
                <h3><?= esc($product['product_name']) ?></h3>
                <p>By <?= esc(ucwords(strtolower((string) ($product['uploader_name'] ?? 'Unknown Seller')))) ?></p>
            </div>
            <span class="card-tag"><?= esc($statusLabel) ?></span>
        </div>
        <div class="card-meta">
            <span class="price"><?= esc($currencySymbol) ?><?= number_format($currentBidValue, 0, '.', ',') ?></span>
            <?php if ($totalBids > 0): ?>
                <span class="bids"><?= $totalBids ?> bid<?= $totalBids === 1 ? '' : 's' ?></span>
            <?php endif; ?>
        </div>
        <div class="card-actions">
            <button class="primary-btn small" type="button" onclick="event.stopPropagation(); window.location.href='<?= base_url('product/' . (int) ($product['id'] ?? 0)) ?>'">Bid Now</button>
        </div>
    </div>
</article>
