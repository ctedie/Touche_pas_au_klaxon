<?php

declare(strict_types=1);

namespace Tests\Support;

use PDO;
use RuntimeException;

/**
 * Gère la base MySQL dédiée aux tests PHPUnit.
 */
final class TestDatabase
{
    private static ?PDO $connection = null;

    /**
     * Retourne la connexion à la base de test.
     */
    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $configPath = dirname(__DIR__, 2) . '/app/Config/config.test.php';

        if (!file_exists($configPath)) {
            throw new RuntimeException('Le fichier de configuration de test est introuvable.');
        }

        /** @var array<string, mixed> $config */
        $config = require $configPath;

        if (!isset($config['db']) || !is_array($config['db'])) {
            throw new RuntimeException('La configuration de test est invalide.');
        }

        /** @var array<string, mixed> $db */
        $db = $config['db'];

        $host = (string) ($db['host'] ?? '');
        $port = (int) ($db['port'] ?? 3306);
        $dbname = (string) ($db['dbname'] ?? '');
        $charset = (string) ($db['charset'] ?? 'utf8mb4');
        $username = (string) ($db['username'] ?? '');
        $password = (string) ($db['password'] ?? '');

        if ($host === '' || $dbname === '' || $username === '') {
            throw new RuntimeException('Les paramètres MySQL de test sont incomplets.');
        }

        $serverPdo = new PDO(
            sprintf('mysql:host=%s;port=%d;charset=%s', $host, $port, $charset),
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        $serverPdo->exec(
            sprintf(
                'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE %s_unicode_ci',
                $dbname,
                $charset,
                $charset
            )
        );

        self::$connection = new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbname, $charset),
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return self::$connection;
    }

    /**
     * Recrée complètement le schéma et les données minimales de test.
     */
    public static function resetDatabase(): PDO
    {
        $pdo = self::getConnection();

        $statements = [
            'SET FOREIGN_KEY_CHECKS = 0',
            'DROP TABLE IF EXISTS reservations',
            'DROP TABLE IF EXISTS trajets',
            'DROP TABLE IF EXISTS agences',
            'DROP TABLE IF EXISTS utilisateurs',
            'SET FOREIGN_KEY_CHECKS = 1',

            "CREATE TABLE utilisateurs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                telephone VARCHAR(20) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                mot_de_passe VARCHAR(255) NOT NULL,
                role VARCHAR(20) NOT NULL DEFAULT 'user',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'CREATE TABLE agences (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(150) NOT NULL UNIQUE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',

            'CREATE TABLE trajets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                auteur_id INT NOT NULL,
                agence_depart_id INT NOT NULL,
                agence_arrivee_id INT NOT NULL,
                date_depart DATETIME NOT NULL,
                date_arrivee DATETIME NOT NULL,
                places_total INT NOT NULL,
                places_disponibles INT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_trajet_utilisateur
                    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
                CONSTRAINT fk_trajet_agence_depart
                    FOREIGN KEY (agence_depart_id) REFERENCES agences(id),
                CONSTRAINT fk_trajet_agence_arrivee
                    FOREIGN KEY (agence_arrivee_id) REFERENCES agences(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',

            'CREATE TABLE reservations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                utilisateur_id INT NOT NULL,
                trajet_id INT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_reservations_utilisateur
                    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
                CONSTRAINT fk_reservations_trajet
                    FOREIGN KEY (trajet_id) REFERENCES trajets(id) ON DELETE CASCADE,
                CONSTRAINT uniq_reservation_utilisateur_trajet
                    UNIQUE (utilisateur_id, trajet_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        ];

        foreach ($statements as $statement) {
            $pdo->exec($statement);
        }

        $passwordHash = password_hash('password', PASSWORD_DEFAULT);

        $users = [
            ['Admin', 'Alice', '0600000001', 'admin@example.test', 'admin'],
            ['Martin', 'Paul', '0600000002', 'paul.martin@example.test', 'user'],
            ['Bernard', 'Julie', '0600000003', 'julie.bernard@example.test', 'user'],
            ['Durand', 'Luc', '0600000004', 'luc.durand@example.test', 'user'],
        ];

        $userStatement = $pdo->prepare(
            'INSERT INTO utilisateurs (nom, prenom, telephone, email, mot_de_passe, role)
             VALUES (:nom, :prenom, :telephone, :email, :mot_de_passe, :role)'
        );

        foreach ($users as [$nom, $prenom, $telephone, $email, $role]) {
            $userStatement->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'telephone' => $telephone,
                'email' => $email,
                'mot_de_passe' => $passwordHash,
                'role' => $role,
            ]);
        }

        $agencies = ['Paris', 'Lyon', 'Marseille', 'Nantes', 'Lille'];

        $agencyStatement = $pdo->prepare('INSERT INTO agences (nom) VALUES (:nom)');
        foreach ($agencies as $agencyName) {
            $agencyStatement->execute(['nom' => $agencyName]);
        }

        return $pdo;
    }
}