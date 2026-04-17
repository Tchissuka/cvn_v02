<?php $this->layout('_panel'); ?>

<?php
$selectedProductId = (int)($selectedProduct->Id ?? 0);
$currentPage = (int)($pagination['page'] ?? 1);
$totalPages = (int)($pagination['pages'] ?? 0);
?>

<div class="top-bar">
    <div class="page-title">
        <h2>Farmácia</h2>
        <p>Catálogo operacional de produtos para venda, dispensa e conferência rápida no balcão</p>
    </div>
</div>

<div class="settings-grid settings-grid-split clinical-patients-grid">
    <div class="settings-card settings-card-table clinical-patients-table-card">
        <div class="card-header">
            <h3><i class="fas fa-boxes"></i>Produtos</h3>
            <form method="get" class="clinical-toolbar-filter">
                <input type="text" name="s" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Pesquisar por nome, código, código de barras ou descrição">
                <button type="submit" class="settings-submit-button clinical-filter-button"><i class="fas fa-search"></i><span>Filtrar</span></button>
            </form>
        </div>
        <div class="settings-card-body">
            <div class="table-container clinical-table-container">
                <table class="clinical-data-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Referência</th>
                            <th>Venda</th>
                            <th>Estado</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)) : ?>
                            <?php foreach ($products as $product) : ?>
                                <?php $isSelected = (int)($product->Id ?? 0) === $selectedProductId; ?>
                                <tr class="<?= $isSelected ? 'clinical-row-selected' : ''; ?>">
                                    <td><strong><?= htmlspecialchars($product->name ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                    <td><?= htmlspecialchars($product->barcode ?? ($product->code ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= str_price((string)($product->sale_price ?? 0)); ?></td>
                                    <td><span class="clinical-status-badge status-<?= !empty($product->is_active) ? 'open' : 'draft'; ?>"><?= !empty($product->is_active) ? 'ativo' : 'inativo'; ?></span></td>
                                    <td><a href="?product_id=<?= (int)($product->Id ?? 0); ?><?= !empty($search) ? '&s=' . urlencode($search) : ''; ?>" class="clinical-open-link <?= $isSelected ? 'is-active' : ''; ?>"><?= $isSelected ? 'Selecionado' : 'Abrir'; ?></a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="5" class="clinical-empty-state">Nenhum produto encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($totalPages > 1) : ?>
                <div class="clinical-pagination">
                    <?php for ($pageNumber = 1; $pageNumber <= $totalPages; $pageNumber++) : ?>
                        <a href="?page=<?= $pageNumber; ?><?= !empty($search) ? '&s=' . urlencode($search) : ''; ?><?= $selectedProductId ? '&product_id=' . $selectedProductId : ''; ?>" class="clinical-page-link <?= $pageNumber === $currentPage ? 'is-active' : ''; ?>"><?= $pageNumber; ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="clinical-side-column">
        <div class="settings-card settings-card-form clinical-workspace-card">
            <div class="card-header">
                <h3><i class="fas fa-capsules"></i>Ficha do Produto</h3>
            </div>
            <div class="settings-card-body">
                <?php if (!empty($selectedProduct)) : ?>
                    <div class="clinical-history-list">
                        <div class="clinical-history-item"><div><strong><?= htmlspecialchars($selectedProduct->name ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong><span>Produto farmacêutico/comercial</span></div></div>
                        <div class="clinical-history-item"><div><strong><?= htmlspecialchars($selectedProduct->barcode ?? ($selectedProduct->code ?? 'Sem referência'), ENT_QUOTES, 'UTF-8'); ?></strong><span>Código ou código de barras</span></div></div>
                        <div class="clinical-history-item"><div><strong><?= str_price((string)($selectedProduct->sale_price ?? 0)); ?></strong><span>Preço de venda</span></div></div>
                        <div class="clinical-history-item"><div><strong><?= str_price((string)($selectedProduct->cost_price ?? 0)); ?></strong><span>Preço de custo</span></div></div>
                    </div>
                    <p class="settings-form-help"><?= htmlspecialchars($selectedProduct->description ?? 'Sem descrição operacional registada.', ENT_QUOTES, 'UTF-8'); ?></p>
                <?php else : ?>
                    <div class="clinical-workspace-empty">
                        <div class="settings-form-badge">Sem ficha ativa</div>
                        <h4>Selecione um produto</h4>
                        <p>A ficha do produto é a porta de entrada para venda, stock e dispensa no balcão da farmácia.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>