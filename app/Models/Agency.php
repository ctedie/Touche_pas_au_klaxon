<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * ModÃƒÂ¨le des agences.
 */
final class Agency
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Retourne la liste des agences triÃƒÂ©es par nom.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAll(): array
    {
        $sql = 'SELECT id, nom FROM agences ORDER BY nom ASC';

        $statement = $this->pdo->query($sql);

        $agencies = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $agencies;
    }
}