<?php $currencies = $currencies ?? [['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹']]; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product | ArtBid</title>
    <?= view('templates/assets', ['include_flatpickr' => true]) ?>
</head>

<body>
    <?= view('templates/header', ['page' => 'upload-products']) ?>

    <main class="page-shell">
        <section class="container form-page-wrap">
            <div class="panel-form" style="max-width: 100% !important;">
                <div class="section-head simple-head">
                    <div>
                        <p class="mini-label">SELL YOUR ART</p>
                        <h2>Upload Product</h2>
                    </div>
                </div>

                <form id="productUploadForm" class="product-form" action="<?= site_url('product/upload') ?>" method="post" enctype="multipart/form-data">
                    <div class="form-grid two-col">
                        <div class="form-row">
                            <label for="product_name">Product Name</label>
                            <input id="product_name" type="text" name="product_name" placeholder="Enter product name" required>
                        </div>

                        <div class="form-row">
                            <label for="product_category">Category</label>
                            <input id="product_category" type="text" name="category" placeholder="Painting, Sculpture, etc." required>
                        </div>

                        <div class="col-12">
                            <label for="product_images" class="form-label fw-semibold">Product Images</label>
                            <input id="product_images" type="file" name="product_images[]" accept="image/*" multiple class="form-control">
                            <!-- <small class="text-muted d-block mt-2">You can keep existing images or add new ones.</small> -->
                            <div id="new_image_preview_container" class="image-preview-container mt-3"></div>
                        </div>

                        <div class="form-row full-span">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4" placeholder="Describe the artwork..." required></textarea>
                        </div>

                        <div class="form-row full-span pricing-row">
                            <div class="pricing-field">
                                <label for="currency">Currency</label>
                                <select id="currency" name="currency" class="form-select form-select-sm" required>
                                    <?php foreach ($currencies as $currency): ?>
                                        <option value="<?= esc($currency['code'] ?? 'INR') ?>">
                                            <?= esc($currency['code'] ?? 'INR') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="pricing-field">
                                <label for="minimum_price">Minimum Price</label>
                                <input id="minimum_price" onkeypress="return event.charCode >= 48 && event.charCode <= 57" type="text" name="minimum_price" inputmode="decimal" placeholder="1500" required>
                            </div>

                            <div class="pricing-field">
                                <label for="bid_raise_price">Bid Raise Price</label>
                                <select id="bid_raise_price" name="bid_raise_price" class="form-select form-select-sm" required>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="200">200</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="2000">2000</option>
                                    <option value="5000">5000</option>
                                    <option value="10000">10000</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <label for="start_datetime">Bid Start Date & Time</label>
                            <input id="start_datetime" type="text" name="bidding_start_datetime" class="datetime-picker" placeholder="Select start date and time" required>
                        </div>

                        <div class="form-row">
                            <label for="end_datetime">Bid End Date & Time</label>
                            <input id="end_datetime" type="text" name="bidding_end_datetime" class="datetime-picker" placeholder="Select end date and time" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="primary-btn">Upload Product</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="<?= base_url('public/assets/js/modal.js') ?>"></script>
    <script>
        const startPicker = flatpickr('#start_datetime', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true,
            minDate: 'today',
            allowInput: true,
            defaultHour: 0,
            defaultMinute: 0,
            onChange: function(selectedDates, dateStr, instance) {
                if (document.getElementById('end_datetime').value) {
                    const start = new Date(dateStr);
                    const end = new Date(document.getElementById('end_datetime').value);

                    if (!isNaN(start.getTime()) && !isNaN(end.getTime()) && start > end) {
                        if (typeof showNotify === 'function') {
                            showNotify('Bid start date and time cannot be after bid end date and time.', 'error');
                        }
                        instance.clear();
                    }
                }
            }
        });

        const endPicker = flatpickr('#end_datetime', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true,
            minDate: 'today',
            allowInput: true,
            defaultHour: 0,
            defaultMinute: 0,
            onChange: function(selectedDates, dateStr, instance) {
                if (document.getElementById('start_datetime').value) {
                    const start = new Date(document.getElementById('start_datetime').value);
                    const end = new Date(dateStr);

                    if (!isNaN(start.getTime()) && !isNaN(end.getTime()) && end < start) {
                        if (typeof showNotify === 'function') {
                            showNotify('Bid end date and time cannot be less than bid start date and time.', 'error');
                        }
                        instance.clear();
                    }
                }
            }
        });

        const imageInput = document.getElementById('product_images');
        const imagePreviewContainer = document.getElementById('image_preview_container');

        imageInput.addEventListener('change', function() {
            const files = Array.from(this.files || []);
            imagePreviewContainer.innerHTML = '';

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
                    imagePreviewContainer.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });

            const fileLabel = document.getElementById('product_images_label');
            if (files.length > 0) {
                fileLabel.textContent = files.length + ' image(s) selected';
            } else {
                fileLabel.textContent = 'Upload one or more images for the artwork';
            }
        });

        document.getElementById('productUploadForm').addEventListener('submit', function(event) {
            const start = document.getElementById('start_datetime').value;
            const end = document.getElementById('end_datetime').value;

            if (!start || !end) {
                event.preventDefault();
                if (typeof showNotify === 'function') {
                    showNotify('Please select both bid start and end date and time.', 'error');
                }
                return;
            }

            if (new Date(end) < new Date(start)) {
                event.preventDefault();
                if (typeof showNotify === 'function') {
                    showNotify('Bid end date and time cannot be less than bid start date and time.', 'error');
                }
            }
        });
    </script>

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