<?php $this->layout("_panel"); ?>

<div class="top-bar">
    <div class="page-title">
        <h2>Numeração Geral</h2>
        <p>Configuração de tipos de numeração e modos (anual / contínuo)</p>
    </div>

    <div class="top-bar-actions">
        <div class="date-display">
            <i class="far fa-calendar-alt"></i>
            <span><?= date('d/m/Y'); ?></span>
        </div>
    </div>
</div>

<div class="dashboard-grid settings-grid settings-grid-split">
    <!-- Lista de Numerações -->
    <div class="card settings-card settings-card-table">
        <div class="card-header">
            <h3><i class="fas fa-hashtag" style="margin-right:8px; color:var(--accent-color);"></i>Tipos de Numeração</h3>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Descrição</th>
                        <th>Modo</th>
                        <th>Nº Atual</th>
                        <th>Ano</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $list = $numerators ?? []; ?>
                    <?php if (!empty($list)): ?>
                        <?php foreach ($list as $num): ?>
                            <tr>
                                <td><?= $num->Id; ?></td>
                                <td><?= htmlspecialchars($num->type_num_id ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($num->description ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= $num->mode === 'C' ? 'Contínuo' : 'Anual'; ?></td>
                                <td><?= $num->number ?? ''; ?></td>
                                <td><?= $num->number_year ?? ''; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Nenhuma configuração de numeração encontrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Formulário de Numeração -->
    <div class="card settings-card settings-card-form">
        <div class="card-header">
            <h3><i class="fas fa-plus" style="margin-right:8px; color:var(--accent-color);"></i>Novo / Editar Tipo de Numeração</h3>
        </div>
        <div class="card-body settings-card-body">
            <div class="settings-form-intro">
                <span class="settings-form-badge">Sequência documental</span>
                <p>Configure a lógica de geração de números para documentos, processos e movimentos do sistema.</p>
            </div>

            <form class="js-ajax-form settings-form" method="post" action="<?= url('/setting/numerador'); ?>">
                <?= csrf_input(); ?>
                <input type="hidden" name="id" id="numerator_id" value="" />

                <div class="form-group">
                    <label for="type_num_id">Código / Tipo</label>
                    <input type="text" name="type_num_id" id="type_num_id" placeholder="Ex.: FATURA, RECIBO, PROCESSO" required />
                </div>

                <div class="form-group">
                    <label for="description">Descrição</label>
                    <input type="text" name="description" id="description" placeholder="Descreva o contexto desta numeração." />
                </div>

                <div class="form-group">
                    <label>Modo de Numeração</label>
                    <div class="settings-radio-group">
                        <label class="settings-radio-option">
                            <input type="radio" name="mode" value="A" checked />
                            <span>Anual (reinicia a cada ano)</span>
                        </label>
                        <label class="settings-radio-option">
                            <input type="radio" name="mode" value="C" />
                            <span>Contínuo</span>
                        </label>
                    </div>
                </div>

                <div class="settings-form-row settings-form-row-2">
                    <div class="form-group">
                        <label for="number_year">Ano de Referência</label>
                        <input type="number" name="number_year" id="number_year" value="<?= date('Y'); ?>" />
                    </div>

                    <div class="form-group">
                        <label for="number">Nº Inicial / Atual</label>
                        <input type="number" name="number" id="number" value="1" min="1" />
                    </div>
                </div>

                <button type="submit" class="settings-submit-button js-ajax-submit" data-loading-text="A guardar...">
                    <i class="fas fa-save"></i>
                    <span>Guardar Numeração</span>
                </button>
            </form>

            <p class="settings-form-help">
                Estes parâmetros alimentam a tabela <strong>general_numerator</strong> e o helper <strong>myNumber()</strong>.
            </p>
        </div>
    </div>
</div>