<?php $this->layout('_panel'); ?>

<?php
$scope = $scope ?? 'documents';
$scopeMeta = [
    'saft' => ['title' => 'SAF-T AO', 'copy' => 'Exportação e coerência do ficheiro fiscal normalizado da clínica.'],
    'series' => ['title' => 'Séries Fiscais', 'copy' => 'Numeração, sequência documental e leitura das séries em uso.'],
    'certificates' => ['title' => 'Certificados Digitais', 'copy' => 'Assinatura e credenciais do ambiente fiscal eletrónico.'],
    'hash' => ['title' => 'Hash Chain', 'copy' => 'Integridade documental e encadeamento das faturas emitidas.'],
    'documents' => ['title' => 'Documentos Fiscais', 'copy' => 'Leitura documental e financeira da clínica, sem separar faturação, cobrança e posição em aberto.'],
    'audit' => ['title' => 'Auditoria Fiscal', 'copy' => 'Leitura operacional de risco, consistência e rastreabilidade dos documentos.'],
    'reports' => ['title' => 'Relatórios Fiscais', 'copy' => 'Síntese fiscal para acompanhamento, conferência e fecho periódico.']
];
$scopeInfo = $scopeMeta[$scope] ?? $scopeMeta['documents'];
?>

<div class="top-bar">
    <div class="page-title">
        <h2><?= htmlspecialchars($scopeInfo['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
        <p><?= htmlspecialchars($scopeInfo['copy'], ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
</div>

<div class="settings-grid settings-grid-split clinical-patients-grid">
    <div class="settings-card settings-card-table clinical-patients-table-card">
        <div class="card-header">
            <h3><i class="fas fa-receipt"></i><?= htmlspecialchars($scopeInfo['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <form method="get" class="clinical-toolbar-filter">
                <input type="hidden" name="scope" value="<?= htmlspecialchars($scope, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="text" name="s" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Pesquisar por número, estado ou nota do documento">
                <button type="submit" class="settings-submit-button clinical-filter-button"><i class="fas fa-search"></i><span>Filtrar</span></button>
            </form>
        </div>
        <div class="settings-card-body">
            <?php if ($billingReady) : ?>
                <div class="clinical-finance-metrics">
                    <div class="clinical-metric-card"><span>Total faturado</span><strong><?= str_price((string)($summary->total_invoiced ?? 0)); ?></strong></div>
                    <div class="clinical-metric-card"><span>Total recebido</span><strong><?= str_price((string)($summary->total_paid ?? 0)); ?></strong></div>
                    <div class="clinical-metric-card clinical-metric-card-alert"><span>Em aberto</span><strong><?= str_price((string)($summary->total_outstanding ?? 0)); ?></strong></div>
                    <div class="clinical-metric-card"><span>Rascunhos</span><strong><?= (int)($summary->draft_invoices ?? 0); ?></strong></div>
                </div>

                <div class="clinical-history-block">
                    <div class="clinical-history-head"><h4>Documentos recentes</h4><span><?= count($invoices); ?> registos</span></div>
                    <?php if (!empty($invoices)) : ?>
                        <div class="clinical-history-list">
                            <?php foreach ($invoices as $invoice) : ?>
                                <div class="clinical-history-item">
                                    <div>
                                        <strong><?= htmlspecialchars($invoice->invoice_number ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                                        <span><?= date('d/m/Y H:i', strtotime($invoice->invoice_date ?? 'now')); ?></span>
                                    </div>
                                    <div>
                                        <span class="clinical-status-badge status-<?= htmlspecialchars($invoice->status ?? 'draft', ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($invoice->status ?? 'draft', ENT_QUOTES, 'UTF-8'); ?></span>
                                        <a class="clinical-open-link" href="<?= url('/fiscal/overview?invoice_id=' . (int)($invoice->Id ?? 0)); ?>">Abrir ficha</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p class="settings-form-help">Sem documentos fiscais para os filtros atuais.</p>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div class="clinical-workspace-empty">
                    <div class="settings-form-badge">Fundação fiscal pendente</div>
                    <h4>As tabelas de faturação ainda não estão prontas</h4>
                    <p>O menu fiscal já está integrado no sistema, mas a base documental precisa estar disponível na base ativa para a leitura completa.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="clinical-side-column">
        <div class="settings-card settings-card-form clinical-workspace-card">
            <div class="card-header">
                <h3><i class="fas fa-file-invoice-dollar"></i>Ficha Fiscal</h3>
            </div>
            <div class="settings-card-body">
                <?php if (!empty($selectedInvoice)) : ?>
                    <div class="clinical-history-list">
                        <div class="clinical-history-item"><div><strong><?= htmlspecialchars($selectedInvoice->invoice_number ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong><span>Número do documento</span></div></div>
                        <div class="clinical-history-item"><div><strong><?= htmlspecialchars($selectedInvoice->status ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong><span>Estado fiscal e operacional</span></div></div>
                        <div class="clinical-history-item"><div><strong><?= str_price((string)($selectedInvoice->total_net ?? 0)); ?></strong><span>Total líquido</span></div></div>
                        <div class="clinical-history-item"><div><strong><?= date('d/m/Y H:i', strtotime($selectedInvoice->invoice_date ?? 'now')); ?></strong><span>Data do documento</span></div></div>
                    </div>
                    <p class="settings-form-help"><?= htmlspecialchars($selectedInvoice->notes ?? 'Sem notas fiscais adicionais.', ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php if (($selectedInvoice->status ?? null) === 'draft') : ?>
                        <form method="post" class="clinical-inline-actions js-ajax-form" data-confirm-title="Eliminar rascunho fiscal" data-confirm-message="O documento em rascunho e os respetivos itens serão removidos do módulo fiscal e do balcão clínico. Esta ação não pode ser anulada." data-confirm-label="Eliminar rascunho">
                            <?php echo csrf_input(); ?>
                            <input type="hidden" name="action" value="delete_draft_invoice">
                            <input type="hidden" name="invoice_id" value="<?= (int)($selectedInvoice->Id ?? 0); ?>">
                            <button type="submit" class="clinical-action-button clinical-action-button-danger js-ajax-submit" data-loading-text="A eliminar...">
                                <i class="fas fa-trash-alt"></i>
                                <span>Eliminar rascunho</span>
                            </button>
                        </form>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="clinical-workspace-empty">
                        <div class="settings-form-badge">Sem documento ativo</div>
                        <h4>Selecione um documento</h4>
                        <p>A leitura fiscal deve ficar no mesmo plano da faturação, cobrança e estado do processo.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>