<?php $this->layout("_panel"); ?>

<div class="top-bar">
    <div class="page-title">
        <h2>Menus Principais</h2>
        <p>Configuração dos menus principais exibidos na barra lateral</p>
    </div>

    <div class="top-bar-actions">
        <div class="date-display">
            <i class="far fa-calendar-alt"></i>
            <span><?= date('d/m/Y'); ?></span>
        </div>
    </div>
</div>

<div class="dashboard-grid settings-grid settings-grid-split">
    <!-- Lista de Menus Principais -->
    <div class="card settings-card settings-card-table">
        <div class="card-header">
            <h3><i class="fas fa-bars" style="margin-right:8px; color:var(--accent-color);"></i>Lista de Menus</h3>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Ícone</th>
                        <th>Rota Base</th>
                        <th>Ordem</th>
                        <th>Ativo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $list = $menus ?? []; ?>
                    <?php if (!empty($list)): ?>
                        <?php foreach ($list as $menu): ?>
                            <tr>
                                <td><?= $menu->Id; ?></td>
                                <td><?= htmlspecialchars($menu->name, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($menu->icon ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($menu->base_route ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= $menu->menu_order ?? ''; ?></td>
                                <td><?= !empty($menu->status) ? 'Sim' : 'Não'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Nenhum menu principal configurado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Formulário de Menu Principal -->
    <div class="card settings-card settings-card-form">
        <div class="card-header">
            <h3><i class="fas fa-plus" style="margin-right:8px; color:var(--accent-color);"></i>Novo / Editar Menu</h3>
        </div>
        <div class="card-body settings-card-body">
            <div class="settings-form-intro">
                <span class="settings-form-badge">Navegação principal</span>
                <p>Defina os blocos centrais do sistema para organizar os módulos clínicos, operacionais e administrativos.</p>
            </div>

            <form class="js-ajax-form settings-form" method="post" action="<?= url('/setting/menus'); ?>">
                <?= csrf_input(); ?>
                <input type="hidden" name="id" id="menu_id" value="" />

                <div class="form-group">
                    <label for="name">Nome do Menu</label>
                    <input type="text" name="name" id="name" placeholder="Ex.: Clínico, Farmácia, Laboratório" required />
                </div>

                <div class="form-group">
                    <label for="icon">Ícone (classe FontAwesome)</label>
                    <input type="text" name="icon" id="icon" placeholder="Ex.: fas fa-clinic-medical" />
                </div>

                <div class="form-group">
                    <label for="base_route">Rota Base</label>
                    <input type="text" name="base_route" id="base_route" placeholder="Ex.: /clinical, /pharmacy" />
                </div>

                <div class="settings-form-row settings-form-row-2">
                    <div class="form-group">
                        <label for="menu_order">Ordem</label>
                        <input type="number" name="menu_order" id="menu_order" min="0" step="1" placeholder="0" />
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
                    <span>Guardar Menu</span>
                </button>
            </form>

            <p class="settings-form-help">
                Este formulário está preparado para submissão AJAX usando o ficheiro <strong>assets/js/app.js</strong>.
                Basta implementar o processamento no método <strong>menus</strong> do módulo de configurações para responder em JSON.
            </p>
        </div>
    </div>
</div>