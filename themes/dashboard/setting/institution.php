<?php $this->layout("_panel"); ?>

<div class="top-bar">
    <div class="page-title">
        <h2>Dados Institucionais</h2>
        <p>Configuração dos dados da clínica/empresa</p>
    </div>

    <div class="top-bar-actions">
        <div class="date-display">
            <i class="far fa-calendar-alt"></i>
            <span><?= date('d/m/Y'); ?></span>
        </div>
    </div>
</div>

<div class="dashboard-grid settings-grid settings-grid-institution">
    <!-- Formulário de Dados Institucionais -->
    <div class="card settings-card settings-card-form settings-card-form-wide">
        <div class="card-header">
            <h3><i class="fas fa-hospital" style="margin-right:8px; color:var(--accent-color);"></i>Dados da Instituição</h3>
        </div>
        <div class="card-body settings-card-body">
            <div class="settings-form-intro">
                <span class="settings-form-badge">Identidade institucional</span>
                <p>Centralize os dados da clínica para utilização consistente em relatórios, documentos, faturação e branding do sistema.</p>
            </div>

            <form class="js-ajax-form settings-form" method="post" action="<?= url('/setting/institution'); ?>" enctype="multipart/form-data">
                <?= csrf_input(); ?>

                <div class="settings-form-row settings-form-row-2">
                    <div class="form-group">
                        <label for="name">Nome da Clínica / Empresa</label>
                        <input type="text" name="name" id="name" placeholder="Ex.: Clínica Videira Nguepe" required />
                    </div>

                    <div class="form-group">
                        <label for="nif">NIF / Número Fiscal</label>
                        <input type="text" name="nif" id="nif" placeholder="Número fiscal da instituição" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Endereço</label>
                    <textarea name="address" id="address" rows="3" placeholder="Morada completa, bairro e referências da clínica."></textarea>
                </div>

                <div class="settings-form-row settings-form-row-3">
                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input type="text" name="phone" id="phone" placeholder="Ex.: +244 923 000 000" />
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" placeholder="geral@clinica.ao" />
                    </div>

                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="url" name="website" id="website" placeholder="https://www.clinica.ao" />
                    </div>
                </div>

                <div class="settings-form-row settings-form-row-2">
                    <div class="form-group">
                        <label for="logo">Logotipo</label>
                        <input class="settings-file-input" type="file" name="logo" id="logo" accept="image/*" />
                    </div>

                    <div class="form-group">
                        <label for="clinic_code">Código Interno da Clínica</label>
                        <input type="text" name="clinic_code" id="clinic_code" value="<?= $clinicId ?? ''; ?>" placeholder="Código interno da unidade" />
                    </div>
                </div>

                <button type="submit" class="settings-submit-button js-ajax-submit" data-loading-text="A guardar...">
                    <i class="fas fa-save"></i>
                    <span>Guardar Dados</span>
                </button>
            </form>

            <p class="settings-form-help">
                Estes dados podem ser usados em cabeçalhos de relatórios, faturas e
                outras áreas do sistema.
            </p>
        </div>
    </div>

    <!-- Ajuda / Notas -->
    <div class="card settings-card settings-card-note">
        <div class="card-header">
            <h3><i class="fas fa-info-circle" style="margin-right:8px; color:var(--accent-color);"></i>Notas</h3>
        </div>
        <div class="card-body settings-card-body">
            <p>
                Quando ligar este formulário a uma tabela (por exemplo, <strong>clinics</strong>),
                utilize o <strong>clinic_id</strong> atual (<strong><?= $clinicId ?? 0; ?></strong>) para garantir que cada
                instituição mantém os seus próprios dados.
            </p>
        </div>
    </div>
</div>