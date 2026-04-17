<?php $this->layout('_panel'); ?>

<div class="top-bar">
    <div class="page-title">
        <h2><?= htmlspecialchars($pageTitle ?? 'Página Operacional', ENT_QUOTES, 'UTF-8'); ?></h2>
        <p><?= htmlspecialchars($pageDescription ?? 'Área reservada para evolução do sistema.', ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
</div>

<div class="settings-grid settings-grid-split clinical-patients-grid">
    <div class="settings-card settings-card-form clinical-workspace-card">
        <div class="card-header">
            <h3><i class="fas fa-compass-drafting"></i><?= htmlspecialchars($pageTitle ?? 'Página Operacional', ENT_QUOTES, 'UTF-8'); ?></h3>
        </div>
        <div class="settings-card-body">
            <div class="settings-form-intro">
                <div class="settings-form-badge"><?= htmlspecialchars($badge ?? 'Em preparação', ENT_QUOTES, 'UTF-8'); ?></div>
                <p><?= htmlspecialchars($pageDescription ?? 'Área reservada para evolução do sistema.', ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <?php if (!empty($highlights)) : ?>
                <div class="clinical-history-list">
                    <?php foreach ($highlights as $highlight) : ?>
                        <div class="clinical-history-item">
                            <div>
                                <strong>Ponto previsto</strong>
                                <span><?= htmlspecialchars((string)$highlight, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="clinical-side-column">
        <div class="settings-card settings-card-form clinical-workspace-card">
            <div class="card-header">
                <h3><i class="fas fa-road"></i>Rota Ativa</h3>
            </div>
            <div class="settings-card-body">
                <div class="clinical-workspace-empty">
                    <div class="settings-form-badge">Placeholder ligado</div>
                    <h4><?= htmlspecialchars($routePath ?? '/', ENT_QUOTES, 'UTF-8'); ?></h4>
                    <p>O menu já está ligado a uma rota real com controlador dedicado. A implementação funcional desta área pode evoluir sem voltar a depender de links quebrados ou marcadores temporários.</p>
                </div>
            </div>
        </div>
    </div>
</div>