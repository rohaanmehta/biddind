<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Page Not Found | <?= esc(env('APP_NAME', 'Art Bidster')) ?></title>
    <?= view('templates/assets') ?>
    <style>
        .not-found-shell {
            min-height: calc(100vh - 72px);
            display: grid;
            place-items: center;
            padding: 48px 16px;
        }
        .not-found-card {
            width: min(100%, 620px);
            text-align: center;
            border-radius: 8px;
        }
        .not-found-code {
            display: block;
            color: #d9a765;
            font-size: clamp(4rem, 16vw, 7rem);
            line-height: 0.85;
            font-weight: 800;
        }
        .not-found-card h1 {
            margin: 24px 0 12px;
            font-size: clamp(1.8rem, 5vw, 2.5rem);
        }
        .not-found-card p {
            max-width: 480px;
            margin: 0 auto 24px;
            color: var(--muted);
            line-height: 1.7;
        }
    </style>
</head>
<body>
    <?= view('templates/header', ['page' => '404']) ?>

    <main class="not-found-shell">
        <section class="not-found-card panel-card p-4 p-md-5" aria-labelledby="notFoundTitle">
            <span class="not-found-code">404</span>
            <h1 id="notFoundTitle">Page not found</h1>
            <p>The page may have moved, been removed, or the address may be incorrect.</p>
            <a href="<?= base_url('/') ?>" class="primary-btn">Return to Home</a>
        </section>
    </main>
</body>
</html>
