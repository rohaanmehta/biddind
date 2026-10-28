<?php
$includeFlatpickr = $include_flatpickr ?? false;
$appName = env('APP_NAME', 'Art Bidster');
$metaDescription = trim((string) ($metaDescription ?? 'Discover and bid on unique artwork at ' . $appName . '. Browse live art auctions and place your bids online.'));
$metaRobots = trim((string) ($metaRobots ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1'));
$canonicalUrl = trim((string) ($canonicalUrl ?? current_url()));
$ogTitle = trim((string) ($ogTitle ?? $appName));
$ogImage = trim((string) ($ogImage ?? base_url('public/img/logo.png')));
$ogType = trim((string) ($ogType ?? 'website'));

$currentPath = trim(service('uri')->getPath(), '/');
$privatePathPatterns = [
    'upload-products', 'my-products', 'my-bids', 'transactions', 'buy-credits',
    'edit-profile', 'payment-successful', 'payment-failed', 'auth', 'product/edit', 'product/bids'
];
foreach ($privatePathPatterns as $privatePath) {
    if ($currentPath === $privatePath || str_starts_with($currentPath, $privatePath . '/')) {
        $metaRobots = 'noindex, nofollow, noarchive';
        break;
    }
}
if (str_contains($currentPath, 'marketplace') && service('request')->getGet('q') !== null) {
    $metaRobots = 'noindex, follow';
}
?>
<link rel="canonical" href="<?= esc($canonicalUrl, 'attr') ?>">
<meta name="robots" content="<?= esc($metaRobots, 'attr') ?>">
<meta name="description" content="<?= esc($metaDescription, 'attr') ?>">
<meta property="og:site_name" content="<?= esc($appName, 'attr') ?>">
<meta property="og:title" content="<?= esc($ogTitle, 'attr') ?>">
<meta property="og:description" content="<?= esc($metaDescription, 'attr') ?>">
<meta property="og:type" content="<?= esc($ogType, 'attr') ?>">
<meta property="og:url" content="<?= esc($canonicalUrl, 'attr') ?>">
<meta property="og:image" content="<?= esc($ogImage, 'attr') ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= esc($ogTitle, 'attr') ?>">
<meta name="twitter:description" content="<?= esc($metaDescription, 'attr') ?>">
<meta name="twitter:image" content="<?= esc($ogImage, 'attr') ?>">
<link rel="icon" href="<?= base_url('public/img/logo.png') ?>?v=<?= filemtime(FCPATH . 'public/img/logo.png') ?>" type="image/png" sizes="any">
<link rel="shortcut icon" href="<?= base_url('public/img/logo.png') ?>?v=<?= filemtime(FCPATH . 'public/img/logo.png') ?>" type="image/png">
<link rel="apple-touch-icon" href="<?= base_url('public/img/logo.png') ?>?v=<?= filemtime(FCPATH . 'public/img/logo.png') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php if ($includeFlatpickr): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<?php endif; ?>
<link rel="stylesheet" href="<?= base_url('public/assets/css/homepage.css') ?>?v=<?= filemtime(FCPATH . 'public/assets/css/homepage.css') ?>">
