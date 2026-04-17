<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $head; ?>
    <link rel="icon" type="image/png" href="<?= theme("/assets/images/logo.png"); ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= theme("/assets/login.css"); ?>" />
    <style>

    </style>
</head>

<body>
    <div class="login-container">
        <?php if ($flash = flash()) : ?>
            <div class="app-flash-stack">
                <?= $flash; ?>
            </div>
        <?php endif; ?>
        <?= $this->section("content"); ?>
    </div>
    <script src="<?= theme("/assets/login.js"); ?>"></script>
</body>

</html>