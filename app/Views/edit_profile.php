<?php
    $user = $user ?? ['name' => '', 'email' => '', 'phone' => ''];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | ArtBid</title>
    <?= view('templates/assets') ?>
</head>
<body>
    <?= view('templates/header', ['page' => 'edit-profile']) ?>

    <main class="page-shell">
        <section class="container form-page-wrap">
            <div class="panel-form" style="max-width: 640px;">
                <div class="section-head simple-head">
                    <div>
                        <p class="mini-label">PROFILE SETTINGS</p>
                        <h2>Edit Profile</h2>
                    </div>
                </div>

                <div class="mb-4 d-flex gap-2 flex-wrap">
                    <a href="<?= site_url('edit-profile') ?>" class="primary-btn">Edit Profile</a>
                    <a href="<?= site_url('my-bids') ?>" class="sell-btn">My Bids</a>
                </div>

                <form id="editProfileForm" action="<?= site_url('edit-profile') ?>" method="post" class="product-form">
                    <div class="form-grid">
                        <div class="form-row">
                            <label for="profile_name">Name</label>
                            <input id="profile_name" type="text" name="name" value="<?= esc($user['name'] ?? '') ?>" placeholder="Enter your name" required>
                        </div>

                        <div class="form-row">
                            <label for="profile_email">Email</label>
                            <input id="profile_email" type="email" name="email" value="<?= esc($user['email'] ?? '') ?>" placeholder="Enter your email" required>
                        </div>

                        <div class="form-row">
                            <label for="profile_phone">Phone</label>
                            <input id="profile_phone" type="tel" name="phone" value="<?= esc($user['phone'] ?? '') ?>" placeholder="Enter your phone number" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="primary-btn">Save Changes</button>
                        <!-- <a href="<?= site_url('/') ?>" class="sell-btn" style="text-decoration: none; display: inline-block;">Cancel</a> -->
                    </div>
                </form>
            </div>
        </section>
    </main>

    <script src="<?= base_url('public/assets/js/modal.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('editProfileForm');
            if (!form) return;

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(form);
                formData.append('is_ajax', '1');

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(async (response) => {
                    const text = await response.text();
                    let data = null;

                    if (text) {
                        try {
                            data = JSON.parse(text);
                        } catch (error) {
                            data = null;
                        }
                    }

                    if (!response.ok && !data) {
                        throw new Error('Request failed');
                    }

                    if (data && data.success) {
                        if (typeof window.showNotify === 'function') {
                            window.showNotify(data.message || 'Profile updated successfully.', 'success');
                        }
                        setTimeout(() => {
                            window.location.href = '<?= site_url('edit-profile') ?>';
                        }, 900);
                        return;
                    }

                    const message = data && data.message ? data.message : 'Unable to update profile.';
                    if (typeof window.showNotify === 'function') {
                        window.showNotify(message, 'error');
                    }
                })
                .catch((error) => {
                    console.error('Profile update error:', error);
                    if (typeof window.showNotify === 'function') {
                        window.showNotify('Unable to update profile. Please try again.', 'error');
                    }
                });
            });
        });
    </script>
</body>
</html>
