<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use PDO;

$pdo = Database::getConnection();

$stmt = $pdo->query('DESCRIBE trajets');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($columns as $column) {
    echo $column['Field'] . PHP_EOL;
}