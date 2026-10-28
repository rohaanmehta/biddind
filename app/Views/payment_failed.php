<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed | <?= esc(env('APP_NAME', 'Art Bidster')) ?></title>
    <?= view('templates/assets') ?>
</head>

<body>
    <?= view('templates/header', ['page' => 'payment-failed']) ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <section class="panel-card p-4 p-md-5 text-center" aria-labelledby="paymentStatus">
                    <h1 id="paymentStatus" class="mb-3">Payment Not Completed</h1>
                    <p class="text-muted mb-4">We couldn't confirm your payment. You can try again from the credits page.</p>
                    <a href="<?= base_url('buy-credits') ?>" class="primary-btn">Try Again</a>
                </section>
            </div>
        </div>
    </main>
</body>

</html>