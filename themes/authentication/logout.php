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
                    <div class="card-body p-4 text-center">
                        <img src="<?= theme('/assets/images/logo.png'); ?>" alt="Logo" class="mb-3" style="height:64px;">
                        <h1 class="h5 mb-2">Sessão encerrada</h1>
                        <p class="text-muted mb-4">Você saiu do sistema com segurança.</p>
                        <a href="<?= url('/auth/login'); ?>" class="btn btn-primary w-100">Voltar para o login</a>
                    </div>
                </div>
                <p class="text-center text-muted small mt-3 mb-0">&copy; <?= date('Y'); ?> <?= CONF_SITE_NAME; ?></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>