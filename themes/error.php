<?php $this->layout("_panel"); ?>

<section class="error-page">
    <div class="error-shell">
        <span class="error-orb error-orb-left" aria-hidden="true"></span>
        <span class="error-orb error-orb-right" aria-hidden="true"></span>

        <div class="error-card">
            <p class="error-code">&bull;<?= $error->code; ?>&bull;</p>
            <h1 class="error-title"><?= $error->title; ?></h1>
            <p class="error-message"><?= $error->message; ?></p>

            <?php if (!empty($error->link) && empty($error->InOut)) : ?>
                <a class="error-link"
                    title="<?= $error->linkTitle; ?>"
                    href="<?= $error->link; ?>">
                    <?= $error->linkTitle; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>