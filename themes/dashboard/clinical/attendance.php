<?php $this->layout('_panel'); ?>

<div class="top-bar">
    <div class="page-title">
        <h2>Pacientes</h2>
        <p>Área clínica orientada ao médico, ao encontro com o paciente e à condução do atendimento</p>
    </div>
</div>

<div class="settings-grid settings-grid-split clinical-patients-grid">
    <div class="settings-card settings-card-form clinical-workspace-card">
        <div class="card-header">
            <h3><i class="fas fa-stethoscope"></i>Atendimento Clínico</h3>
        </div>
        <div class="settings-card-body">
            <?php if (!empty($selectedPatient)) : ?>
                <div class="settings-form-intro">
                    <div class="settings-form-badge">Paciente em atendimento</div>
                    <p>Esta área fica reservada para a consulta, observação clínica, evolução e conduta do médico. O balcão administrativo e financeiro fica separado no menu Balcão.</p>
                </div>

                <div class="clinical-patient-summary">
                    <div>
                        <strong><?= htmlspecialchars($selectedPatient->patient_name ?? 'Paciente', ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span>Processo: <?= htmlspecialchars($selectedPatient->medical_record_number ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div>
                        <strong><?= htmlspecialchars($selectedPatient->patient_contact ?? 'Sem contacto', ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span><?= htmlspecialchars($selectedPatient->patient_email ?? 'Sem email registado', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>

                <div class="clinical-history-list">
                    <div class="clinical-history-item">
                        <div>
                            <strong>Motivo da consulta</strong>
                            <span>Espaço reservado para queixa principal e enquadramento inicial do atendimento.</span>
                        </div>
                    </div>
                    <div class="clinical-history-item">
                        <div>
                            <strong>Observação e evolução</strong>
                            <span>Área prevista para achados clínicos, sinais, notas de evolução e reavaliações.</span>
                        </div>
                    </div>
                    <div class="clinical-history-item">
                        <div>
                            <strong>Plano terapêutico</strong>
                            <span>Zona destinada a exames, prescrição, conduta e encaminhamentos do atendimento.</span>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="clinical-workspace-empty">
                    <div class="settings-form-badge">Sem ficha clínica aberta</div>
                    <h4>Abra o paciente a partir do Balcão ou da busca geral</h4>
                    <p>O menu Pacientes fica reservado ao atendimento médico. Para localizar, registar, listar ou rever o fluxo financeiro do paciente, use o menu Balcão.</p>
                    <a href="<?= url('/desk/patients'); ?>" class="clinical-action-button">Abrir Balcão</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="clinical-side-column">
        <div class="settings-card settings-card-form clinical-workspace-card">
            <div class="card-header">
                <h3><i class="fas fa-user-injured"></i>Regra de Separação</h3>
            </div>
            <div class="settings-card-body">
                <div class="settings-form-intro">
                    <div class="settings-form-badge">Fluxo correto</div>
                    <p>Pacientes serve para atendimento. Balcão serve para localizar, registar, tratar rascunhos, dívida, histórico administrativo e abertura do caso operativo.</p>
                </div>

                <div class="clinical-history-list">
                    <div class="clinical-history-item">
                        <div>
                            <strong>Paciente</strong>
                            <span>consulta, observação, evolução e decisão clínica</span>
                        </div>
                    </div>
                    <div class="clinical-history-item">
                        <div>
                            <strong>Balcão</strong>
                            <span>registo, localização, faturação e leitura financeira do atendimento</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>