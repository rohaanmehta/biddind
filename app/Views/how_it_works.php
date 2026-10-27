<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How It Works | ArtBid</title>
    <?= view('templates/assets') ?>
</head>

<body>
    <?= view('templates/header', ['page' => 'how-it-works']) ?>

    <main class="page-shell">
        <section class="container info-page">
            <div class="info-header">
                <p class="mini-label">How it works</p>
                <h1>Bid, win, and collect artwork in three easy steps.</h1>
            </div>

            <div class="steps-grid">
                <div class="step-box">
                    <span class="step-number">01</span>
                    <h3>Register your account</h3>
                    <p>Create a profile and verify your email with a one-time password for secure access.</p>
                </div>

                <div class="step-box">
                    <span class="step-number">02</span>
                    <h3>Browse the marketplace</h3>
                    <p>Discover paintings by category, artist, and style, then compare prices and bidding activity.</p>
                </div>

                <div class="step-box">
                    <span class="step-number">03</span>
                    <h3>Place your bid</h3>
                    <p>Monitor live listings, place your offer, and win the artwork when the auction closes.</p>
                </div>
            </div>

            <div class="steps-grid" style="margin-top: 25px;">
                <div class="step-box">
                    <span class="step-number">01</span>
                    <h3>Register your account</h3>
                    <p>Create a profile and verify your email with a one-time password for secure access.</p>
                </div>

                <div class="step-box">
                    <span class="step-number">02</span>
                    <h3>Browse the marketplace</h3>
                    <p>Discover paintings by category, artist, and style, then compare prices and bidding activity.</p>
                </div>

                <div class="step-box">
                    <span class="step-number">03</span>
                    <h3>Place your bid</h3>
                    <p>Monitor live listings, place your offer, and win the artwork when the auction closes.</p>
                </div>
            </div>
        </section>
    </main>

    <?= view('templates/login_modal') ?>
    <?= view('templates/register_modal') ?>

    <script src="<?= base_url('public/assets/js/modal.js') ?>"></script>
</body>

</html>