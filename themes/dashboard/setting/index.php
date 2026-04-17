<?php $this->layout("_panel"); ?>

<div class="top-bar">
    <div class="page-title">
        <h2>Configurações do Sistema</h2>
        <p>Gestão de menus, perfis, numeração e dados institucionais</p>
    </div>

    <div class="top-bar-actions">
        <div class="date-display">
            <i class="far fa-calendar-alt"></i>
            <span><?= date('d/m/Y'); ?></span>
        </div>
    </div>
</div>

<div class="dashboard-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
    <a href="<?= url('/setting/menus'); ?>" class="card" style="text-decoration:none; color:inherit;">
        <div class="card-header">
            <h3><i class="fas fa-bars" style="margin-right:8px; color:var(--accent-color);"></i>Menus Principais</h3>
        </div>
        <div class="card-body">
            <p>Configurar os menus principais exibidos na barra lateral.</p>
        </div>
    </a>

    <a href="<?= url('/setting/submenus'); ?>" class="card" style="text-decoration:none; color:inherit;">
        <div class="card-header">
            <h3><i class="fas fa-list-ul" style="margin-right:8px; color:var(--accent-color);"></i>Submenus / Rotas</h3>
        </div>
        <div class="card-body">
            <p>Configurar submenus, rotas (Controller@action) e permissões básicas.</p>
        </div>
    </a>

    <a href="<?= url('/setting/groups'); ?>" class="card" style="text-decoration:none; color:inherit;">
        <div class="card-header">
            <h3><i class="fas fa-user-shield" style="margin-right:8px; color:var(--accent-color);"></i>Grupos de Utilizador</h3>
        </div>
        <div class="card-body">
            <p>Gerir grupos/perfis de utilizador para controlo de acesso.</p>
        </div>
    </a>

    <a href="<?= url('/setting/numerador'); ?>" class="card" style="text-decoration:none; color:inherit;">
        <div class="card-header">
            <h3><i class="fas fa-hashtag" style="margin-right:8px; color:var(--accent-color);"></i>Numeração Geral</h3>
        </div>
        <div class="card-body">
            <p>Configurar tipos de numeração (faturas, recibos, processos, etc.).</p>
        </div>
    </a>

    <a href="<?= url('/setting/institution'); ?>" class="card" style="text-decoration:none; color:inherit;">
        <div class="card-header">
            <h3><i class="fas fa-hospital" style="margin-right:8px; color:var(--accent-color);"></i>Dados Institucionais</h3>
        </div>
        <div class="card-body">
            <p>Definir dados da clínica/empresa (nome, NIF, contactos, logotipo, etc.).</p>
        </div>
    </a>
</div>