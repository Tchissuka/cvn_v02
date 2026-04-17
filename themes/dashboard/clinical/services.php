<?php $this->layout('_panel'); ?>

<?php
$selectedServiceId = (int)($selectedService->Id ?? 0);
$currentPage = (int)($pagination['page'] ?? 1);
$totalPages = (int)($pagination['pages'] ?? 0);
?>

<div class="top-bar">
    <div class="page-title">
        <h2>Serviços</h2>
        <p>Tabela operativa dos serviços clínicos com preços base e ficha rápida para consulta</p>
    </div>
</div>

<div class="settings-grid settings-grid-split clinical-patients-grid">
    <div class="settings-card settings-card-table clinical-patients-table-card">
        <div class="card-header">
            <h3><i class="fas fa-leaf"></i>Catálogo de Serviços</h3>
            <form method="get" class="clinical-toolbar-filter">
                <input type="text" name="s" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Pesquisar por nome, código ou descrição">
                <button type="submit" class="settings-submit-button clinical-filter-button"><i class="fas fa-search"></i><span>Filtrar</span></button>
            </form>
        </div>
        <div class="settings-card-body">
            <div class="table-container clinical-table-container">
                <table class="clinical-data-table">
                    <thead>
                        <tr>
                            <th>Serviço</th>
                            <th>Código</th>
                            <th>Preço</th>
                            <th>Estado</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($services)) : ?>
                            <?php foreach ($services as $service) : ?>
                                <?php $isSelected = (int)($service->Id ?? 0) === $selectedServiceId; ?>
                                <tr class="<?= $isSelected ? 'clinical-row-selected' : ''; ?>">
                                    <td><strong><?= htmlspecialchars($service->name ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                    <td><?= htmlspecialchars($service->code ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= str_price((string)($service->default_price ?? 0)); ?></td>
                                    <td><span class="clinical-status-badge status-<?= !empty($service->is_active) ? 'open' : 'draft'; ?>"><?= !empty($service->is_active) ? 'ativo' : 'inativo'; ?></span></td>
                                    <td><a href="?service_id=<?= (int)($service->Id ?? 0); ?><?= !empty($search) ? '&s=' . urlencode($search) : ''; ?>" class="clinical-open-link <?= $isSelected ? 'is-active' : ''; ?>"><?= $isSelected ? 'Selecionado' : 'Abrir'; ?></a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="5" class="clinical-empty-state">Nenhum serviço encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($totalPages > 1) : ?>
                <div class="clinical-pagination">
                    <?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++) : ?>
                        <a href="?page=<?= $pageNumber; ?><?= !empty($search) ? '&s=' . urlencode($search) : ''; ?><?= $selectedServiceId ? '&service_id=' . $selectedServiceId : ''; ?>" class="clinical-page-link <?= $pageNumber === $currentPage ? 'is-active' : ''; ?>"><?= $pageNumber; ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="clinical-side-column">
        <div class="settings-card settings-card-form clinical-workspace-card">
            <div class="card-header">
                <h3><i class="fas fa-notes-medical"></i>Ficha do Serviço</h3>
            </div>
            <div class="settings-card-body">
                <?php if (!empty($selectedService)) : ?>
                    <div class="clinical-history-list">
                        <div class="clinical-history-item"><div><strong><?= htmlspecialchars($selectedService->name ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong><span>Nome base do serviço</span></div></div>
                        <div class="clinical-history-item"><div><strong><?= htmlspecialchars($selectedService->code ?? 'Sem código', ENT_QUOTES, 'UTF-8'); ?></strong><span>Referência operacional</span></div></div>
                        <div class="clinical-history-item"><div><strong><?= str_price((string)($selectedService->default_price ?? 0)); ?></strong><span>Preço base visível para atendimento e faturação</span></div></div>
                        <div class="clinical-history-item"><div><strong><?= !empty($selectedService->is_active) ? 'Ativo' : 'Inativo'; ?></strong><span>Estado de utilização</span></div></div>
                    </div>
                    <p class="settings-form-help"><?= htmlspecialchars($selectedService->description ?? 'Sem descrição operacional registada.', ENT_QUOTES, 'UTF-8'); ?></p>
                <?php else : ?>
                    <div class="clinical-workspace-empty">
                        <div class="settings-form-badge">Sem ficha ativa</div>
                        <h4>Selecione um serviço</h4>
                        <p>A ficha do serviço funciona como apoio direto ao balcão clínico e à faturação.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>