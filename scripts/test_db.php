<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Database;

echo "Test connexion...\n";

try {
    $pdo = Database::getConnection();

    echo "Connexion OK\n";

    $stmt = $pdo->query("SELECT COUNT(*) FROM agences");

    $count = $stmt->fetchColumn();

    echo "Nombre d'agences : " . $count . "\n";

} catch (Throwable $e) {
    echo "Erreur : " . $e->getMessage();
}