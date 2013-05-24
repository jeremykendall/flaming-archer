<?php

echo 'This will DESTROY your database and replace it with seed data!' . PHP_EOL;
echo 'Are you sure you want to continue? (YES/NO)' . PHP_EOL;

$handle = fopen ("php://stdin","r");
$line = fgets($handle);

if (trim($line) !== 'YES') {
    echo "You did not enter 'YES'. Aborting." . PHP_EOL;
    exit(0);
}

require __DIR__ . '/../vendor/autoload.php';
$config = require_once __DIR__ . '/../config.php';

if (file_exists($config['database'])) {
    unlink($config['database']);
}

try {
    $db = new PDO(
        $config['pdo']['dsn'],
        $config['pdo']['username'],
        $config['pdo']['password'],
        $config['pdo']['options']
    );
} catch (PDOException $e) {
    echo 'Database connection error in ' . $e->getFile() . ' on line ' . $e->getLine() . ': ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

$schema = file_get_contents(__DIR__ . '/../scripts/sql/schema.sql');
$data = file_get_contents(__DIR__ . '/../scripts/sql/seed_data.sql');

try {
    $db->exec($schema);
    $db->exec($data);
} catch (PDOException $e) {
    echo 'PDO Exception while trying to refresh db: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo 'Database successfully refreshed.' . PHP_EOL;
exit(0);
