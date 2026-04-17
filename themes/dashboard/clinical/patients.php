<?php $this->layout("_panel"); ?>

<?php
$selectedPatientId = (int)($selectedPatient->Id ?? 0);
$currentPage = (int)($pagination['page'] ?? 1);
$totalPages = (int)($pagination['pages'] ?? 0);
$windowStart = max(1, $currentPage - 2);
$windowEnd = min($totalPages, $currentPage + 2);
?>

<div class="top-bar">
    <div class="page-title">
        <h2>Balcão</h2>
        <p>Receção, pedido do serviço, possível acoplamento do médico e leitura financeira do processo no mesmo ponto operativo</p>
    </div>

    <div class="top-bar-actions">
        <div class="date-display">
            <i class="far fa-calendar-alt"></i>
            <span><?php echo date('d/m/Y'); ?></span>
        </div>
    </div>
</div>

<div class="settings-grid settings-grid-split clinical-patients-grid">
    <div class="settings-card settings-card-table clinical-patients-table-card">
        <div class="card-header">
            <h3><i class="fas fa-cash-register"></i>Operações do Paciente</h3>
            <form method="get" class="clinical-toolbar-filter">
                <input
                    type="text"
                    name="s"
                    value="<?php echo htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Pesquisar por processo, nome, contacto ou person ID"
                />
                <button type="submit" class="settings-submit-button clinical-filter-button">
                    <i class="fas fa-search"></i>
                    <span>Filtrar</span>
                </button>
            </form>
        </div>

        <div class="settings-card-body">
            <div class="settings-form-intro clinical-patients-intro">
                <div class="settings-form-badge">Balcão clínico</div>
                <p>
                    Pesquise o paciente antes de registar novo vínculo. O processo começa aqui: o balcão recebe o pedido, escolhe o serviço, pode já acoplar o médico se for consulta e deixa o contexto financeiro pronto para o restante fluxo.
                </p>
            </div>

            <div class="table-container clinical-table-container">
                <table class="clinical-data-table">
                    <thead>
                        <tr>
                            <th>Processo</th>
                            <th>Paciente</th>
                            <th>Contacto</th>
                            <th>Seguro</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($patients)): ?>
                            <?php foreach ($patients as $patient): ?>
                                <?php $rowSelected = (int)($patient->Id ?? 0) === $selectedPatientId; ?>
                                <tr class="<?php echo $rowSelected ? 'clinical-row-selected' : ''; ?>">
                                    <td>
                                        <div class="clinical-process-cell">
                                            <span class="clinical-id-badge"><?php echo (int)($patient->Id ?? 0); ?></span>
                                            <strong><?php echo htmlspecialchars($patient->medical_record_number ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="clinical-patient-meta">
                                            <strong><?php echo htmlspecialchars($patient->patient_name ?? ('Pessoa #' . ($patient->person_id ?? '')), ENT_QUOTES, 'UTF-8'); ?></strong>
                                            <span>Person ID: <?php echo (int)($patient->person_id ?? 0); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($patient->patient_contact ?? 'Sem contacto', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php if (!empty($patient->insurance_id)): ?>
                                            <span class="clinical-inline-badge"><?php echo (int)$patient->insurance_id; ?></span>
                                        <?php else: ?>
                                            <span class="clinical-inline-badge clinical-inline-badge-muted">Sem seguro</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?patient_id=<?php echo (int)($patient->Id ?? 0); ?><?php echo !empty($search) ? '&s=' . urlencode($search) : ''; ?>" class="clinical-open-link <?php echo $rowSelected ? 'is-active' : ''; ?>">
                                            <?php echo $rowSelected ? 'Selecionado' : 'Abrir'; ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="clinical-empty-state">Nenhum paciente encontrado para os filtros atuais.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="clinical-pagination">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?php echo $currentPage - 1; ?><?php echo !empty($search) ? '&s=' . urlencode($search) : ''; ?><?php echo $selectedPatientId ? '&patient_id=' . $selectedPatientId : ''; ?>" class="clinical-page-link clinical-page-link-nav">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php if ($windowStart > 1): ?>
                        <a href="?page=1<?php echo !empty($search) ? '&s=' . urlencode($search) : ''; ?><?php echo $selectedPatientId ? '&patient_id=' . $selectedPatientId : ''; ?>" class="clinical-page-link">1</a>
                        <?php if ($windowStart > 2): ?>
                            <span class="clinical-page-gap">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($pageNumber = $windowStart; $pageNumber <= $windowEnd; $pageNumber++): ?>
                        <a href="?page=<?php echo $pageNumber; ?><?php echo !empty($search) ? '&s=' . urlencode($search) : ''; ?><?php echo $selectedPatientId ? '&patient_id=' . $selectedPatientId : ''; ?>" class="clinical-page-link <?php echo $pageNumber === $currentPage ? 'is-active' : ''; ?>">
                            <?php echo $pageNumber; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($windowEnd < $totalPages): ?>
                        <?php if ($windowEnd < $totalPages - 1): ?>
                            <span class="clinical-page-gap">...</span>
                        <?php endif; ?>
                        <a href="?page=<?php echo $totalPages; ?><?php echo !empty($search) ? '&s=' . urlencode($search) : ''; ?><?php echo $selectedPatientId ? '&patient_id=' . $selectedPatientId : ''; ?>" class="clinical-page-link"><?php echo $totalPages; ?></a>
                    <?php endif; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?php echo $currentPage + 1; ?><?php echo !empty($search) ? '&s=' . urlencode($search) : ''; ?><?php echo $selectedPatientId ? '&patient_id=' . $selectedPatientId : ''; ?>" class="clinical-page-link clinical-page-link-nav">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="clinical-side-column">
        <div class="settings-card settings-card-form clinical-workspace-card">
            <div class="card-header">
                <h3><i class="fas fa-notes-medical"></i>Pedido e Encaminhamento</h3>
            </div>

            <div class="settings-card-body">
                <?php if (!empty($selectedPatient)): ?>
                    <div class="settings-form-intro">
                        <div class="settings-form-badge">Paciente ativo</div>
                        <p>
                            Registe aqui o serviço solicitado. Se o pedido for consulta, o balcão pode já indicar o médico que irá atender e agendar o horário, deixando o caso pronto para o trabalho clínico.
                        </p>
                    </div>

                    <div class="clinical-patient-summary">
                        <div>
                            <strong><?php echo htmlspecialchars($selectedPatient->patient_name ?? 'Paciente', ENT_QUOTES, 'UTF-8'); ?></strong>
                            <span>Processo: <?php echo htmlspecialchars($selectedPatient->medical_record_number ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div>
                            <strong><?php echo htmlspecialchars($selectedPatient->patient_contact ?? 'Sem contacto', ENT_QUOTES, 'UTF-8'); ?></strong>
                            <span><?php echo htmlspecialchars($selectedPatient->patient_email ?? 'Sem email registado', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>

                    <div class="clinical-finance-metrics">
                        <div class="clinical-metric-card">
                            <span>Total faturado</span>
                            <strong><?php echo str_price((float)($financialSummary->total_invoiced ?? 0)); ?></strong>
                        </div>
                        <div class="clinical-metric-card">
                            <span>Total pago</span>
                            <strong><?php echo str_price((float)($financialSummary->total_paid ?? 0)); ?></strong>
                        </div>
                        <div class="clinical-metric-card clinical-metric-card-alert">
                            <span>Em aberto</span>
                            <strong><?php echo str_price((float)($financialSummary->total_outstanding ?? 0)); ?></strong>
                        </div>
                        <div class="clinical-metric-card">
                            <span>Rascunhos</span>
                            <strong><?php echo (int)($financialSummary->draft_invoices ?? 0); ?></strong>
                        </div>
                    </div>

                    <form method="post" class="settings-form js-ajax-form">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="action" value="create_draft_invoice">
                        <input type="hidden" name="patient_id" value="<?php echo $selectedPatientId; ?>">

                        <div class="settings-form-row settings-form-row-2">
                            <div class="form-group">
                                <label for="service_id">Serviço solicitado</label>
                                <select id="service_id" name="service_id">
                                    <option value="">Selecione o serviço</option>
                                    <?php foreach (($services ?? []) as $service): ?>
                                        <option value="<?php echo (int)($service->Id ?? 0); ?>" data-price="<?php echo htmlspecialchars((string)($service->default_price ?? 0), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($service->name ?? 'Serviço', ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="doctor_id">Médico do atendimento</label>
                                <select id="doctor_id" name="doctor_id">
                                    <option value="">Sem médico definido no balcão</option>
                                    <?php foreach (($doctors ?? []) as $doctor): ?>
                                        <option value="<?php echo (int)($doctor->Id ?? 0); ?>"><?php echo htmlspecialchars($doctor->doctor_name ?? ('Médico #' . (int)($doctor->Id ?? 0)), ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="settings-form-row">
                            <div class="form-group">
                                <label for="invoice_description">Descrição do rascunho</label>
                                <input type="text" id="invoice_description" name="invoice_description" placeholder="Ex.: Consulta, exame, pacote de tratamento" required>
                            </div>
                        </div>

                        <div class="settings-form-row settings-form-row-2">
                            <div class="form-group">
                                <label for="invoice_amount">Valor inicial</label>
                                <input type="number" step="0.01" min="0.01" id="invoice_amount" name="invoice_amount" placeholder="0.00" required>
                            </div>

                            <div class="form-group">
                                <label for="scheduled_at">Quando o atendimento deve começar</label>
                                <input type="datetime-local" id="scheduled_at" name="scheduled_at">
                            </div>
                        </div>

                        <div class="settings-form-row">
                            <div class="form-group">
                                <label for="invoice_notes">Notas internas</label>
                                <textarea id="invoice_notes" name="invoice_notes" rows="3" placeholder="Observações da receção, enquadramento do pedido, prioridade ou informação útil para o médico e a faturação."></textarea>
                            </div>
                        </div>

                        <button type="submit" class="settings-submit-button js-ajax-submit" data-loading-text="A criar rascunho...">
                            <i class="fas fa-file-medical"></i>
                            <span>Registar pedido do paciente</span>
                        </button>
                    </form>

                    <div class="clinical-history-block">
                        <div class="clinical-history-head">
                            <h4>Leitura financeira do processo</h4>
                            <span>Base para receção e atendimento</span>
                        </div>
                    </div>

                    <div class="clinical-history-block">
                        <div class="clinical-history-head">
                            <h4>Histórico de Faturas</h4>
                            <span><?php echo (int)($financialSummary->total_invoices ?? 0); ?> registos</span>
                        </div>
                        <?php if (!empty($invoiceHistory)): ?>
                            <div class="clinical-history-list">
                                <?php foreach ($invoiceHistory as $invoice): ?>
                                    <div class="clinical-history-item">
                                        <div>
                                            <strong><?php echo htmlspecialchars($invoice->invoice_number ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                                            <span><?php echo date('d/m/Y H:i', strtotime($invoice->invoice_date ?? 'now')); ?></span>
                                        </div>
                                        <div>
                                            <span class="clinical-status-badge status-<?php echo htmlspecialchars($invoice->status ?? 'draft', ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($invoice->status ?? 'draft', ENT_QUOTES, 'UTF-8'); ?></span>
                                            <strong><?php echo str_price((float)($invoice->outstanding_balance ?? 0)); ?></strong>
                                            <?php if (($invoice->status ?? null) === 'draft') : ?>
                                                <form method="post" class="clinical-inline-actions js-ajax-form" data-confirm-title="Eliminar rascunho" data-confirm-message="Este rascunho será removido da ficha do paciente e do fiscal. Esta ação não pode ser anulada." data-confirm-label="Eliminar rascunho">
                                                    <?php echo csrf_input(); ?>
                                                    <input type="hidden" name="action" value="delete_draft_invoice">
                                                    <input type="hidden" name="patient_id" value="<?php echo $selectedPatientId; ?>">
                                                    <input type="hidden" name="invoice_id" value="<?php echo (int)($invoice->Id ?? 0); ?>">
                                                    <button type="submit" class="clinical-action-button clinical-action-button-danger js-ajax-submit" data-loading-text="A eliminar...">
                                                        <i class="fas fa-trash-alt"></i>
                                                        <span>Eliminar rascunho</span>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="settings-form-help">Ainda não existem faturas ligadas a este paciente.</p>
                        <?php endif; ?>
                    </div>

                    <div class="clinical-history-block">
                        <div class="clinical-history-head">
                            <h4>Pagamentos e Prestações</h4>
                            <span>Movimentos recentes</span>
                        </div>
                        <?php if (!empty($paymentHistory)): ?>
                            <div class="clinical-history-list">
                                <?php foreach ($paymentHistory as $payment): ?>
                                    <div class="clinical-history-item">
                                        <div>
                                            <strong><?php echo str_price((float)($payment->amount ?? 0)); ?></strong>
                                            <span><?php echo date('d/m/Y H:i', strtotime($payment->payment_date ?? 'now')); ?></span>
                                        </div>
                                        <div>
                                            <span><?php echo htmlspecialchars($payment->method ?? 'Método não informado', ENT_QUOTES, 'UTF-8'); ?></span>
                                            <strong><?php echo htmlspecialchars($payment->invoice_number ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="settings-form-help">Sem pagamentos registados para este paciente até ao momento.</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="clinical-workspace-empty">
                        <div class="settings-form-badge">Sem paciente selecionado</div>
                        <h4>Selecione um paciente da lista</h4>
                        <p>
                            Ao abrir um paciente, este painel passa a iniciar o pedido de serviço, o eventual acoplamento do médico e a leitura financeira que sustenta o atendimento.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="settings-card clinical-register-card">
            <div class="card-header">
                <h3><i class="fas fa-user-plus"></i>Registar Novo Paciente</h3>
            </div>

            <div class="settings-card-body">
                <div class="settings-form-intro">
                    <div class="settings-form-badge">Registo</div>
                    <p>
                        Só registe novo paciente depois de confirmar que ele ainda não existe na lista à esquerda. Se existir, use o mesmo registo para preservar histórico clínico e financeiro.
                    </p>
                </div>

                <form id="patientForm" method="post" class="settings-form js-ajax-form">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="action" value="save_patient">
                    <input type="hidden" name="id" id="patient_id" value="<?php echo $selectedPatientId ?: ''; ?>">

                    <div class="settings-form-row settings-form-row-2">
                        <div class="form-group">
                            <label for="person_id">Person ID</label>
                            <input type="number" name="person_id" id="person_id" required placeholder="Identificador da pessoa na base clínica" value="<?php echo htmlspecialchars($selectedPatient->person_id ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="medical_record_number">Nº Processo</label>
                            <input type="text" name="medical_record_number" id="medical_record_number" required placeholder="Ex.: CVN-2026-00421" value="<?php echo htmlspecialchars($selectedPatient->medical_record_number ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="settings-form-row">
                        <div class="form-group">
                            <label for="insurance_id">ID Seguro (opcional)</label>
                            <input type="number" name="insurance_id" id="insurance_id" placeholder="Preencha apenas se houver cobertura" value="<?php echo htmlspecialchars($selectedPatient->insurance_id ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <div class="settings-form-row">
                        <div class="form-group">
                            <label for="notes">Observações</label>
                            <textarea name="notes" id="notes" rows="4" placeholder="Notas operacionais, observações do cadastro ou referência para a equipa clínica."><?php echo htmlspecialchars($selectedPatient->notes ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="settings-submit-button js-ajax-submit" data-loading-text="A guardar paciente...">
                        <i class="fas fa-save"></i>
                        <span><?php echo $selectedPatientId ? 'Atualizar Paciente' : 'Guardar Paciente'; ?></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var serviceSelect = document.getElementById('service_id');
    var descriptionInput = document.getElementById('invoice_description');
    var amountInput = document.getElementById('invoice_amount');

    if (!serviceSelect || !descriptionInput || !amountInput) {
        return;
    }

    serviceSelect.addEventListener('change', function () {
        var option = serviceSelect.options[serviceSelect.selectedIndex];
        if (!option || !option.value) {
            return;
        }

        if (!descriptionInput.value.trim()) {
            descriptionInput.value = option.text.trim();
        }

        var price = option.getAttribute('data-price');
        if (price && Number(price) > 0) {
            amountInput.value = Number(price).toFixed(2);
        }
    });
});
</script>