document.addEventListener('DOMContentLoaded', function () {
    const openButtons = document.querySelectorAll('[data-open-modal]');
    const closeButtons = document.querySelectorAll('[data-close-modal]');
    const switchButtons = document.querySelectorAll('[data-switch-modal]');
    const registerForm = document.getElementById('registerForm');
    const loginForm = document.getElementById('loginForm');
    const productForm = document.getElementById('productUploadForm');
    const profileTrigger = document.querySelector('.profile-trigger');
    const profileMenu = document.querySelector('.profile-menu-wrap');

    function showNotify(message, type = 'success') {
        let container = document.getElementById('notify-container');

        if (!container) {
            container = document.createElement('div');
            container.id = 'notify-container';
            container.className = 'notify-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'notify ' + (type === 'error' ? 'notify-error' : 'notify-success');
        toast.textContent = message;
        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('is-visible');
        }, 10);

        setTimeout(() => {
            toast.classList.remove('is-visible');
            setTimeout(() => toast.remove(), 250);
        }, 2600);
    }

    window.showNotify = showNotify;

    function openModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    }

    function showCreditsPrompt() {
        if (document.getElementById('creditsPromptModal')) {
            return;
        }

        const backdrop = document.createElement('div');
        backdrop.id = 'creditsPromptModal';
        backdrop.className = 'credits-prompt-backdrop is-open';

        const modal = document.createElement('div');
        modal.className = 'credits-prompt-modal';

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'credits-prompt-close';
        closeBtn.setAttribute('aria-label', 'Close credits prompt');
        closeBtn.textContent = '×';
        closeBtn.addEventListener('click', function () {
            backdrop.classList.remove('is-open');
            backdrop.remove();
            localStorage.removeItem('artbid_show_credits_prompt');
        });

        const badge = document.createElement('div');
        badge.className = 'credits-prompt-badge';
        badge.textContent = 'ArtBid Credits';

        const title = document.createElement('h3');
        title.textContent = 'Buy credits to bid on art';

        const message = document.createElement('p');
        message.textContent = 'You are ready to explore the marketplace. Add credits to start bidding on your favorite artworks.';

        const actions = document.createElement('div');
        actions.className = 'credits-prompt-actions';

        const buyBtn = document.createElement('a');
        buyBtn.href = '/bidding/buy-credits';
        buyBtn.className = 'primary-btn';
        buyBtn.textContent = 'Buy Credits';
        buyBtn.addEventListener('click', function () {
            localStorage.removeItem('artbid_show_credits_prompt');
        });

        const laterBtn = document.createElement('button');
        laterBtn.type = 'button';
        laterBtn.className = 'secondary-btn credits-prompt-later';
        laterBtn.textContent = 'Maybe Later';
        laterBtn.addEventListener('click', function () {
            backdrop.classList.remove('is-open');
            backdrop.remove();
            localStorage.removeItem('artbid_show_credits_prompt');
        });

        actions.appendChild(buyBtn);
        actions.appendChild(laterBtn);

        modal.appendChild(closeBtn);
        modal.appendChild(badge);
        modal.appendChild(title);
        modal.appendChild(message);
        modal.appendChild(actions);
        backdrop.appendChild(modal);
        document.body.appendChild(backdrop);
    }

    openButtons.forEach((button) => {
        button.addEventListener('click', function () {
            openModal(this.dataset.openModal);
        });
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', function () {
            closeModal(this.dataset.closeModal);
        });
    });

    switchButtons.forEach((button) => {
        button.addEventListener('click', function () {
            const target = this.dataset.switchModal;
            const current = document.querySelector('.modal-backdrop.is-open');
            if (current) {
                current.classList.remove('is-open');
                current.setAttribute('aria-hidden', 'true');
            }
            openModal(target);
        });
    });

    document.querySelectorAll('.password-toggle').forEach((toggle) => {
        toggle.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.togglePassword);
            if (!input) return;

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            this.classList.toggle('is-visible', isHidden);
            this.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            this.setAttribute('aria-pressed', String(isHidden));
        });
    });

    if (registerForm) {
        registerForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(registerForm);
            formData.append('is_ajax', '1');

            fetch(registerForm.action, {
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
                    } catch (e) {
                        data = null;
                    }
                }

                if (!response.ok && !data) {
                    throw new Error('Request failed');
                }

                if (data && data.success) {
                    showNotify(data.message || 'Account created successfully.', 'success');
                    registerForm.reset();
                    closeModal('registerModal');
                    localStorage.setItem('artbid_show_credits_prompt', '1');
                    setTimeout(() => {
                        window.location.href = data.redirect || '/bidding/';
                    }, 900);
                    return;
                }

                const message = data && data.message ? data.message : 'Registration failed. Please try again.';
                showNotify(message, 'error');
            })
            .catch((error) => {
                console.error('Register AJAX error:', error);
                showNotify('Registration failed. Please try again.', 'error');
            });
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(loginForm);
            formData.append('is_ajax', '1');

            fetch(loginForm.action, {
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
                    } catch (e) {
                        data = null;
                    }
                }

                if (!response.ok && !data) {
                    throw new Error('Request failed');
                }

                if (data && data.success) {
                    showNotify(data.message || 'Login successful.', 'success');
                    loginForm.reset();
                    closeModal('loginModal');
                    setTimeout(() => {
                        window.location.href = data.redirect || document.referrer || '/bidding/';
                    }, 900);
                    return;
                }

                const message = data && data.message ? data.message : 'Login failed. Please try again.';
                showNotify(message, 'error');
            })
            .catch((error) => {
                console.error('Login AJAX error:', error);
                showNotify('Login failed. Please try again.', 'error');
            });
        });
    }

    if (productForm) {
        productForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(productForm);
            formData.append('is_ajax', '1');

            fetch(productForm.action, {
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
                    } catch (e) {
                        data = null;
                    }
                }

                if (!response.ok && !data) {
                    throw new Error('Request failed');
                }

                if (data && data.success) {
                    showNotify(data.message || 'Product uploaded successfully.', 'success');
                    productForm.reset();
                    setTimeout(() => {
                        window.location.href = '/bidding/my-products';
                    }, 1000);
                    return;
                }

                const message = data && data.message ? data.message : 'Product upload failed. Please try again.';
                showNotify(message, 'error');
            })
            .catch((error) => {
                console.error('Product upload AJAX error:', error);
                showNotify('Product upload failed. Please try again.', 'error');
            });
        });
    }

    if (localStorage.getItem('artbid_show_credits_prompt') === '1') {
        setTimeout(() => showCreditsPrompt(), 200);
    }

    if (profileTrigger && profileMenu) {
        profileTrigger.addEventListener('click', function (event) {
            event.stopPropagation();
            profileMenu.classList.toggle('open');
            const isOpen = profileMenu.classList.contains('open');
            profileTrigger.setAttribute('aria-expanded', String(isOpen));
        });
    }

    document.addEventListener('click', function (event) {
        if (profileMenu && profileMenu.classList.contains('open')) {
            const insideDropdown = event.target.closest('.profile-menu-wrap');
            if (!insideDropdown) {
                profileMenu.classList.remove('open');
                if (profileTrigger) {
                    profileTrigger.setAttribute('aria-expanded', 'false');
                }
            }
        }

        const backdrop = event.target.closest('.modal-backdrop');
        if (!backdrop || !backdrop.classList.contains('is-open')) {
            return;
        }

        const modalBox = backdrop.querySelector('.modal-box');
        if (modalBox && modalBox.contains(event.target)) {
            return;
        }

        closeModal(backdrop.id);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal-backdrop.is-open').forEach((modal) => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            });
        }
    });
});
