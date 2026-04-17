<?php $this->layout("_panel"); ?>

<div class="top-bar">
    <div class="page-title">
        <h2>Grupos de Utilizador</h2>
        <p>Gestão de grupos/perfis para controlo de acesso</p>
    </div>

    <div class="top-bar-actions">
        <div class="date-display">
            <i class="far fa-calendar-alt"></i>
            <span><?= date('d/m/Y'); ?></span>
        </div>
    </div>
</div>

<div class="dashboard-grid settings-grid settings-grid-groups">
    <!-- Lista de Grupos -->
    <div class="card settings-card settings-card-table">
        <div class="card-header">
            <h3><i class="fas fa-user-shield" style="margin-right:8px; color:var(--accent-color);"></i>Lista de Grupos / Perfis</h3>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Ativo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $list = $groups ?? []; ?>
                    <?php if (!empty($list)): ?>
                        <?php foreach ($list as $group): ?>
                            <tr>
                                <td><?= $group->Id; ?></td>
                                <td><?= htmlspecialchars($group->name ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($group->description ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= !empty($group->status) ? 'Sim' : 'Não'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Nenhum grupo de utilizador configurado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Formulário de Grupo -->
    <div class="card settings-card settings-card-form">
        <div class="card-header">
            <h3><i class="fas fa-plus" style="margin-right:8px; color:var(--accent-color);"></i>Novo / Editar Grupo</h3>
        </div>
        <div class="card-body settings-card-body">
            <div class="settings-form-intro">
                <span class="settings-form-badge">Perfis e acessos</span>
                <p>Defina grupos de utilizador para organizar permissões, menus e responsabilidades operacionais.</p>
            </div>

            <form class="js-ajax-form settings-form" method="post" action="<?= url('/setting/groups'); ?>">
                <?= csrf_input(); ?>
                <input type="hidden" name="id" id="group_id" value="" />

                <div class="form-group">
                    <label for="name">Nome do Grupo</label>
                    <input type="text" name="name" id="name" placeholder="Ex.: Recepção, Laboratório, Gestor de Stock" required />
                </div>

                <div class="form-group">
                    <label for="description">Descrição</label>
                    <textarea name="description" id="description" rows="4" placeholder="Descreva as responsabilidades e o nível de acesso deste grupo."></textarea>
                </div>

                <div class="form-group">
                    <label for="status">Ativo</label>
                    <select name="status" id="status">
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>

                <button type="submit" class="settings-submit-button js-ajax-submit" data-loading-text="A guardar...">
                    <i class="fas fa-save"></i>
                    <span>Guardar Grupo</span>
                </button>
            </form>

            <p class="settings-form-help">
                Depois de criar os grupos, pode associá-los a menus/submenus
                usando as tabelas de permissões (GroupMenu, Usermenu, etc.).
            </p>
        </div>
    </div>
</div>