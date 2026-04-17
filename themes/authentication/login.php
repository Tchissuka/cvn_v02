<?php $this->layout("_authentic"); ?>
<div class="login-card">
    <div class="login-header">
        <div class="logo-circle">
            <img src="<?= theme("/assets/images/logo.png"); ?>" alt="Logo Clínica Videira Nguepe" class="clinic-logo">
        </div>
        <h2>Clínica Videira Nguepe</h2>
        <p>
            <i class="fas fa-spa"></i>
            Saúde Natural & Bem-estar
        </p>
    </div>

    <div id="loginMessage" class="app-alert app-alert-error" hidden role="alert" aria-live="polite">
        <span class="app-alert-icon" aria-hidden="true">
            <i class="fas fa-circle-exclamation"></i>
        </span>
        <span class="app-alert-content message-text"></span>
    </div>

    <form method="post" action="<?= url('/auth/login'); ?>" id="loginForm" autocomplete="off">
        <?= csrf_input(); ?>
        <div class="form-group">
            <label>
                <i class="fas fa-envelope"></i>
                E-mail
            </label>
            <div class="input-wrapper">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" placeholder="dr.jose@videiranguape.co.ao" required>
            </div>
        </div>

        <div class="form-group">
            <label>
                <i class="fas fa-lock"></i>
                Senha
            </label>
            <div class="input-wrapper">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <i class="fas fa-eye" id="togglePassword" style="left: auto; right: 15px; cursor: pointer;" onclick="togglePasswordVisibility()"></i>
            </div>
        </div>

        <div class="form-options">
            <label class="remember-me">
                <input type="checkbox" name="remember">
                <i class="fas fa-check-circle"></i>
                Lembrar-me
            </label>
            <a href="<?= url('/auth/recover'); ?>" class="forgot-password">
                <i class="fas fa-question-circle"></i>
                Esqueceu a senha?
            </a>
        </div>

        <button type="submit" class="login-btn" id="loginBtn">
            <span class="btn-text">Entrar no Sistema</span>
            <span class="loader"></span>
            <i class="fas fa-arrow-right"></i>
        </button>
    </form>

    <div class="login-footer">
        <p>
            <i class="fas fa-phone-alt"></i>
            Precisa de ajuda?
            <a href="#">
                <i class="fas fa-headset"></i>
                Contatar suporte
            </a>
        </p>

    </div>
</div>