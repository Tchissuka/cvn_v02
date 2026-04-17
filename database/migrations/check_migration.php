<?php

$pdo = new PDO('mysql:host=localhost;dbname=cvnsu_vbd', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tables = [
    'clinics',
    'users',
    'roles',
    'people',
    'employees',
    'patients',
    'menus',
    'menu_groups',
    'menu_group_items',
    'user_menu_groups',
    'products',
    'product_categories',
    'general_numerator'
];

echo "COUNTS\n";
foreach ($tables as $table) {
    $count = $pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
    echo $table . ':' . $count . PHP_EOL;
}

echo PHP_EOL . "SAMPLES\n";

$sampleQueries = [
    'users' => 'SELECT id, clinic_id, name, email FROM users ORDER BY id LIMIT 3',
    'people' => 'SELECT id, clinic_id, full_name FROM people ORDER BY id LIMIT 3',
    'patients' => 'SELECT id, clinic_id, medical_record_number FROM patients ORDER BY id LIMIT 3',
    'products' => 'SELECT id, clinic_id, name, code FROM products ORDER BY id LIMIT 3'
];

foreach ($sampleQueries as $label => $query) {
    foreach ($pdo->query($query, PDO::FETCH_ASSOC) as $row) {
        echo $label . ':' . implode('|', $row) . PHP_EOL;
    }
}