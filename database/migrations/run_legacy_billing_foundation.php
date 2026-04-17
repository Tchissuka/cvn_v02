<?php

require dirname(__DIR__, 2) . '/source/Boot/Config.php';

$dbName = $argv[1] ?? CONF_DB_NAME;
$pdo = new PDO('mysql:host=' . CONF_DB_HOST . ';dbname=' . $dbName, CONF_DB_USER, CONF_DB_PASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sqlPath = __DIR__ . '/legacy_billing_foundation.sql';
$sql = file_get_contents($sqlPath);
if ($sql === false) {
    throw new RuntimeException('Nao foi possivel ler o ficheiro SQL da fundacao de faturacao legacy.');
}

foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
    $pdo->exec($statement);
}

echo 'Legacy billing foundation ready on database: ' . $dbName . PHP_EOL;
echo 'invoices=' . $pdo->query('SELECT COUNT(*) FROM invoices')->fetchColumn() . PHP_EOL;
echo 'invoice_items=' . $pdo->query('SELECT COUNT(*) FROM invoice_items')->fetchColumn() . PHP_EOL;
echo 'payments=' . $pdo->query('SELECT COUNT(*) FROM payments')->fetchColumn() . PHP_EOL;