<?php

$sql = file_get_contents(__DIR__ . '/post_migration_patient_clinic_resolution.sql');

if ($sql === false) {
    fwrite(STDERR, "Nao foi possivel ler o ficheiro SQL de saneamento.\n");
    exit(1);
}

$pdo = new PDO('mysql:host=localhost;dbname=cvnsu_vbd', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$statements = array_filter(array_map('trim', preg_split('/;\s*(?:\r?\n|$)/', $sql)));

try {
    foreach ($statements as $statement) {
        if ($statement === '') {
            continue;
        }

        $query = $pdo->query($statement);
        if ($query instanceof PDOStatement && $query->columnCount() > 0) {
            while ($row = $query->fetch(PDO::FETCH_NUM)) {
                echo implode(':', $row) . PHP_EOL;
            }
        }
    }
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}