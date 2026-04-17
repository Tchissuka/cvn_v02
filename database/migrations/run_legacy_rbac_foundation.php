<?php

require dirname(__DIR__, 2) . '/source/Boot/Config.php';

$dbName = $argv[1] ?? CONF_DB_NAME;
$pdo = new PDO('mysql:host=' . CONF_DB_HOST . ';dbname=' . $dbName, CONF_DB_USER, CONF_DB_PASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sqlPath = __DIR__ . '/legacy_rbac_foundation.sql';
$sql = file_get_contents($sqlPath);
if ($sql === false) {
    throw new RuntimeException('Nao foi possivel ler o ficheiro SQL de RBAC legacy.');
}

foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
    $pdo->exec($statement);
}

$permissions = [
    ['name' => 'Gerir Menus', 'slug' => 'menus.manage', 'description' => 'Criar e editar menus e submenus.'],
    ['name' => 'Gerir Perfis', 'slug' => 'roles.manage', 'description' => 'Criar e editar perfis e grupos de acesso.'],
    ['name' => 'Gerir Instituicao', 'slug' => 'institution.manage', 'description' => 'Editar configuracoes institucionais e numeradores.'],
    ['name' => 'Pesquisa Global', 'slug' => 'search.global', 'description' => 'Executar busca operacional unificada no sistema.'],
    ['name' => 'Ver Pacientes', 'slug' => 'patients.view', 'description' => 'Consultar cadastro e historico de pacientes.'],
    ['name' => 'Criar Pacientes', 'slug' => 'patients.create', 'description' => 'Criar registos de pacientes.'],
    ['name' => 'Editar Pacientes', 'slug' => 'patients.update', 'description' => 'Atualizar registos de pacientes.'],
    ['name' => 'Gerir Servicos', 'slug' => 'services.manage', 'description' => 'Consultar e manter tabela de servicos e precos base.'],
    ['name' => 'Gerir Agenda', 'slug' => 'appointments.manage', 'description' => 'Gerir consultas e agendamentos.'],
    ['name' => 'Abrir Consulta', 'slug' => 'consultations.open', 'description' => 'Iniciar atendimento clinico.'],
    ['name' => 'Criar Prescricoes', 'slug' => 'prescriptions.create', 'description' => 'Emitir prescricoes clinicas.'],
    ['name' => 'Solicitar Exames', 'slug' => 'exam_requests.create', 'description' => 'Solicitar exames laboratoriais.'],
    ['name' => 'Ver Resultados', 'slug' => 'exam_results.view', 'description' => 'Consultar resultados laboratoriais.'],
    ['name' => 'Lancar Resultados', 'slug' => 'lab.results.create', 'description' => 'Lancar resultados de laboratorio.'],
    ['name' => 'Validar Resultados', 'slug' => 'lab.results.validate', 'description' => 'Validar resultados laboratoriais.'],
    ['name' => 'Gerir Produtos', 'slug' => 'pharmacy.products.manage', 'description' => 'Gerir cadastro de produtos.'],
    ['name' => 'Realizar Vendas', 'slug' => 'pharmacy.sales.create', 'description' => 'Efetuar vendas de farmacia.'],
    ['name' => 'Ver Stock', 'slug' => 'stock.view', 'description' => 'Consultar stock atual.'],
    ['name' => 'Receber Stock', 'slug' => 'stock.receive', 'description' => 'Dar entrada a mercadoria em stock.'],
    ['name' => 'Ajustar Stock', 'slug' => 'stock.adjust', 'description' => 'Executar ajustes de stock e inventario.'],
    ['name' => 'Ver Fiscal', 'slug' => 'fiscal.view', 'description' => 'Aceder ao painel fiscal e resumo documental.'],
    ['name' => 'Ver Documentos Fiscais', 'slug' => 'fiscal.documents.view', 'description' => 'Consultar faturas, recibos e documentos fiscais.'],
    ['name' => 'Gerir Faturas', 'slug' => 'invoices.manage', 'description' => 'Emitir e acompanhar faturas e rascunhos.'],
    ['name' => 'Gerir Pagamentos', 'slug' => 'payments.manage', 'description' => 'Registar e acompanhar pagamentos e prestações.'],
];

$insertPermission = $pdo->prepare(
    'INSERT INTO permissions (name, slug, description) VALUES (:name, :slug, :description)
     ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description)'
);

foreach ($permissions as $permission) {
    $insertPermission->execute($permission);
}

$tableExists = static function (PDO $pdo, string $tableName): bool {
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table_name'
    );
    $stmt->execute(['table_name' => $tableName]);
    return (int)$stmt->fetchColumn() > 0;
};

if ($tableExists($pdo, 'user_type')) {
    $groups = $pdo->query('SELECT Id, name FROM user_type ORDER BY Id')->fetchAll(PDO::FETCH_ASSOC) ?: [];
    $insertRole = $pdo->prepare(
        'INSERT INTO roles (name, slug, description) VALUES (:name, :slug, :description)
         ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description)'
    );
    $findRoleId = $pdo->prepare('SELECT id FROM roles WHERE slug = :slug LIMIT 1');
    $assignRole = $pdo->prepare(
        'INSERT INTO role_user (user_id, role_id) VALUES (:user_id, :role_id)
         ON DUPLICATE KEY UPDATE role_id = VALUES(role_id)'
    );

    foreach ($groups as $group) {
        $slug = 'legacy-group-' . (int)$group['Id'];
        $insertRole->execute([
            'name' => $group['name'],
            'slug' => $slug,
            'description' => 'Perfil migrado automaticamente do grupo legado user_type.'
        ]);

        $findRoleId->execute(['slug' => $slug]);
        $roleId = (int)$findRoleId->fetchColumn();
        if ($roleId <= 0) {
            continue;
        }

        $usersStmt = $pdo->prepare('SELECT Id FROM users WHERE tipoUtili = :group_id');
        $usersStmt->execute(['group_id' => $group['Id']]);
        foreach ($usersStmt->fetchAll(PDO::FETCH_COLUMN) ?: [] as $userId) {
            $assignRole->execute([
                'user_id' => (int)$userId,
                'role_id' => $roleId
            ]);
        }
    }
}

echo 'RBAC legacy foundation ready on database: ' . $dbName . PHP_EOL;
echo 'roles=' . $pdo->query('SELECT COUNT(*) FROM roles')->fetchColumn() . PHP_EOL;
echo 'permissions=' . $pdo->query('SELECT COUNT(*) FROM permissions')->fetchColumn() . PHP_EOL;
echo 'role_user=' . $pdo->query('SELECT COUNT(*) FROM role_user')->fetchColumn() . PHP_EOL;
echo 'permission_role=' . $pdo->query('SELECT COUNT(*) FROM permission_role')->fetchColumn() . PHP_EOL;
echo 'Nota: permission_role permanece vazio ate a definicao da matriz final de acessos.' . PHP_EOL;