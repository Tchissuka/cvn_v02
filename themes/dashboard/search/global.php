<?php $this->layout('_panel'); ?>

<div class="top-bar">
    <div class="page-title">
        <h2>Pesquisa Global</h2>
        <p>Um ponto único para localizar pacientes, serviços, produtos e documentos fiscais, sempre filtrado pela permissão operacional do utilizador</p>
    </div>
</div>

<div class="settings-grid settings-grid-split search-grid">
    <div class="settings-card settings-card-table search-card-main">
        <div class="card-header">
            <h3><i class="fas fa-compass"></i>Resultados operacionais</h3>
            <form method="get" class="clinical-toolbar-filter" action="<?= url('/search/global'); ?>">
                <input type="text" name="q" value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nome, contacto, serviço, produto, fatura, recibo">
                <button type="submit" class="settings-submit-button clinical-filter-button">
                    <i class="fas fa-search"></i>
                    <span>Procurar</span>
                </button>
            </form>
        </div>
        <div class="settings-card-body search-sections">
            <?php if ($query === '') : ?>
                <div class="clinical-workspace-empty">
                    <div class="settings-form-badge">Busca operacional</div>
                    <h4>Introduza um termo para começar</h4>
                    <p>A busca cruza pessoas, serviços, produtos e documentos e mostra apenas as áreas a que o utilizador tem acesso.</p>
                </div>
            <?php else : ?>
                <?php foreach ($results as $section => $items) : ?>
                    <section class="search-section">
                        <div class="clinical-history-head">
                            <h4><?= $section === 'patients' ? 'Pacientes' : ($section === 'services' ? 'Serviços' : ($section === 'products' ? 'Produtos' : 'Fiscal')); ?></h4>
                            <span><?= count($items); ?> resultado(s)</span>
                        </div>

                        <?php if (!empty($items)) : ?>
                            <div class="clinical-history-list">
                                <?php foreach ($items as $item) : ?>
                                    <?php if ($section === 'patients') : ?>
                                        <div class="clinical-history-item search-result-item">
                                            <div>
                                                <strong><?= htmlspecialchars($item->patient_name ?? ('Pessoa #' . ($item->person_id ?? '')), ENT_QUOTES, 'UTF-8'); ?></strong>
                                                <span><?= htmlspecialchars($item->patient_contact ?? 'Sem contacto', ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                            <div>
                                                <span>Processo <?= htmlspecialchars($item->medical_record_number ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                                                <a class="clinical-open-link" href="<?= url('/desk/patients?patient_id=' . (int)($item->Id ?? 0)); ?>">Abrir ficha</a>
                                            </div>
                                        </div>
                                    <?php elseif ($section === 'services') : ?>
                                        <div class="clinical-history-item search-result-item">
                                            <div>
                                                <strong><?= htmlspecialchars($item->name ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                                                <span><?= htmlspecialchars($item->code ?? 'Sem código', ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                            <div>
                                                <strong><?= str_price((string)($item->default_price ?? 0)); ?></strong>
                                                <a class="clinical-open-link" href="<?= url('/clinical/services?service_id=' . (int)($item->Id ?? 0)); ?>">Abrir ficha</a>
                                            </div>
                                        </div>
                                    <?php elseif ($section === 'products') : ?>
                                        <div class="clinical-history-item search-result-item">
                                            <div>
                                                <strong><?= htmlspecialchars($item->name ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                                                <span><?= htmlspecialchars($item->barcode ?? ($item->code ?? 'Sem código'), ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                            <div>
                                                <strong><?= str_price((string)($item->sale_price ?? 0)); ?></strong>
                                                <a class="clinical-open-link" href="<?= url('/pharmacy/products?product_id=' . (int)($item->Id ?? 0)); ?>">Abrir ficha</a>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <div class="clinical-history-item search-result-item">
                                            <div>
                                                <strong><?= htmlspecialchars($item->invoice_number ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                                                <span><?= htmlspecialchars($item->status ?? 'Sem estado', ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                            <div>
                                                <strong><?= str_price((string)($item->outstanding_balance ?? 0)); ?></strong>
                                                <a class="clinical-open-link" href="<?= url('/fiscal/overview?invoice_id=' . (int)($item->Id ?? 0)); ?>">Abrir ficha</a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <p class="settings-form-help">Sem resultados nesta área para o termo pesquisado.</p>
                        <?php endif; ?>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="clinical-side-column">
        <div class="settings-card settings-card-form clinical-workspace-card">
            <div class="card-header">
                <h3><i class="fas fa-sitemap"></i>Regra de operação</h3>
            </div>
            <div class="settings-card-body">
                <div class="settings-form-intro">
                    <div class="settings-form-badge">Filosofia MEXA aplicada</div>
                    <p>A busca não serve para “ver listas”. Serve para encontrar rapidamente o objeto operativo certo e abrir a respetiva ficha no ponto em que o trabalho continua.</p>
                </div>

                <div class="clinical-history-list">
                    <div class="clinical-history-item">
                        <div>
                            <strong>Paciente</strong>
                            <span>abre o balcão do paciente e o respetivo contexto financeiro</span>
                        </div>
                    </div>
                    <div class="clinical-history-item">
                        <div>
                            <strong>Serviço</strong>
                            <span>abre a ficha de preço e enquadramento clínico</span>
                        </div>
                    </div>
                    <div class="clinical-history-item">
                        <div>
                            <strong>Produto</strong>
                            <span>abre a ficha farmacêutica e comercial</span>
                        </div>
                    </div>
                    <div class="clinical-history-item">
                        <div>
                            <strong>Documento fiscal</strong>
                            <span>abre a leitura fiscal e financeira do caso</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>