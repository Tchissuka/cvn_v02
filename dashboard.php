<?php
session_start();

// Verificar se está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Dados do usuário da sessão
$user_name = $_SESSION['user_name'] ?? 'Dr. José Nguepe';
$user_role = $_SESSION['user_role'] ?? 'Naturopata Especialista';
$current_page = basename($_SERVER['PHP_SELF']);

// Função para formatar moeda Kz
function formatKz($value) {
    return number_format($value, 2, ',', '.') . ' Kz';
}

// Detectar modo escuro do cookie ou preferência do sistema
$dark_mode = isset($_COOKIE['dark_mode']) ? $_COOKIE['dark_mode'] : 'light';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Videira Nguepe - Sistema de Gestão Naturopática</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #27ae60;
            --accent-hover: #2ecc71;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            
            /* Cores do modo claro (padrão) */
            --bg-color: #f4f7fc;
            --card-bg: #ffffff;
            --text-primary: #2c3e50;
            --text-secondary: #666666;
            --border-color: #e1e1e1;
            --hover-bg: #f5f5f5;
            --shadow-color: rgba(0, 0, 0, 0.05);
            --table-header-bg: #f8f9fa;
            --input-bg: #ffffff;
            --input-border: #e1e1e1;
        }

        /* Modo Escuro */
        body.dark-mode {
            --bg-color: #1a1a1a;
            --card-bg: #2d2d2d;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --border-color: #2c3e50;
            --hover-bg: #3d3d3d;
            --shadow-color: rgba(0, 0, 0, 0.2);
            --table-header-bg: #363636;
            --input-bg: #363636;
            --input-border: #404040;
        }

        body {
            background: var(--bg-color);
            transition: background-color 0.3s, color 0.3s;
            color: var(--text-primary);
             overflow-y: auto;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
        }
        body::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles - mantém as cores originais mesmo no modo escuro */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
             scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        .sidebar::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

        .sidebar.collapsed .sidebar-header h3,
        .sidebar.collapsed .sidebar-header p,
        .sidebar.collapsed .user-info,
        .sidebar.collapsed .nav-item span,
        .sidebar.collapsed .submenu {
            display: none;
        }

        .sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 15px;
        }

        .sidebar.collapsed .nav-item i {
            margin: 0;
            font-size: 20px;
        }

        .sidebar.collapsed .user-profile-mini {
            justify-content: center;
        }

        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: var(--primary-color);
            font-size: 28px;
            font-weight: bold;
            border: 3px solid var(--accent-color);
        }

        .sidebar-header h3 {
            color: white;
            font-size: 18px;
            font-weight: 600;
        }

        .sidebar-header p {
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            margin-top: 5px;
        }

        .user-profile-mini {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: var(--accent-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .user-info h4 {
            color: white;
            font-size: 15px;
            font-weight: 600;
        }

        .user-info p {
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            margin-top: 3px;
        }

        .user-status {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: var(--accent-color);
            border-radius: 50%;
            margin-left: 5px;
        }

        .nav-menu {
            flex: 1;
            padding: 20px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: rgba(255,255,255,0.8);
            border-radius: 10px;
            margin-bottom: 5px;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
        }

        .nav-item:hover {
            background: var(--secondary-color);
            color: white;
        }

        .nav-item.active {
            background: var(--accent-color);
            color: white;
        }

        .nav-item i {
            font-size: 18px;
            width: 24px;
        }

        .nav-item span {
            font-size: 14px;
            font-weight: 500;
        }

        .nav-item .arrow {
            margin-left: auto;
            font-size: 12px;
            transition: transform 0.3s;
        }

        .nav-item.open .arrow {
            transform: rotate(180deg);
        }

        /* Submenu Styles */
        .submenu {
            list-style: none;
            padding-left: 45px;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .submenu li {
            margin-bottom: 5px;
        }

        .submenu a {
            display: block;
            padding: 8px 15px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 13px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .submenu a:hover {
            background: var(--secondary-color);
            color: white;
            padding-left: 20px;
        }

        .submenu a.active {
            color: var(--accent-color);
            font-weight: 500;
        }

        .nav-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 20px 0;
        }

        /* Toggle Sidebar Button */
        .toggle-sidebar {
            position: fixed;
            left: var(--sidebar-width);
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 48px;
            background: var(--card-bg);
            border: 2px solid var(--primary-color);
            border-left: none;
            border-radius: 0 8px 8px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1001;
            transition: left 0.3s ease;
            color: var(--primary-color);
            box-shadow: 2px 0 10px var(--shadow-color);
        }

        .toggle-sidebar.collapsed {
            left: var(--sidebar-collapsed-width);
        }

        .toggle-sidebar:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left 0.3s ease;
             scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
    height: 100vh;
    overflow-y: auto;
        }
.main-content::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

/* Opcional: Adicionar um indicador sutil de scroll no hover (só aparece quando passa o mouse) */
.main-content:hover {
    scrollbar-width: thin; /* Firefox */
    scrollbar-color: var(--accent-color) var(--border-color); /* Firefox */
}

.main-content:hover::-webkit-scrollbar {
    display: block;
    width: 6px;
}

.main-content:hover::-webkit-scrollbar-track {
    background: var(--border-color);
    border-radius: 10px;
}

.main-content:hover::-webkit-scrollbar-thumb {
    background: var(--accent-color);
    border-radius: 10px;
}

.main-content:hover::-webkit-scrollbar-thumb:hover {
    background: var(--accent-hover);
}
        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Top Bar */
        .top-bar {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 15px 25px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px var(--shadow-color);
        }

        .page-title h2 {
            color: var(--text-primary);
            font-size: 22px;
            font-weight: 600;
        }

        .page-title p {
            color: var(--text-secondary);
            font-size: 13px;
            margin-top: 3px;
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .theme-toggle {
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 20px;
            transition: color 0.3s;
        }

        .theme-toggle:hover {
            color: var(--accent-color);
        }

        .notification-icon {
            position: relative;
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 20px;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent-color);
            color: white;
            font-size: 10px;
            padding: 2px 5px;
            border-radius: 10px;
        }

        .date-display {
            color: var(--text-secondary);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 10px var(--shadow-color);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px var(--shadow-color);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .stat-info h3 {
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .stat-trend {
            font-size: 12px;
            color: var(--accent-color);
            margin-top: 5px;
        }

        .stat-trend.negative {
            color: #e74c3c;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px var(--shadow-color);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header h3 {
            color: var(--text-primary);
            font-size: 16px;
            font-weight: 600;
        }

        .card-header a {
            color: var(--accent-color);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        .card-header a:hover {
            text-decoration: underline;
        }

        /* Appointment List */
        .appointment-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .appointment-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            border-radius: 10px;
            transition: background 0.3s;
        }

        .appointment-item:hover {
            background: var(--hover-bg);
        }

        .appointment-time {
            background: rgba(39, 174, 96, 0.1);
            color: var(--accent-color);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            min-width: 60px;
            text-align: center;
        }

        .appointment-info {
            flex: 1;
        }

        .appointment-patient {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 3px;
        }

        .appointment-service {
            color: var(--text-secondary);
            font-size: 12px;
        }

        .appointment-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-confirmed {
            background: rgba(39, 174, 96, 0.1);
            color: var(--accent-color);
        }

        .status-pending {
            background: rgba(241, 196, 15, 0.1);
            color: #f1c40f;
        }

        .status-cancelled {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }

        /* Activity List */
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px;
            border-radius: 8px;
        }

        .activity-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            color: var(--text-primary);
            font-size: 13px;
            margin-bottom: 3px;
        }

        .activity-time {
            color: var(--text-secondary);
            font-size: 11px;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .action-btn {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 15px;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .action-btn:hover {
            background: var(--accent-color);
            color: white;
            border-color: var(--accent-color);
        }

        .action-btn i {
            font-size: 24px;
            color: var(--accent-color);
        }

        .action-btn:hover i {
            color: white;
        }

        .action-btn span {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .action-btn:hover span {
            color: white;
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th {
            text-align: left;
            padding: 12px;
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
            background: var(--table-header-bg);
        }

        td {
            padding: 12px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-color);
        }

        .priority-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            color: white;
        }

        .priority-high {
            background: #e74c3c;
        }

        .priority-medium {
            background: #f39c12;
        }

        .priority-low {
            background: var(--accent-color);
        }

        .action-button {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            transition: background 0.3s;
        }

        .action-button:hover {
            background: var(--accent-hover);
        }

        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .service-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s;
        }

        .service-card:hover {
            transform: translateY(-3px);
            border-color: var(--accent-color);
            box-shadow: 0 5px 15px var(--shadow-color);
        }

        .service-icon {
            width: 50px;
            height: 50px;
            background: rgba(39, 174, 96, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            color: var(--accent-color);
            font-size: 20px;
        }

        .service-name {
            color: var(--text-primary);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .service-price {
            color: var(--accent-color);
            font-size: 16px;
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: var(--sidebar-collapsed-width);
            }
            
            .sidebar:not(.expanded) .sidebar-header h3,
            .sidebar:not(.expanded) .sidebar-header p,
            .sidebar:not(.expanded) .user-info,
            .sidebar:not(.expanded) .nav-item span,
            .sidebar:not(.expanded) .submenu {
                display: none;
            }
            
            .sidebar:not(.expanded) .nav-item {
                justify-content: center;
                padding: 15px;
            }
            
            .sidebar:not(.expanded) .nav-item i {
                margin: 0;
            }
            
            .sidebar:not(.expanded) .user-profile-mini {
                justify-content: center;
            }
            
            .main-content {
                margin-left: var(--sidebar-collapsed-width);
            }
            
            .toggle-sidebar {
                display: none;
            }
            
            .top-bar-actions {
                gap: 10px;
            }
            
            .date-display span:last-child {
                display: none;
            }
        }
    </style>
</head>
<body class="<?php echo $dark_mode; ?>-mode">
    <div class="app-container">
        <!-- Toggle Sidebar Button -->
        <div class="toggle-sidebar" id="toggleSidebar" onclick="toggleSidebar()">
            <i class="fas fa-chevron-left"></i>
        </div>

        <!-- Sidebar -->
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
                    <?php echo substr($user_name, 0, 2); ?>
                </div>
                <div class="user-info">
                    <h4><?php echo htmlspecialchars($user_name); ?> <span class="user-status"></span></h4>
                    <p><?php echo htmlspecialchars($user_role); ?></p>
                </div>
            </div>
            
            <nav class="nav-menu">
                <!-- Dashboard -->
                <a href="dashboard.php" style="text-decoration: none;">
                    <div class="nav-item <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </div>
                </a>
                
                <!-- Pacientes com Submenu -->
                <div class="nav-item has-submenu" onclick="toggleSubmenu(this)">
                    <i class="fas fa-users"></i>
                    <span>Pacientes</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#" class="active"><i class="fas fa-list"></i> Listar Pacientes</a></li>
                    <li><a href="#"><i class="fas fa-user-plus"></i> Novo Paciente</a></li>
                    <li><a href="#"><i class="fas fa-search"></i> Buscar Paciente</a></li>
                    <li><a href="#"><i class="fas fa-history"></i> Histórico</a></li>
                </ul>

                <!-- Agenda com Submenu -->
                <div class="nav-item has-submenu" onclick="toggleSubmenu(this)">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Agenda</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"><i class="fas fa-calendar-day"></i> Consultas Hoje</a></li>
                    <li><a href="#"><i class="fas fa-calendar-week"></i> Agenda Semanal</a></li>
                    <li><a href="#"><i class="fas fa-calendar-plus"></i> Nova Consulta</a></li>
                    <li><a href="#"><i class="fas fa-clock"></i> Horários Disponíveis</a></li>
                </ul>

                <!-- Serviços de Naturopatia com Submenu -->
                <div class="nav-item has-submenu" onclick="toggleSubmenu(this)">
                    <i class="fas fa-leaf"></i>
                    <span>Serviços</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"><i class="fas fa-leaf"></i> Novos serviço</a></li>
                    <li><a href="#"><i class="fas fa-oil-can"></i> Lista de serviço</a></li>
                </ul>

                <!-- Financeiro com Submenu -->
                <div class="nav-item has-submenu" onclick="toggleSubmenu(this)">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Financeiro</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"><i class="fas fa-chart-bar"></i> Resumo Financeiro</a></li>
                    <li><a href="#"><i class="fas fa-receipt"></i> Receitas (Kz)</a></li>
                    <li><a href="#"><i class="fas fa-file-invoice"></i> Despesas</a></li>
                    <li><a href="#"><i class="fas fa-file-invoice-dollar"></i> Relatórios</a></li>
                </ul>

                <!-- Produtos Naturais com Submenu -->
                <div class="nav-item has-submenu" onclick="toggleSubmenu(this)">
                    <i class="fas fa-boxes"></i>
                    <span>Produtos</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"><i class="fas fa-leaf"></i> Novos Produtos</a></li>
                    <li><a href="#"><i class="fas fa-oil-can"></i> Lista de Produtos</a></li>
                  
                </ul>

                <!-- Relatórios -->
                <a href="#" style="text-decoration: none;">
                    <div class="nav-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Relatórios</span>
                    </div>
                </a>
                
                <div class="nav-divider"></div>
                
                <!-- Configurações com Submenu -->
                <div class="nav-item has-submenu" onclick="toggleSubmenu(this)">
                    <i class="fas fa-cog"></i>
                    <span>Configurações</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"><i class="fas fa-user-md"></i> Perfil</a></li>
                    <li><a href="#"><i class="fas fa-lock"></i> Segurança</a></li>
                    <li><a href="#"><i class="fas fa-palette"></i> Aparência</a></li>
                    <li><a href="#"><i class="fas fa-users-cog"></i> Usuários</a></li>
                </ul>
                
                <!-- Logout -->
                <a href="logout.php" style="text-decoration: none;">
                    <div class="nav-item logout-item">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sair</span>
                    </div>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="page-title">
                    <h2>Dashboard</h2>
                    <p>Bem-vindo de volta, Dr. José! 🌿</p>
                </div>
                
                <div class="top-bar-actions">
                    <div class="theme-toggle" onclick="toggleDarkMode()">
                        <i class="fas <?php echo $dark_mode == 'dark' ? 'fa-sun' : 'fa-moon'; ?>"></i>
                    </div>
                    <div class="date-display">
                        <i class="far fa-calendar-alt"></i>
                        <span><?php echo date('d/m/Y'); ?></span>
                    </div>
                    <div class="notification-icon">
                        <i class="far fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </div>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Pacientes Hoje</h3>
                        <div class="stat-number">24</div>
                        <div class="stat-trend">
                            <i class="fas fa-arrow-up"></i> +12% que ontem
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Consultas</h3>
                        <div class="stat-number">18</div>
                        <div class="stat-trend">
                            <i class="fas fa-clock"></i> 8 agendadas
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Faturamento Hoje</h3>
                        <div class="stat-number"><?php echo formatKz(48500); ?></div>
                        <div class="stat-trend">
                            <i class="fas fa-arrow-up"></i> +5% meta
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Em Espera</h3>
                        <div class="stat-number">6</div>
                        <div class="stat-trend negative">
                            <i class="fas fa-arrow-down"></i> 3 urgentes
                        </div>
                    </div>
                </div>
            </div>

            <!-- Serviços de Naturopatia em Destaque -->
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="service-name">Acupuntura</div>
                    <div class="service-price"><?php echo formatKz(7500); ?></div>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-mortar-pestle"></i>
                    </div>
                    <div class="service-name">Fitoterapia</div>
                    <div class="service-price"><?php echo formatKz(5000); ?></div>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="service-name">Massoterapia</div>
                    <div class="service-price"><?php echo formatKz(6000); ?></div>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-spa"></i>
                    </div>
                    <div class="service-name">Aromaterapia</div>
                    <div class="service-price"><?php echo formatKz(4500); ?></div>
                </div>
            </div>
            
            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Próximas Consultas -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="far fa-clock" style="margin-right: 8px; color: var(--accent-color);"></i>Próximas Consultas</h3>
                        <a href="#">Ver todas <i class="fas fa-arrow-right"></i></a>
                    </div>
                    
                    <div class="appointment-list">
                        <div class="appointment-item">
                            <span class="appointment-time">09:00</span>
                            <div class="appointment-info">
                                <div class="appointment-patient">Maria Silva</div>
                                <div class="appointment-service">Acupuntura - Dr. José</div>
                            </div>
                            <span class="appointment-status status-confirmed">Confirmado</span>
                        </div>
                        
                        <div class="appointment-item">
                            <span class="appointment-time">10:30</span>
                            <div class="appointment-info">
                                <div class="appointment-patient">João Santos</div>
                                <div class="appointment-service">Fitoterapia - Dr. José</div>
                            </div>
                            <span class="appointment-status status-confirmed">Confirmado</span>
                        </div>
                        
                        <div class="appointment-item">
                            <span class="appointment-time">14:00</span>
                            <div class="appointment-info">
                                <div class="appointment-patient">Pedro Oliveira</div>
                                <div class="appointment-service">Massoterapia - Dr. José</div>
                            </div>
                            <span class="appointment-status status-pending">Pendente</span>
                        </div>
                        
                        <div class="appointment-item">
                            <span class="appointment-time">15:30</span>
                            <div class="appointment-info">
                                <div class="appointment-patient">Ana Costa</div>
                                <div class="appointment-service">Aromaterapia - Dr. José</div>
                            </div>
                            <span class="appointment-status status-confirmed">Confirmado</span>
                        </div>
                        
                        <div class="appointment-item">
                            <span class="appointment-time">16:45</span>
                            <div class="appointment-info">
                                <div class="appointment-patient">Roberto Alves</div>
                                <div class="appointment-service">Nutrição Natural - Dr. José</div>
                            </div>
                            <span class="appointment-status status-pending">Pendente</span>
                        </div>
                    </div>
                </div>
                
                <!-- Atividades Recentes -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-history" style="margin-right: 8px; color: var(--accent-color);"></i>Atividades Recentes</h3>
                        <a href="#">Ver todas <i class="fas fa-arrow-right"></i></a>
                    </div>
                    
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-dot" style="background: var(--accent-color);"></div>
                            <div class="activity-content">
                                <div class="activity-text">Nova consulta agendada - Maria Souza</div>
                                <div class="activity-time">Há 5 minutos</div>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-dot" style="background: #f39c12;"></div>
                            <div class="activity-content">
                                <div class="activity-text">Pagamento recebido - <?php echo formatKz(7500); ?></div>
                                <div class="activity-time">Há 15 minutos</div>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-dot" style="background: #3498db;"></div>
                            <div class="activity-content">
                                <div class="activity-text">Prescrição fitoterápica disponível</div>
                                <div class="activity-time">Há 30 minutos</div>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-dot" style="background: #e74c3c;"></div>
                            <div class="activity-content">
                                <div class="activity-text">Paciente cancelou consulta</div>
                                <div class="activity-time">Há 1 hora</div>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-dot" style="background: var(--accent-color);"></div>
                            <div class="activity-content">
                                <div class="activity-text">Novo paciente cadastrado</div>
                                <div class="activity-time">Há 2 horas</div>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-dot" style="background: #9b59b6;"></div>
                            <div class="activity-content">
                                <div class="activity-text">Receita de chá medicinal emitida</div>
                                <div class="activity-time">Há 3 horas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <div class="action-btn" onclick="window.location.href='#'">
                    <i class="fas fa-user-plus"></i>
                    <span>Novo Paciente</span>
                </div>
                <div class="action-btn" onclick="window.location.href='#'">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Nova Consulta</span>
                </div>
                <div class="action-btn" onclick="window.location.href='#'">
                    <i class="fas fa-leaf"></i>
                    <span>Prescrição Natural</span>
                </div>
                <div class="action-btn" onclick="window.location.href='#'">
                    <i class="fas fa-flask"></i>
                    <span>Análise Natural</span>
                </div>
                <div class="action-btn" onclick="window.location.href='#'">
                    <i class="fas fa-chart-line"></i>
                    <span>Relatórios</span>
                </div>
                <div class="action-btn" onclick="window.location.href='#'">
                    <i class="fas fa-cog"></i>
                    <span>Configurações</span>
                </div>
            </div>
            
            <!-- Pacientes em Espera -->
            <div style="margin-top: 20px;">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-procedures" style="margin-right: 8px; color: var(--accent-color);"></i>Pacientes em Espera</h3>
                        <a href="#">Gerenciar fila <i class="fas fa-arrow-right"></i></a>
                    </div>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Chegada</th>
                                    <th>Prioridade</th>
                                    <th>Serviço</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Carlos Ferreira</td>
                                    <td>08:45</td>
                                    <td><span class="priority-badge priority-high">Alta</span></td>
                                    <td>Acupuntura</td>
                                    <td><span class="appointment-status status-pending">Aguardando</span></td>
                                    <td>
                                        <button class="action-button">Chamar</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Mariana Lima</td>
                                    <td>09:15</td>
                                    <td><span class="priority-badge priority-medium">Média</span></td>
                                    <td>Fitoterapia</td>
                                    <td><span class="appointment-status status-pending">Aguardando</span></td>
                                    <td>
                                        <button class="action-button">Chamar</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Roberto Alves</td>
                                    <td>09:30</td>
                                    <td><span class="priority-badge priority-low">Baixa</span></td>
                                    <td>Massoterapia</td>
                                    <td><span class="appointment-status status-pending">Aguardando</span></td>
                                    <td>
                                        <button class="action-button">Chamar</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Helena Gomes</td>
                                    <td>09:45</td>
                                    <td><span class="priority-badge priority-high">Alta</span></td>
                                    <td>Aromaterapia</td>
                                    <td><span class="appointment-status status-pending">Aguardando</span></td>
                                    <td>
                                        <button class="action-button">Chamar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('toggleSidebar');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            toggleBtn.classList.toggle('collapsed');
            
            // Change arrow direction
            const arrow = toggleBtn.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                arrow.classList.remove('fa-chevron-left');
                arrow.classList.add('fa-chevron-right');
            } else {
                arrow.classList.remove('fa-chevron-right');
                arrow.classList.add('fa-chevron-left');
            }
        }
        
        // Toggle Dark Mode
        function toggleDarkMode() {
            const body = document.body;
            const themeIcon = document.querySelector('.theme-toggle i');
            
            if (body.classList.contains('dark-mode')) {
                body.classList.remove('dark-mode');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                document.cookie = "dark_mode=light; path=/; max-age=" + 60*60*24*365;
            } else {
                body.classList.add('dark-mode');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                document.cookie = "dark_mode=dark; path=/; max-age=" + 60*60*24*365;
            }
        }
        
        // Toggle Submenu
        function toggleSubmenu(element) {
            element.classList.toggle('open');
            const submenu = element.nextElementSibling;
            
            if (submenu.style.display === 'block') {
                submenu.style.display = 'none';
            } else {
                submenu.style.display = 'block';
            }
        }
        
        // Initialize submenus (closed by default)
        document.addEventListener('DOMContentLoaded', function() {
            const submenus = document.querySelectorAll('.submenu');
            submenus.forEach(submenu => {
                submenu.style.display = 'none';
            });
        });
        
        // Mark active menu item
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const menuLinks = document.querySelectorAll('.nav-menu a');
            
            menuLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath.split('/').pop()) {
                    link.classList.add('active');
                }
            });
        });
        
        // Handle responsive behavior
        function handleResize() {
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('mainContent').classList.add('expanded');
            }
        }
        
        window.addEventListener('resize', handleResize);
        handleResize();
    </script>
</body>
</html>