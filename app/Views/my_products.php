<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products | <?= esc(env('APP_NAME', 'Art Bidster')) ?></title>
    <?= view('templates/assets') ?>
</head>

<body>
    <?= view('templates/header', ['page' => 'my-products']) ?>

    <main class="page-shell">
        <section class="container py-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 product-table-card">
                <div class="section-head simple-head">
                    <div>
                        <p class="mini-label">MY COLLECTION</p>
                        <h2>Products</h2>
                    </div>
                    <a href="<?= site_url('upload-products') ?>" class="primary-btn text-light">Upload New Product</a>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <div class="table-responsive product-table-wrap">
                    <table class="table table-striped products-table align-middle mb-0" style="width: 100%;">
                        <thead>
                            <tr>
                                <!-- <th>Image</th> -->
                                <th>Product</th>
                                <th>Category</th>
                                <th>Min Price</th>
                                <th>Bid Raise</th>
                                <th>Bid Start Date</th>
                                <th>Bid End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $product): ?>
                                    <?php
                                    $imageJson = $product['image_urls'] ?? '[]';
                                    $imageList = json_decode($imageJson, true);
                                    $firstImage = is_array($imageList) && !empty($imageList)
                                        ? base_url(str_replace('\\', '/', $imageList[0]))
                                        : 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?auto=format&fit=crop&w=600&q=80';

                                    $currencyCode = strtoupper($product['currency'] ?? 'INR');
                                    $currencySymbols = [
                                        'INR' => '₹',
                                        'USD' => '$',
                                    ];
                                    $currencySymbol = $currencySymbols[$currencyCode] ?? $currencyCode;

                                    $now = new DateTime();
                                    $startDate = !empty($product['bidding_start_datetime']) ? new DateTime($product['bidding_start_datetime']) : null;
                                    $endDate = !empty($product['bidding_end_datetime']) ? new DateTime($product['bidding_end_datetime']) : null;

                                    $status = 'Upcoming';
                                    if ($startDate && $now >= $startDate && $endDate && $now < $endDate) {
                                        $status = 'Live';
                                    } elseif ($endDate && $now >= $endDate) {
                                        $status = 'Ended';
                                    }
                                    ?>
                                    <tr>
                                        <!-- <td class="product-thumb-cell">
                                            <img src="<//?= esc($firstImage) ?>" alt="<//?= esc($product['product_name']) ?>" class="product-thumb">
                                        </td> -->
                                        <td>
                                            <div class="product-name"><?= esc($product['product_name']) ?></div>
                                        </td>
                                        <td><?= esc($product['category']) ?></td>
                                        <td><?= esc($currencySymbol) ?><?= number_format((float) $product['minimum_price'], 2) ?></td>
                                        <td><?= esc($currencySymbol) ?><?= number_format((float) $product['bid_raise_price'], 2) ?></td>
                                        <td><?= esc(date('d M Y, H:i', strtotime($product['bidding_start_datetime']))) ?></td>
                                        <td><?= esc(date('d M Y, H:i', strtotime($product['bidding_end_datetime']))) ?></td>
                                        <td>
                                            <span style="font-size: 13px;padding: 7px 15px 7px 15px;" class="badge rounded-pill <?= $status === 'Live' ? 'bg-success' : ($status === 'Ended' ? 'bg-secondary' : 'bg-warning text-dark') ?>">
                                                <?= esc($status) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="product-table-actions">
                                                <a href="<?= site_url('product/bids/' . $product['id']) ?>" class="btn btn-sm btn-outline-dark">View Bids</a>
                                                <a href="<?= site_url('product/' . $product['id']) ?>" class="btn btn-sm btn-outline-dark" target="_blank" rel="noopener noreferrer" title="View product" aria-label="View <?= esc($product['product_name'], 'attr') ?>">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="<?= site_url('product/edit/' . $product['id']) ?>" class="btn btn-sm btn-outline-dark" title="Edit product" aria-label="Edit product">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-dark share-product-btn" data-url="<?= esc(site_url('product/' . $product['id']), 'attr') ?>" data-name="<?= esc($product['product_name'], 'attr') ?>" title="Share product" aria-label="Share <?= esc($product['product_name'], 'attr') ?>">
                                                    <i class="fa-solid fa-share-nodes"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-product-btn" data-id="<?= $product['id'] ?>" title="Delete product" aria-label="Delete product">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">No products uploaded yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadedProduct = sessionStorage.getItem('uploadedProductSuccess');
            if (uploadedProduct && typeof Swal !== 'undefined') {
                sessionStorage.removeItem('uploadedProductSuccess');

                let uploadData = {};
                try {
                    uploadData = JSON.parse(uploadedProduct);
                } catch (error) {
                    uploadData = {};
                }

                const productUrl = uploadData.productUrl || '';
                Swal.fire({
                    title: 'Product uploaded successfully',
                    html: '<p style="margin-bottom:12px;color:#6b7280;line-height:1.6;">Your artwork is live. Share the link below with friends and on social media so others can discover it and place bids.</p>' +
                        (productUrl ? '<input id="shared-product-url" value="' + productUrl.replace(/["&<>']/g, '') + '" readonly style="width:100%;border:1px solid #ddd;border-radius:8px;padding:10px;font-size:13px;">' : ''),
                    icon: 'success',
                    confirmButtonText: 'Share Product',
                    showDenyButton: Boolean(productUrl),
                    denyButtonText: 'Copy Link',
                    confirmButtonColor: '#111111',
                    denyButtonColor: '#111111',
                    width: 480,
                    customClass: {
                        confirmButton: 'swal-confirm-dark',
                        denyButton: 'swal-copy-link'
                    }
                }).then(async function(result) {
                    if (!productUrl) return;

                    if (result.isConfirmed && navigator.share) {
                        try {
                            await navigator.share({
                                title: 'View this artwork auction',
                                text: 'Discover this artwork and place your bid.',
                                url: productUrl
                            });
                        } catch (error) {
                            if (error && error.name !== 'AbortError') {
                                window.alert('Unable to share the product link.');
                            }
                        }
                    } else if (result.isConfirmed) {
                        try {
                            await navigator.clipboard.writeText(productUrl);
                            // window.alert('Product link copied to clipboard.');
                        } catch (error) {
                            window.alert(productUrl);
                        }
                    } else if (result.isDenied) {
                        try {
                            await navigator.clipboard.writeText(productUrl);
                            // window.alert('Product link copied to clipboard.');
                        } catch (error) {
                            window.alert(productUrl);
                        }
                    }
                });
            }

            document.querySelectorAll('.share-product-btn').forEach(function(button) {
                button.addEventListener('click', async function() {
                    const productUrl = this.dataset.url;
                    const productName = this.dataset.name || 'Artwork auction';
                    const shareData = {
                        title: productName,
                        text: 'View this artwork auction and place your bid.',
                        url: productUrl
                    };

                    try {
                        if (navigator.share) {
                            await navigator.share(shareData);
                            return;
                        }

                        await navigator.clipboard.writeText(productUrl);
                        if (window.showNotify) {
                            window.showNotify('Product link copied to clipboard.', 'success');
                        } else {
                            alert('Product link copied to clipboard.');
                        }
                    } catch (error) {
                        if (error && error.name === 'AbortError') {
                            return;
                        }
                        alert(productUrl);
                    }
                });
            });

            document.querySelectorAll('.delete-product-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    if (!productId) return;

                    const confirmed = confirm('Are you sure you want to delete this product?');
                    if (!confirmed) return;

                    fetch('<?= site_url('product/delete') ?>/' + productId, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(async function(response) {
                            const text = await response.text();
                            let data = null;
                            try {
                                data = text ? JSON.parse(text) : null;
                            } catch (e) {
                                data = null;
                            }

                            if (!response.ok && !data) {
                                throw new Error('Delete failed');
                            }

                            if (data && data.success) {
                                location.reload();
                                return;
                            }

                            alert(data && data.message ? data.message : 'Unable to delete product.');
                        })
                        .catch(function() {
                            alert('Unable to delete product.');
                        });
                });
            });
        });
    </script>
</body>

</html>