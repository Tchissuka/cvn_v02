<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="mit" content="2025-04-14T15:41:56-03:00+171975">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <?= $head; ?>
    <link rel="icon" type="image/png" href="<?= theme("/assets/images/logo.png"); ?>" />
    <link rel="stylesheet" href="<?= theme("/assets/style.css"); ?>" />
</head>



<body>
    <div class="app-container">
        <!-- Toggle Sidebar Button -->
        <div class="toggle-sidebar" id="toggleSidebar" onclick="toggleSidebar()">
            <i class="fas fa-chevron-left"></i>
        </div>

        <!-- Sidebar -->
        <?php $this->insert("sidebar"); ?>

        <main class="main-content" id="mainContent">
            <section class="global-search-strip">
                <div class="global-search-shell">
                    <div class="global-search-copy">
                        <span class="global-search-kicker">Operação rápida</span>
                        <strong class="global-search-title">Busca geral</strong>
                        <p class="global-search-text">Abra o paciente, serviço, produto ou documento certo sem sair do fluxo principal do painel.</p>
                    </div>
                    <form method="get" action="<?= url('/search/global'); ?>" class="global-search-form">
                        <label for="globalSearch" class="global-search-label">Termo de procura</label>
                        <div class="global-search-input-wrap">
                            <span class="global-search-icon"><i class="fas fa-search"></i></span>
                            <input
                                type="search"
                                id="globalSearch"
                                name="q"
                                value="<?= htmlspecialchars((string)($query ?? ($_GET['q'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>"
                                placeholder="Nome, processo, contacto, serviço, produto ou documento fiscal"
                            >
                        </div>
                        <button type="submit" class="settings-submit-button global-search-submit">Abrir</button>
                    </form>
                </div>
            </section>
            <?php if ($flash = flash()) : ?>
                <div class="app-flash-stack">
                    <?= $flash; ?>
                </div>
            <?php endif; ?>
            <?= $this->section("content"); ?>
        </main>
    </div>
    <div class="app-modal" id="appModal" hidden aria-hidden="true">
        <div class="app-modal-backdrop" data-modal-close></div>
        <div class="app-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="appModalTitle">
            <div class="app-modal-header">
                <div>
                    <div class="settings-form-badge app-modal-badge">Operação</div>
                    <h3 id="appModalTitle">Janela operacional</h3>
                </div>
                <button type="button" class="app-modal-close" data-modal-close aria-label="Fechar janela">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="app-modal-body" id="appModalBody"></div>
            <div class="app-modal-footer" id="appModalFooter"></div>
        </div>
    </div>
    <script src="<?= theme("/assets/scripts.js"); ?>"></script>
</body>

</html>