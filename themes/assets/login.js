document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const loginBtn = document.getElementById('loginBtn');
    const toggleIcon = document.getElementById('togglePassword');

    if (!form || !emailInput || !passwordInput || !loginBtn) {
        return;
    }

    let messageTimeout;

    function getOrCreateMessageBox() {
        let box = document.getElementById('loginMessage');
        if (!box) {
            box = document.createElement('div');
            box.id = 'loginMessage';
            box.className = 'app-alert app-alert-error';
            box.setAttribute('role', 'alert');
            box.setAttribute('aria-live', 'polite');
            box.innerHTML = '<span class="app-alert-icon" aria-hidden="true"><i class="fas fa-circle-exclamation"></i></span><span class="app-alert-content message-text"></span>';
            const card = document.querySelector('.login-card');
            if (card) {
                card.insertBefore(box, card.firstChild.nextSibling);
            } else if (form && form.parentElement) {
                form.parentElement.insertBefore(box, form);
            }
        }

        box.hidden = false;
        return box;
    }

    function showMessage(text, type = 'error') {
        const box = getOrCreateMessageBox();
        const icon = box.querySelector('.app-alert-icon i');

        const textSpan = box.querySelector('.message-text');
        if (textSpan) {
            textSpan.textContent = text;
        } else {
            box.textContent = text;
        }

        box.classList.remove('app-alert-error', 'app-alert-success', 'app-alert-warning', 'app-alert-info');
        box.classList.add(`app-alert-${type === 'success' ? 'success' : type}`);

        if (icon) {
            icon.className = `fas ${type === 'success' ? 'fa-circle-check' : type === 'warning' ? 'fa-triangle-exclamation' : type === 'info' ? 'fa-circle-info' : 'fa-circle-exclamation'}`;
        }

        if (messageTimeout) {
            clearTimeout(messageTimeout);
        }

        messageTimeout = setTimeout(() => {
            box.hidden = true;
        }, 6000);
    }

    function setLoading(isLoading) {
        if (isLoading) {
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;
        } else {
            loginBtn.classList.remove('loading');
            loginBtn.disabled = false;
        }
    }

    function togglePasswordVisibility() {
        if (!passwordInput || !toggleIcon) return;

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    if (toggleIcon) {
        toggleIcon.addEventListener('click', togglePasswordVisibility);
    }

    emailInput.addEventListener('input', function () {
        this.style.borderColor = this.value.includes('@') ? 'var(--accent-color)' : '#e1e1e1';
    });

    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function () {
            if (this.parentElement) {
                this.parentElement.style.transform = 'scale(1.02)';
            }
        });

        input.addEventListener('blur', function () {
            if (this.parentElement) {
                this.parentElement.style.transform = 'scale(1)';
            }
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const email = emailInput.value.trim();
        const password = passwordInput.value;

        if (!email || !password) {
            showMessage('Por favor, preencha todos os campos!');
            return;
        }

        setLoading(true);

        const formData = new FormData(form);
        const action = form.getAttribute('action') || window.location.href;

        fetch(action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json, text/plain, */*'
            }
        })
            .then(response => {
                const contentType = response.headers.get('Content-Type') || '';
                if (!contentType.includes('application/json')) {
                    throw new Error('Resposta inesperada do servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.redirect) {
                    showMessage(data.message || 'Login realizado com sucesso!', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 800);
                } else {
                    showMessage(data.message || 'Email ou senha inválidos.', 'error');
                }
            })
            .catch(() => {
                showMessage('Erro ao comunicar com o servidor. Tente novamente em instantes.', 'error');
            })
            .finally(() => {
                setLoading(false);
            });
    });
});