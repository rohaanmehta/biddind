<?php
$pageTitle = 'Buy Credits';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?> | <?= esc(env('APP_NAME', 'Art Bidster')) ?></title>
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
                        <h1 class="mt-3 mb-2">Top up your <?= esc(env('APP_NAME', 'Art Bidster')) ?> credits</h1>
                        <p class="text-muted mb-0">Choose a package to continue bidding and uploading artworks.</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="panel-card p-4 h-100 border">
                                <h3>Starter</h3>
                                <div class="display-6 fw-bold mb-3">₹300</div>
                                <p class="text-muted mb-3">300 Credits</p>
                                <button type="button" class="primary-btn full-width buy-credits-btn" data-amount="300">Buy Now</button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="panel-card p-4 h-100 border primary-bg-soft">
                                <h3>Pro</h3>
                                <div class="display-6 fw-bold mb-3">₹1,200</div>
                                <p class="text-muted mb-3">1,200 Credits</p>
                                <button type="button" class="primary-btn full-width buy-credits-btn" data-amount="1200">Buy Now</button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="panel-card p-4 h-100 border">
                                <h3>Premium</h3>
                                <div class="display-6 fw-bold mb-3">₹3,000</div>
                                <p class="text-muted mb-3">3,000 Credits</p>
                                <button type="button" class="primary-btn full-width buy-credits-btn" data-amount="3000">Buy Now</button>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="panel-card p-4 border">
                                <h3>Custom amount</h3>
                                <p class="text-muted mb-3">Choose any whole-number amount from ₹300 and above.</p>
                                <form id="customAmountForm" class="row g-3 align-items-end">
                                    <div class="col-md-8">
                                        <label for="customAmount" class="form-label">Amount (minimum ₹300)</label>
                                        <input id="customAmount" name="amount" type="number" class="form-control" min="300" step="1" value="300" required>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="primary-btn full-width">Continue to Payment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const purchaseUrl = '<?= base_url('credits/purchase') ?>';
            const notifyError = function(message) {
                if (window.showNotify) {
                    window.showNotify(message, 'error');
                } else {
                    window.alert(message);
                }
            };

            const startCheckout = async function(amount, button) {
                const paymentWindowName = 'payuCheckout_' + Date.now();
                const paymentWindow = window.open('about:blank', paymentWindowName);
                if (!paymentWindow) {
                    notifyError('Please allow pop-ups to open the payment gateway in a new tab.');
                    return;
                }

                if (button) {
                    button.disabled = true;
                }

                try {
                    const response = await fetch(purchaseUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new URLSearchParams({
                            amount: String(amount)
                        })
                    });
                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Unable to start payment. Please try again.');
                    }

                    const form = document.createElement('form');
                    form.method = 'post';
                    form.action = result.action;
                    form.target = paymentWindowName;
                    Object.entries(result.fields).forEach(function([name, value]) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = name;
                        input.value = value;
                        form.appendChild(input);
                    });
                    document.body.appendChild(form);
                    form.submit();
                } catch (error) {
                    paymentWindow.close();
                    notifyError(error.message || 'Unable to start payment. Please try again.');
                    if (button) {
                        button.disabled = false;
                    }
                }
            };

            document.querySelectorAll('.buy-credits-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    startCheckout(this.dataset.amount, this);
                });
            });

            document.getElementById('customAmountForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const amountInput = document.getElementById('customAmount');
                if (!amountInput.reportValidity()) {
                    return;
                }
                startCheckout(amountInput.value, this.querySelector('button[type="submit"]'));
            });
        });
    </script>
</body>

</html>