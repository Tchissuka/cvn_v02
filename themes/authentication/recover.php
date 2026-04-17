<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $head; ?>
    <link rel="icon" type="image/png" href="<?= theme('/assets/images/logo.png'); ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <img src="<?= theme('/assets/images/logo.png'); ?>" alt="Logo" class="mb-3" style="height:64px;">
                            <h1 class="h5 mb-1">Recuperar senha</h1>
                            <p class="text-muted small mb-0">Informe o e-mail cadastrado para receber instruções.</p>
                        </div>

                        <form method="post" action="<?= url('/auth/recover'); ?>" autocomplete="off">
                            <?= csrf_input(); ?>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-2">Enviar link de recuperação</button>
                            <a href="<?= url('/auth/login'); ?>" class="btn btn-outline-secondary w-100">Voltar ao login</a>
                        </form>
                    </div>
                </div>
                <p class="text-center text-muted small mt-3 mb-0">&copy; <?= date('Y'); ?> <?= CONF_SITE_NAME; ?></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>