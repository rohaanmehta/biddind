<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | <?= esc(env('APP_NAME', 'Art Bidster')) ?></title>
    <?= view('templates/assets') ?>
</head>

<body>
    <?= view('templates/header', ['page' => 'payment-successful']) ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <section class="panel-card p-4 p-md-5 text-center" aria-labelledby="paymentStatus">
                    <h1 id="paymentStatus" class="mb-3">Payment Successful</h1>
                    <p class="text-muted mb-4">Your payment was completed successfully. Thank you for your purchase.</p>
                    <a href="<?= base_url('marketplace') ?>" class="primary-btn">Go to Marketplace</a>
                </section>
            </div>
        </div>
    </main>
</body>
</html>