<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * ModÃ¨le utilisateur.
 */
final class User
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Recherche un utilisateur par email.
     *
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = '
            SELECT id, nom, prenom, telephone, email, mot_de_passe, role
            FROM utilisateurs
            WHERE email = :email
            LIMIT 1
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':email', $email);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return is_array($user) ? $user : null;
    }

    /**
     * Retourne la liste des utilisateurs.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAll(): array
    {
        $sql = '
            SELECT id, nom, prenom, telephone, email, role
            FROM utilisateurs
            ORDER BY nom ASC, prenom ASC
        ';

        /** @var array<int, array<string, mixed>> $users */
        $users = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return $users;
    }

    /**
     * Authentifie un utilisateur.
     *
     * Compatible avec un mot de passe hashÃ© ou, temporairement,
     * avec une valeur en clair dÃ©jÃ  prÃ©sente dans le jeu d'essai.
     *
     * @return array<string, mixed>|null
     */
    public function authenticate(string $email, string $plainPassword): ?array
    {
        $user = $this->findByEmail($email);

        if ($user === null) {
            return null;
        }

        $storedPassword = (string) $user['mot_de_passe'];

        if (!$this->verifyPassword($plainPassword, $storedPassword)) {
            return null;
        }

        unset($user['mot_de_passe']);

        return $user;
    }

    /**
     * VÃ©rifie le mot de passe.
     */
    private function verifyPassword(string $plainPassword, string $storedPassword): bool
    {
        $passwordInfo = password_get_info($storedPassword);

        if (($passwordInfo['algo'] ?? null) !== null && $passwordInfo['algo'] !== 0) {
            return password_verify($plainPassword, $storedPassword);
        }

        return hash_equals($storedPassword, $plainPassword);
    }
}