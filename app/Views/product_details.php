<?php
$product = $product ?? [];
$images = $product['images'] ?? [];
$mainImage = !empty($images)
    ? base_url(str_replace('\\', '/', $images[0]))
    : 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?auto=format&fit=crop&w=1200&q=80';
$currencyCode = strtoupper($product['currency'] ?? 'INR');
$currencySymbol = ['INR' => '₹', 'USD' => '$'][$currencyCode] ?? $currencyCode;
$sellerName = ucwords(strtolower((string) ($product['uploader_name'] ?? 'Unknown Seller')));
$artistAvatar = !empty($product['user_profile_image'] ?? '')
    ? base_url(str_replace('\\', '/', $product['user_profile_image']))
    : 'https://ui-avatars.com/api/?name=' . urlencode($sellerName) . '&background=F5F5F5&color=1f2937&size=200';
$artistArtworkCount = 0;
if (!empty($product['user_id'])) {
    $db = db_connect();
    if ($db->tableExists('products')) {
        $artistArtworkCount = (int) $db->table('products')
            ->where('user_id', (int) $product['user_id'])
            ->countAllResults();
    }
}
$minimumPriceValue = (float) ($product['minimum_price'] ?? 0);
$currentBidPriceValue = (float) ($product['current_bid_price'] ?? $minimumPriceValue);
$bidRaiseValue = (float) ($product['bid_raise_price'] ?? 0);
$defaultBidValue = $currentBidPriceValue + $bidRaiseValue;
$minimumPrice = number_format($minimumPriceValue, 0, '.', ',');
$currentBidPrice = number_format($currentBidPriceValue, 0, '.', ',');
// echo $currentBidPrice;exit;

$bidRaise = number_format($bidRaiseValue, 0, '.', ',');
$defaultBidDisplay = number_format($defaultBidValue, 0, '.', ',');
$endDate = !empty($product['bidding_end_datetime']) ? date('d M Y, H:i', strtotime($product['bidding_end_datetime'])) : 'Not set';
$startDate = !empty($product['bidding_start_datetime']) ? date('d M Y, H:i', strtotime($product['bidding_start_datetime'])) : 'Not set';
$currentDateTime = date('Y-m-d H:i:s');
$now = new DateTime();
$auctionStart = !empty($product['bidding_start_datetime']) ? new DateTime($product['bidding_start_datetime']) : null;
$auctionEnd = !empty($product['bidding_end_datetime']) ? new DateTime($product['bidding_end_datetime']) : null;
//  echo $minimum_credits;exit;
if ($auctionStart && $auctionEnd) {
    if ($now < $auctionStart) {
        $auctionStatus = 'Upcoming';
    } elseif ($now > $auctionEnd) {
        $auctionStatus = 'Ended';
    } else {
        $auctionStatus = 'Live';
    }
} else {
    $auctionStatus = 'Live';
}

$auctionEnded = $auctionEnd && $now > $auctionEnd;
$currentUserCredits = 0;
if (!empty(session('logged_in_user')['id'])) {
    $userId = (int) session('logged_in_user')['id'];
    $db = db_connect();
    if ($db && $db->tableExists('credits')) {
        $creditRow = $db->table('credits')->where('userid', $userId)->get()->getRowArray();
        $currentUserCredits = (int) ($creditRow['credit'] ?? 0);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($product['product_name'] ?? 'Artwork') ?> | ArtBid</title>
    <?= view('templates/assets') ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="detail-page-body" data-user-logged-in="<?= !empty(session('logged_in_user')) ? '1' : '0' ?>" data-user-credits="<?= esc((string) $currentUserCredits) ?>">
    <?= view('templates/header', ['page' => 'marketplace']) ?>

    <main class="detail-page-shell">
        <section class="container detail-page-container">
            <div class="detail-gallery-panel panel-card">
                <div class="detail-main-image-wrap">
                    <img src="<?= esc($mainImage) ?>" alt="<?= esc($product['product_name'] ?? 'Artwork') ?>" class="detail-main-image">
                    <button type="button" class="detail-arrow detail-arrow-left pb-1" aria-label="Previous image">&#8249;</button>
                    <button type="button" class="detail-arrow detail-arrow-right pb-1" aria-label="Next image">&#8250;</button>
                </div>

                <div class="detail-thumb-row">
                    <?php if (!empty($images)): ?>
                        <?php foreach (array_slice($images, 0, 4) as $index => $image): ?>
                            <?php $thumb = base_url(str_replace('\\', '/', $image)); ?>
                            <div class="detail-thumb-item <?= $index === 0 ? 'active' : '' ?>" data-image="<?= esc($thumb) ?>">
                                <img src="<?= esc($thumb) ?>" alt="Thumbnail" class="detail-thumb-image">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="detail-thumb-item active" data-image="<?= esc($mainImage) ?>">
                            <img src="<?= esc($mainImage) ?>" alt="Thumbnail" class="detail-thumb-image">
                        </div>
                        <div class="detail-thumb-item" data-image="https://images.unsplash.com/photo-1515405295579-ba7b45403062?auto=format&fit=crop&w=800&q=80">
                            <img src="https://images.unsplash.com/photo-1515405295579-ba7b45403062?auto=format&fit=crop&w=800&q=80" alt="Thumbnail" class="detail-thumb-image">
                        </div>
                        <div class="detail-thumb-item" data-image="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=800&q=80">
                            <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=800&q=80" alt="Thumbnail" class="detail-thumb-image">
                        </div>
                        <div class="detail-thumb-item" data-image="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80">
                            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80" alt="Thumbnail" class="detail-thumb-image">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="detail-side-panel panel-card">
                <div class="detail-header-block">
                    <span class="detail-heading-tag"><?= esc($auctionStatus).' Auction' ?></span>
                    <h1><?= esc($product['product_name'] ?? 'Untitled Artwork') ?></h1>
                </div>

                <div class="detail-seller-line">By <?= esc($sellerName) ?></div>

                <div class="detail-bid-card">
                    <div class="detail-bid-row">
                        <div class="detail-stat-box">
                            <span class="detail-stat-label">Current Bid</span>
                            <strong><?= esc($currencySymbol) ?> <?= esc($currentBidPrice) ?></strong>
                        </div>
                        <?php if ($auctionStatus === 'Upcoming'): ?>
                            <div class="detail-stat-box accent-box">
                                <span class="detail-stat-label">Auction Starts In</span>
                                <strong id="auction-countdown" class="detail-countdown" data-end-time="<?= esc($product['bidding_start_datetime'] ?? '') ?>">--</strong>
                            </div>
                        <?php elseif ($auctionStatus !== 'Ended'): ?>
                            <div class="detail-stat-box accent-box">
                                <span class="detail-stat-label">Auction Ends In</span>
                                <strong id="auction-countdown" class="detail-countdown" data-end-time="<?= esc($product['bidding_end_datetime'] ?? '') ?>">--</strong>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($auctionStatus === 'Live'): ?>
                        <div class="detail-bid-form">
                            <div class="detail-bid-stepper">
                                <button type="button" class="detail-bid-step detail-bid-decrease" data-step="-1" aria-label="Decrease bid">Decrease Bid</button>
                                <button type="button" class="detail-bid-step detail-bid-increase" data-step="1" aria-label="Increase bid">Increase Bid</button>
                            </div>
                            <div class="detail-bid-total">Bid For: <strong><?= esc($currencySymbol) ?> <span id="bid-total-value"><?= esc($defaultBidDisplay) ?></span></strong></div>
                            <button type="button" class="primary-btn detail-submit-btn">Place Bid</button>
                            <small>Minimum Bid: <?= esc($currencySymbol) ?> <?= esc($bidRaise) ?></small>
                        </div>
                    <?php elseif ($auctionStatus === 'Upcoming'): ?>
                        <div class="detail-bid-form">
                            <small>This auction has not started yet. It will go live on <?= esc($startDate) ?>.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>
        </section>

        <section class="container detail-lower-grid">
            <?php if (!empty(trim((string) ($product['description'] ?? '')))): ?>
                <div class="detail-content-card panel-card">
                    <h3>About the Artwork</h3>
                    <p><?= nl2br(esc($product['description'])) ?></p>
                </div>
            <?php endif; ?>

            <div class="detail-content-card panel-card">
                <h3>Artist Information</h3>
                <div class="artist-card-row">
                    <div class="artist-avatar-wrap">
                        <img src="<?= esc($artistAvatar) ?>" alt="<?= esc($sellerName) ?>" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=User&background=F5F5F5&color=1f2937&size=200';">
                    </div>
                    <div class="artist-copy">
                        <h4><?= esc($sellerName) ?></h4>
                    </div>
                </div>
                <div class="artist-art-count">Total Artworks: <?= esc((string) $artistArtworkCount) ?> </div>
                <!-- <p><?= esc($sellerName) ?> is a contemporary artist known for her vivid storytelling through abstract forms and urban landscapes. Her paintings explore light, memory, and movement with a strong sense of atmosphere.</p> -->
                <a href="<?= base_url('marketplace?q=' . urlencode($sellerName)) ?>" class="mt-1 detail-artist-btn">View More Artworks From this Artist</a>
            </div>
        </section>

        <section class="container similar-section">
            <div class="section-head similar-head">
                <h2>Similar Artworks</h2>
                <a href="<?= base_url('marketplace?q=' . urlencode($sellerName)) ?>">View All</a>
            </div>

            <?php if (!empty($similarProducts)): ?>
                <div class="similar-slider-shell">
                    <button type="button" class="similar-slider-btn similar-slider-prev" aria-label="Previous artwork">&#8249;</button>
                    <div class="similar-slider-wrap" id="similar-slider-wrap">
                        <?php foreach ($similarProducts as $similarProduct): ?>
                            <div class="m-2">
                                <?= view('partials/product_card', ['product' => $similarProduct]) ?>
                            </div>

                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="similar-slider-btn similar-slider-next" aria-label="Next artwork">&#8250;</button>
                </div>
            <?php else: ?>
                <div class="similar-empty">No other artworks from this artist yet.</div>
            <?php endif; ?>
        </section>
    </main>

    <?= view('templates/login_modal') ?>
    <?= view('templates/register_modal') ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="<?= base_url('public/assets/js/modal.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mainImage = document.querySelector('.detail-main-image');
            const thumbs = [...document.querySelectorAll('.detail-thumb-item')];
            const prevBtn = document.querySelector('.detail-arrow-left');
            const nextBtn = document.querySelector('.detail-arrow-right');
            const bidTotalValue = document.getElementById('bid-total-value');
            const currentBidLabel = document.querySelector('.detail-stat-box strong');
            const countdownEl = document.getElementById('auction-countdown');
            const productId = Number(<?php echo json_encode((int) ($product['id'] ?? 0)); ?>);
            const bidStep = Number(<?php echo json_encode((float) ($product['bid_raise_price'] ?? 0)); ?>);
            const minimumBid = Number(<?php echo json_encode((float) ($product['minimum_price'] ?? 0)); ?>);
            const requiredBidCredits = '<?= $minimum_credits; ?>';
            const currentBidValue = Number(<?php echo json_encode((float) ($product['current_bid_price'] ?? $product['minimum_price'] ?? 0)); ?>);
            const startingBid = bidStep > 0 ? currentBidValue + bidStep : currentBidValue;
            const userCredits = Number(document.body.dataset.userCredits || 0);
            let previewBid = startingBid;
            const formatAmount = (value) => new Intl.NumberFormat('en-IN', {
                maximumFractionDigits: 0
            }).format(Number(value || 0));

            const updateCountdown = () => {
                if (!countdownEl) {
                    return;
                }

                const endTime = countdownEl.dataset.endTime;
                if (!endTime) {
                    countdownEl.textContent = 'Not set';
                    return;
                }

                const endDate = new Date(endTime.replace(' ', 'T'));
                const now = new Date();
                const diffMs = endDate.getTime() - now.getTime();

                if (diffMs <= 0) {
                    countdownEl.textContent = 'Ended';
                    const bidForm = document.querySelector('.detail-bid-form');
                    if (bidForm) {
                        bidForm.remove();
                    }
                    return;
                }

                const totalSeconds = Math.floor(diffMs / 1000);
                const days = Math.floor(totalSeconds / 86400);
                const hours = Math.floor((totalSeconds % 86400) / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;

                countdownEl.textContent = `${days}d ${String(hours).padStart(2, '0')}h ${String(minutes).padStart(2, '0')}m ${String(seconds).padStart(2, '0')}s`;
            };

            updateCountdown();
            setInterval(updateCountdown, 1000);

            const similarSliderWrap = document.getElementById('similar-slider-wrap');
            if (similarSliderWrap && window.jQuery && $.fn.slick) {
                const $slider = $(similarSliderWrap);
                if (!$slider.hasClass('slick-initialized')) {
                    $slider.slick({
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        infinite: false,
                        arrows: true,
                        prevArrow: '.similar-slider-prev',
                        nextArrow: '.similar-slider-next',
                        speed: 300,
                        dots: false,
                        draggable: true,
                        focusOnSelect: false,
                        adaptiveHeight: false,
                        responsive: [{
                                breakpoint: 1200,
                                settings: {
                                    slidesToShow: 3
                                }
                            },
                            {
                                breakpoint: 900,
                                settings: {
                                    slidesToShow: 2
                                }
                            },
                            {
                                breakpoint: 560,
                                settings: {
                                    slidesToShow: 1
                                }
                            }
                        ]
                    });
                }
            }

            const updatePreviewDisplay = (value) => {
                const safeValue = Number(value || startingBid);
                previewBid = safeValue;

                if (bidTotalValue) {
                    bidTotalValue.textContent = formatAmount(safeValue);
                }
            };

            const updateCurrentBidLabel = (value) => {
                if (currentBidLabel) {
                    currentBidLabel.textContent = '₹ ' + formatAmount(value || 0);
                }
            };

            const showInsufficientCreditsPopup = () => {
                if (typeof Swal === 'undefined') {
                    if (window.showNotify) {
                        window.showNotify('You need ' + requiredBidCredits + ' credits to place a bid.', 'error');
                    }
                    return;
                }

                Swal.fire({
                    title: 'Not enough credits',
                    text: 'You need ' + requiredBidCredits + ' credits to place a bid.',
                    icon: 'warning',
                    showCancelButton: false,
                    showCloseButton: false,
                    confirmButtonText: 'Buy Credits',
                    confirmButtonColor: '#111111',
                    width: 420,
                    customClass: {
                        confirmButton: 'swal-buy-credits-btn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open('<?= base_url('buy-credits') ?>', '_blank');
                    }
                });
            };

            const increaseBtn = document.querySelector('.detail-bid-increase');
            const decreaseBtn = document.querySelector('.detail-bid-decrease');
            const placeBidBtn = document.querySelector('.detail-submit-btn');

            increaseBtn?.addEventListener('click', function() {
                if (!bidStep) {
                    return;
                }

                updatePreviewDisplay(previewBid + bidStep);
            });

            decreaseBtn?.addEventListener('click', function() {
                if (!bidStep) {
                    return;
                }

                updatePreviewDisplay(Math.max(startingBid, previewBid - bidStep));
            });

            placeBidBtn?.addEventListener('click', function() {
                if (!productId) {
                    return;
                }

                const isLoggedIn = document.body.dataset.userLoggedIn === '1';
                if (!isLoggedIn) {
                    const loginModal = document.getElementById('loginModal');
                    if (loginModal) {
                        loginModal.classList.add('is-open');
                        loginModal.setAttribute('aria-hidden', 'false');
                    }
                    if (window.showNotify) {
                        window.showNotify('Please login to place a bid.', 'error');
                    }
                    return;
                }

                if (userCredits <= requiredBidCredits) {
                    showInsufficientCreditsPopup();
                    return;
                }

                fetch('<?= base_url('product/place-bid') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            bid_value: previewBid
                        })
                    })
                    .then(async (response) => {
                        const text = await response.text();
                        let result = null;

                        if (text) {
                            try {
                                result = JSON.parse(text);
                            } catch (error) {
                                result = null;
                            }
                        }

                        if (!result) {
                            throw new Error('Invalid response');
                        }

                        if (result.success && typeof result.new_value !== 'undefined') {
                            previewBid = Number(result.new_value || minimumBid);
                            updateCurrentBidLabel(previewBid);
                            updatePreviewDisplay(previewBid);
                            if (window.showNotify) {
                                window.showNotify(result.message || 'Bid placed successfully.', 'success');
                            }
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                            return;
                        }

                        if (result.redirect || result.message && result.message.toLowerCase().includes('credit')) {
                            showInsufficientCreditsPopup();
                            if (result.redirect) {
                                setTimeout(() => {
                                    window.location.href = result.redirect;
                                }, 1200);
                            }
                            return;
                        }

                        if (window.showNotify) {
                            window.showNotify(result.message || 'Unable to place bid.', 'error');
                        }
                    })
                    .catch(() => {
                        updateCurrentBidLabel(minimumBid);
                        updatePreviewDisplay(minimumBid);
                        if (window.showNotify) {
                            window.showNotify('Unable to place bid. Please try again.', 'error');
                        }
                    });
            });

            if (bidTotalValue) {
                bidTotalValue.textContent = formatAmount(startingBid);
            }

            if (currentBidLabel) {
                currentBidLabel.textContent = '₹ ' + formatAmount(currentBidValue);
            }

            if (!mainImage || !thumbs.length || !prevBtn || !nextBtn) {
                return;
            }

            const imageList = thumbs.map((thumb) => thumb.dataset.image || thumb.querySelector('img')?.src).filter(Boolean);
            let currentIndex = 0;

            const updateGallery = (index) => {
                if (!imageList.length) {
                    return;
                }

                currentIndex = (index + imageList.length) % imageList.length;
                mainImage.src = imageList[currentIndex];

                thumbs.forEach((thumb, idx) => {
                    const isActive = idx === currentIndex;
                    thumb.classList.toggle('active', isActive);
                });
            };

            thumbs.forEach((thumb, index) => {
                thumb.addEventListener('click', function() {
                    updateGallery(index);
                });
            });

            prevBtn.addEventListener('click', function() {
                updateGallery(currentIndex - 1);
            });

            nextBtn.addEventListener('click', function() {
                updateGallery(currentIndex + 1);
            });
        });
    </script>
</body>

</html>