<?php
$pageTitle = 'Buy Credits';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?> | ArtBid</title>
    <?= view('templates/assets') ?>
</head>
<body>
    <?= view('templates/header', ['page' => 'buy-credits']) ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="panel-card p-4 p-md-5">
                    <div class="mb-4">
                        <span class="detail-heading-tag">Buy Credits</span>
                        <h1 class="mt-3 mb-2">Top up your ArtBid credits</h1>
                        <p class="text-muted mb-0">Choose a package to continue bidding and uploading artworks.</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="panel-card p-4 h-100 border">
                                <h3>Starter</h3>
                                <div class="display-6 fw-bold mb-3">₹300</div>
                                <p class="text-muted mb-3">300 Credits</p>
                                <button type="button" class="primary-btn full-width buy-credits-btn" data-package="300">Buy Now</button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="panel-card p-4 h-100 border primary-bg-soft">
                                <h3>Pro</h3>
                                <div class="display-6 fw-bold mb-3">₹1,200</div>
                                <p class="text-muted mb-3">1,200 Credits</p>
                                <button type="button" class="primary-btn full-width buy-credits-btn" data-package="1200">Buy Now</button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="panel-card p-4 h-100 border">
                                <h3>Premium</h3>
                                <div class="display-6 fw-bold mb-3">₹3,000</div>
                                <p class="text-muted mb-3">3,000 Credits</p>
                                <button type="button" class="primary-btn full-width buy-credits-btn" data-package="3000">Buy Now</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.buy-credits-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    const packageValue = Number(this.dataset.package || 0);

                    fetch('<?= base_url('credits/purchase') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new URLSearchParams({ package: String(packageValue) })
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

                        if (result.success) {
                            if (window.showNotify) {
                                window.showNotify(result.message || 'Credits purchased successfully.', 'success');
                            }
                            setTimeout(() => {
                                window.location.href = '<?= base_url('/') ?>';
                            }, 800);
                            return;
                        }

                        if (window.showNotify) {
                            window.showNotify(result.message || 'Unable to buy credits.', 'error');
                        }
                    })
                    .catch(() => {
                        if (window.showNotify) {
                            window.showNotify('Unable to buy credits. Please try again.', 'error');
                        }
                    });
                });
            });
        });
    </script>
    <script src="<?= base_url('public/assets/js/modal.js') ?>"></script>
</body>
</html>
