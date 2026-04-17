<?php $this->layout("_panel"); ?>
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
            <div class="stat-number"><?php echo str_price(48500); ?></div>
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
        <div class="service-price"><?php echo str_price(7500); ?></div>
    </div>
    <div class="service-card">
        <div class="service-icon">
            <i class="fas fa-mortar-pestle"></i>
        </div>
        <div class="service-name">Fitoterapia</div>
        <div class="service-price"><?php echo str_price(5000); ?></div>
    </div>
    <div class="service-card">
        <div class="service-icon">
            <i class="fas fa-hand-holding-heart"></i>
        </div>
        <div class="service-name">Massoterapia</div>
        <div class="service-price"><?php echo str_price(6000); ?></div>
    </div>
    <div class="service-card">
        <div class="service-icon">
            <i class="fas fa-spa"></i>
        </div>
        <div class="service-name">Aromaterapia</div>
        <div class="service-price"><?php echo str_price(4500); ?></div>
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
                    <div class="activity-text">Pagamento recebido - <?php echo str_price(7500); ?></div>
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