<?php
$perfil = $data['perfil'] ?? null;

if (!$perfil) {
    $authUser = \Source\Models\Users\Auth::user();
    $person = $authUser?->person_full("full_name,genre,photo");

    $perfil = [
        "name" => $person->full_name ?? $authUser->user_name ?? "Utilizador",
        "email" => $authUser->user_name ?? "",
        "photo" => $person->photo ?? null,
        "genre" => $person->genre ?? null
    ];
}

$profilePhoto = image($perfil['photo'] ?? null, 40, 40, $perfil['genre'] ?? null);
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isCurrentPath = static function (array $paths) use ($currentPath): bool {
    foreach ($paths as $path) {
        if ($currentPath === $path) {
            return true;
        }
    }

    return false;
};
$canManageMenus = can('menus.manage');
$canManageRoles = can('roles.manage');
$canManageInstitution = can('institution.manage');
$canViewDesk = can_any(['patients.view', 'patients.create', 'patients.update', 'search.global', 'invoices.manage', 'payments.manage']);
$canViewPatients = can_any(['patients.view', 'patients.create', 'patients.update']);
$canViewServices = can_any(['services.manage', 'appointments.manage', 'consultations.open']);
$canViewProducts = can_any(['pharmacy.products.manage', 'pharmacy.sales.create', 'stock.view']);
$canViewFiscal = can_any(['fiscal.view', 'fiscal.documents.view', 'invoices.manage', 'payments.manage']);
$showSettings = $canManageMenus || $canManageRoles || $canManageInstitution;
$isDashboardActive = $isCurrentPath(['/dashboard', '/']);
$isDeskActive = $isCurrentPath(['/desk/patients', '/desk/register', '/clinical/patients', '/search/global']);
$isAttendanceActive = $isCurrentPath(['/clinical/attendance', '/clinical/chart', '/clinical/evolution']);
$isAgendaActive = $isCurrentPath(['/agenda/today', '/agenda/week', '/agenda/create', '/agenda/availability']);
$isServicesActive = $isCurrentPath(['/clinical/services', '/clinical/services/search']);
$isFinanceActive = $isCurrentPath(['/finance/overview', '/finance/revenue', '/finance/expenses', '/finance/reports']);
$isPharmacyActive = $isCurrentPath(['/pharmacy/desk', '/pharmacy/products', '/pharmacy/search']);
$isFiscalActive = $isCurrentPath(['/fiscal/overview', '/fiscal/saft', '/fiscal/series', '/fiscal/certificates', '/fiscal/hash-chain', '/fiscal/documents', '/fiscal/audit', '/fiscal/reports']);
$isReportsActive = $isCurrentPath(['/reports/overview']);
$isSettingsActive = $isCurrentPath(['/setting/menus', '/setting/submenus', '/setting/groups', '/setting/numerador', '/setting/institution']);
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">

            <img src="logo.png" alt="Logo" style="width: 70px; height: 70px;">
        </div>
        <h3>Clínica Videira Nguepe</h3>
        <p>Saúde Natural & Bem-estar</p>
    </div>

    <div class="user-profile-mini">
        <div class="user-avatar">
            <img src="<?= htmlspecialchars($profilePhoto); ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%;">
        </div>
        <div class="user-info">
            <h4><?= htmlspecialchars($perfil['name'] ?? 'Utilizador'); ?> <span class="user-status"></span></h4>
            <p><?= htmlspecialchars($perfil['email'] ?? ''); ?></p>
        </div>
    </div>

    <nav class="nav-menu">
        <!-- Dashboard -->
        <a href="<?= url('/dashboard'); ?>" style="text-decoration: none;">
            <div class="nav-item <?= $isDashboardActive ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard</span>
            </div>
        </a>

        <?php if ($canViewDesk) : ?>
        <div class="nav-item has-submenu <?= $isDeskActive ? 'active open' : ''; ?>" onclick="toggleSubmenu(this)">
            <i class="fas fa-cash-register"></i>
            <span>Balcão</span>
            <i class="fas fa-chevron-down arrow"></i>
        </div>
        <ul class="submenu"<?= $isDeskActive ? ' style="display:block;"' : ''; ?>>
            <li><a href="<?= url('/desk/patients'); ?>" class="<?= $isCurrentPath(['/desk/patients', '/clinical/patients']) ? 'active' : ''; ?>"><i class="fas fa-list"></i> Operações do paciente</a></li>
            <li><a href="<?= url('/desk/register'); ?>" class="<?= $isCurrentPath(['/desk/register']) ? 'active' : ''; ?>"><i class="fas fa-user-plus"></i> Registar paciente</a></li>
            <li><a href="<?= url('/search/global'); ?>" class="<?= $isCurrentPath(['/search/global']) ? 'active' : ''; ?>"><i class="fas fa-search"></i> Busca operacional</a></li>
            <li><a href="<?= url('/fiscal/documents'); ?>" class="<?= $isCurrentPath(['/fiscal/documents', '/fiscal/overview']) ? 'active' : ''; ?>"><i class="fas fa-file-invoice-dollar"></i> Histórico financeiro</a></li>
        </ul>
        <?php endif; ?>

        <!-- Pacientes orientado ao atendimento -->
        <?php if ($canViewPatients) : ?>
        <div class="nav-item has-submenu <?= $isAttendanceActive ? 'active open' : ''; ?>" onclick="toggleSubmenu(this)">
            <i class="fas fa-user-doctor"></i>
            <span>Pacientes</span>
            <i class="fas fa-chevron-down arrow"></i>
        </div>
        <ul class="submenu"<?= $isAttendanceActive ? ' style="display:block;"' : ''; ?>>
            <li><a href="<?= url('/clinical/attendance'); ?>" class="<?= $isCurrentPath(['/clinical/attendance']) ? 'active' : ''; ?>"><i class="fas fa-stethoscope"></i> Atendimento clínico</a></li>
            <li><a href="<?= url('/clinical/chart'); ?>" class="<?= $isCurrentPath(['/clinical/chart']) ? 'active' : ''; ?>"><i class="fas fa-notes-medical"></i> Ficha do paciente</a></li>
            <li><a href="<?= url('/clinical/evolution'); ?>" class="<?= $isCurrentPath(['/clinical/evolution']) ? 'active' : ''; ?>"><i class="fas fa-heartbeat"></i> Evolução e conduta</a></li>
            <li><a href="<?= url('/desk/patients'); ?>" class="<?= $isCurrentPath(['/desk/patients', '/clinical/patients']) ? 'active' : ''; ?>"><i class="fas fa-share-square"></i> Abrir pelo balcão</a></li>
        </ul>
        <?php endif; ?>

        <!-- Agenda com Submenu -->
        <div class="nav-item has-submenu <?= $isAgendaActive ? 'active open' : ''; ?>" onclick="toggleSubmenu(this)">
            <i class="fas fa-calendar-alt"></i>
            <span>Agenda</span>
            <i class="fas fa-chevron-down arrow"></i>
        </div>
        <ul class="submenu"<?= $isAgendaActive ? ' style="display:block;"' : ''; ?>>
            <li><a href="<?= url('/agenda/today'); ?>" class="<?= $isCurrentPath(['/agenda/today']) ? 'active' : ''; ?>"><i class="fas fa-calendar-day"></i> Consultas Hoje</a></li>
            <li><a href="<?= url('/agenda/week'); ?>" class="<?= $isCurrentPath(['/agenda/week']) ? 'active' : ''; ?>"><i class="fas fa-calendar-week"></i> Agenda Semanal</a></li>
            <li><a href="<?= url('/agenda/create'); ?>" class="<?= $isCurrentPath(['/agenda/create']) ? 'active' : ''; ?>"><i class="fas fa-calendar-plus"></i> Nova Consulta</a></li>
            <li><a href="<?= url('/agenda/availability'); ?>" class="<?= $isCurrentPath(['/agenda/availability']) ? 'active' : ''; ?>"><i class="fas fa-clock"></i> Horários Disponíveis</a></li>
        </ul>

        <!-- Serviços de Naturopatia com Submenu -->
        <?php if ($canViewServices) : ?>
        <div class="nav-item has-submenu <?= $isServicesActive ? 'active open' : ''; ?>" onclick="toggleSubmenu(this)">
            <i class="fas fa-leaf"></i>
            <span>Serviços</span>
            <i class="fas fa-chevron-down arrow"></i>
        </div>
        <ul class="submenu"<?= $isServicesActive ? ' style="display:block;"' : ''; ?>>
            <li><a href="<?= url('/clinical/services'); ?>" class="<?= $isCurrentPath(['/clinical/services']) ? 'active' : ''; ?>"><i class="fas fa-notes-medical"></i> Catálogo clínico</a></li>
            <li><a href="<?= url('/clinical/services/search'); ?>" class="<?= $isCurrentPath(['/clinical/services/search']) ? 'active' : ''; ?>"><i class="fas fa-search"></i> Buscar serviço</a></li>
        </ul>
        <?php endif; ?>

        <!-- Financeiro com Submenu -->
        <div class="nav-item has-submenu <?= $isFinanceActive ? 'active open' : ''; ?>" onclick="toggleSubmenu(this)">
            <i class="fas fa-dollar-sign"></i>
            <span>Financeiro</span>
            <i class="fas fa-chevron-down arrow"></i>
        </div>
        <ul class="submenu"<?= $isFinanceActive ? ' style="display:block;"' : ''; ?>>
            <li><a href="<?= url('/finance/overview'); ?>" class="<?= $isCurrentPath(['/finance/overview']) ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Resumo Financeiro</a></li>
            <li><a href="<?= url('/finance/revenue'); ?>" class="<?= $isCurrentPath(['/finance/revenue']) ? 'active' : ''; ?>"><i class="fas fa-receipt"></i> Receitas (Kz)</a></li>
            <li><a href="<?= url('/finance/expenses'); ?>" class="<?= $isCurrentPath(['/finance/expenses']) ? 'active' : ''; ?>"><i class="fas fa-file-invoice"></i> Despesas</a></li>
            <li><a href="<?= url('/finance/reports'); ?>" class="<?= $isCurrentPath(['/finance/reports']) ? 'active' : ''; ?>"><i class="fas fa-file-invoice-dollar"></i> Relatórios</a></li>
        </ul>

        <!-- Produtos Naturais com Submenu -->
        <?php if ($canViewProducts) : ?>
        <div class="nav-item has-submenu <?= $isPharmacyActive ? 'active open' : ''; ?>" onclick="toggleSubmenu(this)">
            <i class="fas fa-capsules"></i>
            <span>Farmácia</span>
            <i class="fas fa-chevron-down arrow"></i>
        </div>
        <ul class="submenu"<?= $isPharmacyActive ? ' style="display:block;"' : ''; ?>>
            <li><a href="<?= url('/pharmacy/desk'); ?>" class="<?= $isCurrentPath(['/pharmacy/desk']) ? 'active' : ''; ?>"><i class="fas fa-pills"></i> Balcão da farmácia</a></li>
            <li><a href="<?= url('/pharmacy/products'); ?>" class="<?= $isCurrentPath(['/pharmacy/products']) ? 'active' : ''; ?>"><i class="fas fa-box-open"></i> Catálogo e ficha</a></li>
            <li><a href="<?= url('/pharmacy/search'); ?>" class="<?= $isCurrentPath(['/pharmacy/search']) ? 'active' : ''; ?>"><i class="fas fa-search"></i> Buscar produto</a></li>

        </ul>
        <?php endif; ?>

        <?php if ($canViewFiscal) : ?>
            <div class="nav-item has-submenu <?= $isFiscalActive ? 'active open' : ''; ?>" onclick="toggleSubmenu(this)">
                <i class="fas fa-receipt"></i>
                <span>Fiscal</span>
                <i class="fas fa-chevron-down arrow"></i>
            </div>
            <ul class="submenu"<?= $isFiscalActive ? ' style="display:block;"' : ''; ?>>
                <li><a href="<?= url('/fiscal/saft'); ?>" class="<?= $isCurrentPath(['/fiscal/saft']) ? 'active' : ''; ?>"><i class="fas fa-file-export"></i> SAF-T AO</a></li>
                <li><a href="<?= url('/fiscal/series'); ?>" class="<?= $isCurrentPath(['/fiscal/series']) ? 'active' : ''; ?>"><i class="fas fa-list-ol"></i> Séries Fiscais</a></li>
                <li><a href="<?= url('/fiscal/certificates'); ?>" class="<?= $isCurrentPath(['/fiscal/certificates']) ? 'active' : ''; ?>"><i class="fas fa-certificate"></i> Certificados Digitais</a></li>
                <li><a href="<?= url('/fiscal/hash-chain'); ?>" class="<?= $isCurrentPath(['/fiscal/hash-chain']) ? 'active' : ''; ?>"><i class="fas fa-link"></i> Hash Chain</a></li>
                <li><a href="<?= url('/fiscal/documents'); ?>" class="<?= $isCurrentPath(['/fiscal/documents', '/fiscal/overview']) ? 'active' : ''; ?>"><i class="fas fa-file-invoice-dollar"></i> Documentos Fiscais</a></li>
                <li><a href="<?= url('/fiscal/audit'); ?>" class="<?= $isCurrentPath(['/fiscal/audit']) ? 'active' : ''; ?>"><i class="fas fa-shield-alt"></i> Auditoria Fiscal</a></li>
                <li><a href="<?= url('/fiscal/reports'); ?>" class="<?= $isCurrentPath(['/fiscal/reports']) ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Relatórios Fiscais</a></li>
            </ul>
        <?php endif; ?>

        <!-- Relatórios -->
        <a href="<?= url('/reports/overview'); ?>" style="text-decoration: none;">
            <div class="nav-item <?= $isReportsActive ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Relatórios</span>
            </div>
        </a>

        <div class="nav-divider"></div>

        <!-- Configurações com Submenu (rotas estáticas do módulo Setting) -->
        <?php if ($showSettings) : ?>
            <div class="nav-item has-submenu <?= $isSettingsActive ? 'active open' : ''; ?>" onclick="toggleSubmenu(this)">
                <i class="fas fa-cog"></i>
                <span>Configurações</span>
                <i class="fas fa-chevron-down arrow"></i>
            </div>
            <ul class="submenu"<?= $isSettingsActive ? ' style="display:block;"' : ''; ?>>
                <?php if ($canManageMenus) : ?>
                    <li><a href="<?= url('/setting/menus'); ?>" class="<?= $isCurrentPath(['/setting/menus']) ? 'active' : ''; ?>"><i class="fas fa-list"></i> Menus principais</a></li>
                    <li><a href="<?= url('/setting/submenus'); ?>" class="<?= $isCurrentPath(['/setting/submenus']) ? 'active' : ''; ?>"><i class="fas fa-stream"></i> Submenus</a></li>
                <?php endif; ?>
                <?php if ($canManageRoles) : ?>
                    <li><a href="<?= url('/setting/groups'); ?>" class="<?= $isCurrentPath(['/setting/groups']) ? 'active' : ''; ?>"><i class="fas fa-users-cog"></i> Grupos de utilizador</a></li>
                <?php endif; ?>
                <?php if ($canManageInstitution) : ?>
                    <li><a href="<?= url('/setting/numerador'); ?>" class="<?= $isCurrentPath(['/setting/numerador']) ? 'active' : ''; ?>"><i class="fas fa-sort-numeric-up"></i> Numeração</a></li>
                    <li><a href="<?= url('/setting/institution'); ?>" class="<?= $isCurrentPath(['/setting/institution']) ? 'active' : ''; ?>"><i class="fas fa-clinic-medical"></i> Dados institucionais</a></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>

        <!-- Logout -->
        <a href="<?= url('/auth/logout'); ?>" style="text-decoration: none;">
            <div class="nav-item logout-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </div>
        </a>
    </nav>
</aside>