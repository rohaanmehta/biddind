<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How Art Auctions Work | <?= esc(env('APP_NAME', 'Art Bidster')) ?></title>
    <?= view('templates/assets', [
        'metaDescription' => 'Learn how to register, browse artworks, place bids, and win online painting auctions on ' . env('APP_NAME', 'Art Bidster') . '.',
        'ogTitle' => 'How Art Auctions Work | ' . env('APP_NAME', 'Art Bidster'),
        'canonicalUrl' => base_url('how-it-works'),
        'ogType' => 'website'
    ]) ?>
</head>

<body>
    <?= view('templates/header', ['page' => 'how-it-works']) ?>

    <main class="page-shell">
        <section class="container info-page">
            <div class="info-header">
                <p class="mini-label">How it works</p>
                <h1>Bid, win, and collect artwork in six easy steps.</h1>
            </div>

            <div class="steps-grid">
                <div class="step-box">
                    <span class="step-number">1</span>
                    <h3>Create Your Account</h3>
                    <p>Sign up on ArtBidster and verify your email to get started with your art-buying journey.</p>
                </div>

                <div class="step-box">
                    <span class="step-number">2</span>
                    <h3>Explore Artwork</h3>
                    <p>Browse unique paintings from talented artists and discover pieces that match your taste.</p>
                </div>

                <div class="step-box">
                    <span class="step-number">3</span>
                    <h3>View the Details</h3>
                    <p>Check the artwork description, starting price, current highest bid, and auction closing time.</p>
                </div>
            </div>

            <div class="steps-grid" style="margin-top: 25px;">
                <div class="step-box">
                    <span class="step-number">4</span>
                    <h3>Place Your Bid</h3>
                    <p>Enter your bid, compete with other art lovers, and keep track of the auction.</p>
                </div>

                <div class="step-box">
                    <span class="step-number">5</span>
                    <h3>Win the Auction</h3>
                    <p>Stay in the lead until the auction closes to become the winning bidder.</p>
                </div>

                <div class="step-box">
                    <span class="step-number">6</span>
                    <h3>Own Your Masterpiece</h3>
                    <p>Complete the payment and arrange delivery according to the seller's instructions to bring your new artwork home.</p>
                </div>
            </div>
        </section>
    </main>

</body>

</html>