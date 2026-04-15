<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * ModÃ¨le des agences.
 */
final class Agency
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Retourne la liste des agences triÃ©es par nom.
     *
     * @return array<int, array{id:int, nom:string}>
     */
    public function findAll(): array
    {
        $sql = 'SELECT id, nom FROM agences ORDER BY nom ASC';
        $statement = $this->pdo->query($sql);

        if ($statement === false) {
            return [];
        }

        /** @var array<int, array{id:int, nom:string}> $agencies */
        $agencies = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $agencies;
    }

    /**
     * @return array{id:int, nom:string}|null
     */
    public function findById(int $agencyId): ?array
    {
        $sql = 'SELECT id, nom FROM agences WHERE id = :id LIMIT 1';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $agencyId]);

        $agency = $statement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($agency)) {
            return null;
        }

        /** @var array{id:int, nom:string} $agency */
        return $agency;
    }

    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM agences WHERE LOWER(nom) = LOWER(:nom)';
        $params = ['nom' => $name];

        if ($excludeId !== null) {
            $sql .= ' AND id <> :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn() > 0;
    }

    public function create(string $name): int
    {
        $statement = $this->pdo->prepare('INSERT INTO agences (nom) VALUES (:nom)');
        $statement->execute(['nom' => $name]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $agencyId, string $name): bool
    {
        $statement = $this->pdo->prepare('UPDATE agences SET nom = :nom WHERE id = :id');
        $statement->execute([
            'id' => $agencyId,
            'nom' => $name,
        ]);

        return $statement->rowCount() > 0;
    }

    public function isUsed(int $agencyId): bool
    {
        $sql = <<<SQL
SELECT COUNT(*)
FROM trajets
WHERE agence_depart_id = :departure_id
   OR agence_arrivee_id = :arrival_id
SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'departure_id' => $agencyId,
            'arrival_id' => $agencyId,
        ]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function delete(int $agencyId): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM agences WHERE id = :id');
        $statement->execute(['id' => $agencyId]);

        return $statement->rowCount() > 0;
    }
}