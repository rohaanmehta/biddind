<div class="modal-backdrop" id="registerModal" aria-hidden="true">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="registerTitle">
        <button type="button" class="modal-close" data-close-modal="registerModal" aria-label="Close">×</button>

        <h3 id="registerTitle">Register</h3>

        <form id="registerForm" action="<?= site_url('auth/register') ?>" method="post" class="auth-form">
            <div class="form-row">
                <label for="reg_name">Name</label>
                <input id="reg_name" type="text" name="name" placeholder="Enter your full name" required>
            </div>

            <div class="form-row">
                <label for="reg_email">Email</label>
                <input id="reg_email" type="email" name="email" placeholder="you@example.com" required>
            </div>

            <div class="form-row">
                <label for="reg_phone">Phone</label>
                <input id="reg_phone" type="tel" name="phone" placeholder="Enter phone number" required>
            </div>

            <div class="form-row">
                <label for="reg_password">Password</label>
                <div class="password-field">
                    <input id="reg_password" type="password" name="password" placeholder="Create password" required>
                    <button type="button" class="password-toggle" data-toggle-password="reg_password" aria-label="Show password" aria-pressed="false">
                        <svg class="eye-icon eye-open" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Zm10 3.5A3.5 3.5 0 1 0 12 8.5a3.5 3.5 0 0 0 0 7Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <svg class="eye-icon eye-off" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 3l18 18M10.5 10.5A2.5 2.5 0 0 0 13.5 13.5M9.1 5.4A11.4 11.4 0 0 1 12 5c6.5 0 10 7 10 7a16.9 16.9 0 0 1-4.2 5.2M6.8 6.8A16.6 16.6 0 0 0 2 12s3.5 7 10 7a11.4 11.4 0 0 0 6-1.8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="primary-btn full-width">Register</button>
        </form>

        <p class="switch-text">
            Already have an account?
            <button type="button" class="text-link" data-switch-modal="loginModal">Login</button>
        </p>
    </div>
</div>
