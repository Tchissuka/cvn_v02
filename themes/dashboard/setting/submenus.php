<?php $this->layout("_panel"); ?>

<div class="top-bar">
    <div class="page-title">
        <h2>Submenus / Rotas</h2>
        <p>Configuração de submenus e rotas (Controller@action)</p>
    </div>

    <div class="top-bar-actions">
        <div class="date-display">
            <i class="far fa-calendar-alt"></i>
            <span><?= date('d/m/Y'); ?></span>
        </div>
    </div>
</div>

<div class="dashboard-grid settings-grid settings-grid-split">
    <!-- Lista de Submenus -->
    <div class="card settings-card settings-card-table">
        <div class="card-header">
            <h3><i class="fas fa-list-ul" style="margin-right:8px; color:var(--accent-color);"></i>Lista de Submenus</h3>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Menu Principal</th>
                        <th>Nome</th>
                        <th>Rota (Controller@action)</th>
                        <th>Ordem</th>
                        <th>Ativo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $list = $submenus ?? []; ?>
                    <?php if (!empty($list)): ?>
                        <?php foreach ($list as $submenu): ?>
                            <tr>
                                <td><?= $submenu->Id; ?></td>
                                <td><?= htmlspecialchars($submenu->main_menu_name ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($submenu->name, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($submenu->route ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= $submenu->submenu_order ?? ''; ?></td>
                                <td><?= !empty($submenu->status) ? 'Sim' : 'Não'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Nenhum submenu configurado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Formulário de Submenu -->
    <div class="card settings-card settings-card-form">
        <div class="card-header">
            <h3><i class="fas fa-plus" style="margin-right:8px; color:var(--accent-color);"></i>Novo / Editar Submenu</h3>
        </div>
        <div class="card-body settings-card-body">
            <div class="settings-form-intro">
                <span class="settings-form-badge">Rotas e ações</span>
                <p>Associe cada submenu ao método correto para manter a navegação operacional consistente e previsível.</p>
            </div>

            <form class="js-ajax-form settings-form" method="post" action="<?= url('/setting/submenus'); ?>">
                <?= csrf_input(); ?>
                <input type="hidden" name="id" id="submenu_id" value="" />

                <div class="form-group">
                    <label for="main_menu_id">Menu Principal</label>
                    <select name="main_menu_id" id="main_menu_id" required>
                        <option value="">Selecione...</option>
                        <?php $menusList = $menus ?? []; ?>
                        <?php foreach ($menusList as $menu): ?>
                            <option value="<?= $menu->Id; ?>"><?= htmlspecialchars($menu->name, ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="name">Nome do Submenu</label>
                    <input type="text" name="name" id="name" placeholder="Ex.: Resultados, Estoque, Venda de Balcão" required />
                </div>

                <div class="form-group">
                    <label for="route">Rota (Controller@action)</label>
                    <input type="text" name="route" id="route" placeholder="Ex.: Clinical@patients, Pharmacy@sales" required />
                </div>

                <div class="settings-form-row settings-form-row-2">
                    <div class="form-group">
                        <label for="submenu_order">Ordem</label>
                        <input type="number" name="submenu_order" id="submenu_order" min="0" step="1" placeholder="0" />
                    </div>

                    <div class="form-group">
                        <label for="status">Ativo</label>
                        <select name="status" id="status">
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="settings-submit-button js-ajax-submit" data-loading-text="A guardar...">
                    <i class="fas fa-save"></i>
                    <span>Guardar Submenu</span>
                </button>
            </form>

            <p class="settings-form-help">
                Use a notação <strong>Controller@action</strong> para apontar o submenu
                para o método correto (ex.: <strong>Clinical@patients</strong>).
            </p>
        </div>
    </div>
</div>