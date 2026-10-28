<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | <?= esc(env('APP_NAME', 'Art Bidster')) ?></title>
    <?= view('templates/assets') ?>
</head>

<body>
    <?php $productData = $product ?? []; ?>
    <?= view('templates/header', ['page' => 'my-products']) ?>

    <main class="page-shell">
        <div class="container py-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <p class="mini-label mb-2">UPDATE LISTING</p>
                        <h2 class="mb-0">Edit Product</h2>
                    </div>
                    <a href="<?= site_url('my-products') ?>" class="btn btn-outline-dark">Back to My Products</a>
                </div>

                <form action="<?= site_url('product/update/' . ($productData['id'] ?? 0)) ?>" method="post" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="product_name" class="form-label fw-semibold">Product Name</label>
                            <input id="product_name" type="text" name="product_name" class="form-control" value="<?= esc($productData['product_name'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="category" class="form-label fw-semibold">Category</label>
                            <input id="category" type="text" name="category" class="form-control" value="<?= esc($productData['category'] ?? '') ?>" required>
                        </div>

                        <div class="col-12">
                            <label for="product_images" class="form-label fw-semibold">Product Images</label>
                            <input id="product_images" type="file" name="product_images[]" accept="image/*" multiple class="form-control">
                            <small class="text-muted d-block mt-2">You can keep existing images or add new ones.</small>
                            <div id="new_image_preview_container" class="image-preview-container mt-3"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Current Product Images</label>
                            <?php
                            $existingImageList = json_decode($productData['image_urls'] ?? '[]', true);
                            $existingImageList = is_array($existingImageList) ? $existingImageList : [];
                            ?>
                            <?php if (!empty($existingImageList)): ?>
                                <div class="image-preview-container existing-images-row">
                                    <?php foreach ($existingImageList as $index => $imagePath): ?>
                                        <?php $safeImagePath = base_url(str_replace('\\', '/', $imagePath)); ?>
                                        <div class="image-preview-item existing-image-item">
                                            <img src="<?= esc($safeImagePath) ?>" alt="Product image <?= esc($index + 1) ?>">
                                            <label class="remove-image-check">
                                                <input type="checkbox" name="remove_images[<?= esc($index) ?>]" value="1">
                                                <span>Remove</span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No product images saved.</p>
                            <?php endif; ?>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="4" required><?= esc($productData['description'] ?? '') ?></textarea>
                        </div>

                        <div class="col-md-4">
                            <label for="currency" class="form-label fw-semibold">Currency</label>
                            <select id="currency" name="currency" class="form-select form-select-sm" required>
                                <?php foreach ($currencies ?? [] as $currency): ?>
                                    <option value="<?= esc($currency['code'] ?? 'INR') ?>" <?= (($productData['currency'] ?? 'INR') === ($currency['code'] ?? 'INR')) ? 'selected' : '' ?>>
                                        <?= esc($currency['code'] ?? 'INR') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="minimum_price" class="form-label fw-semibold">Minimum Price</label>
                            <input id="minimum_price" type="text" name="minimum_price" class="form-control" value="<?= esc($productData['minimum_price'] ?? '') ?>" onkeypress="return event.charCode >= 48 && event.charCode <= 57" inputmode="decimal" required>
                        </div>

                        <div class="col-md-4">
                            <label for="bid_raise_price" class="form-label fw-semibold">Bid Raise Price</label>
                            <select id="bid_raise_price" name="bid_raise_price" class="form-select form-select-sm" required>
                                <option value="20" <?= (($productData['bid_raise_price'] ?? '20') == '20') ? 'selected' : '' ?>>20</option>
                                <option value="50" <?= (($productData['bid_raise_price'] ?? '20') == '50') ? 'selected' : '' ?>>50</option>
                                <option value="200" <?= (($productData['bid_raise_price'] ?? '20') == '200') ? 'selected' : '' ?>>200</option>
                                <option value="500" <?= (($productData['bid_raise_price'] ?? '20') == '500') ? 'selected' : '' ?>>500</option>
                                <option value="1000" <?= (($productData['bid_raise_price'] ?? '20') == '1000') ? 'selected' : '' ?>>1000</option>
                                <option value="2000" <?= (($productData['bid_raise_price'] ?? '20') == '2000') ? 'selected' : '' ?>>2000</option>
                                <option value="5000" <?= (($productData['bid_raise_price'] ?? '20') == '5000') ? 'selected' : '' ?>>5000</option>
                                <option value="10000" <?= (($productData['bid_raise_price'] ?? '20') == '10000') ? 'selected' : '' ?>>10000</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="bidding_start_datetime" class="form-label fw-semibold">Bid Start Date & Time</label>
                            <input id="bidding_start_datetime" type="datetime-local" name="bidding_start_datetime" class="form-control" value="<?= esc(str_replace(' ', 'T', $productData['bidding_start_datetime'] ?? '')) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="bidding_end_datetime" class="form-label fw-semibold">Bid End Date & Time</label>
                            <input id="bidding_end_datetime" type="datetime-local" name="bidding_end_datetime" class="form-control" value="<?= esc(str_replace(' ', 'T', $productData['bidding_end_datetime'] ?? '')) ?>" required>
                        </div>



                    </div>

                    <div class="mt-4">
                        <button type="submit" class="primary-btn w-100">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        const editImageInput = document.getElementById('product_images');
        const newImagePreviewContainer = document.getElementById('new_image_preview_container');

        if (editImageInput && newImagePreviewContainer) {
            editImageInput.addEventListener('change', function() {
                const files = Array.from(this.files || []);
                newImagePreviewContainer.innerHTML = '';

                files.forEach((file) => {
                    if (!file.type.startsWith('image/')) {
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'image-preview-item';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = file.name;
                        img.title = file.name;

                        wrapper.appendChild(img);
                        newImagePreviewContainer.appendChild(wrapper);
                    };
                    reader.readAsDataURL(file);
                });
            });
        }
    </script>
</body>

</html>