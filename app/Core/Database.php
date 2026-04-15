<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Gère la connexion à la base de données via PDO.
 */
final class Database
{
    private static ?PDO $connection = null;

    /**
     * Définit une connexion PDO spécifique.
     */
    public static function setConnection(PDO $connection): void
    {
        self::$connection = $connection;
    }

    /**
     * Réinitialise la connexion mémorisée.
     */
    public static function resetConnection(): void
    {
        self::$connection = null;
    }

    /**
     * Retourne une instance unique de connexion PDO.
     */
    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $configPath = self::resolveConfigPath();

        if (!file_exists($configPath)) {
            throw new RuntimeException('Le fichier de configuration est introuvable.');
        }

        /** @var array<string, mixed> $config */
        $config = require $configPath;

        if (!isset($config['db']) || !is_array($config['db'])) {
            throw new RuntimeException('La configuration de la base de données est invalide.');
        }

        /** @var array<string, mixed> $dbConfig */
        $dbConfig = $config['db'];

        $host = (string) ($dbConfig['host'] ?? '');
        $port = (int) ($dbConfig['port'] ?? 3306);
        $dbname = (string) ($dbConfig['dbname'] ?? '');
        $charset = (string) ($dbConfig['charset'] ?? 'utf8mb4');
        $username = (string) ($dbConfig['username'] ?? '');
        $password = (string) ($dbConfig['password'] ?? '');

        if ($host === '' || $dbname === '' || $username === '') {
            throw new RuntimeException('Les paramètres de connexion à la base sont incomplets.');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $host,
            $port,
            $dbname,
            $charset
        );

        try {
            self::$connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'Connexion à la base de données impossible : ' . $exception->getMessage(),
                0,
                $exception
            );
        }

        return self::$connection;
    }

    private static function resolveConfigPath(): string
    {
        $configDirectory = dirname(__DIR__) . '/Config';
        $appEnv = (string) ($_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? getenv('APP_ENV') ?: '');

        if ($appEnv === 'test') {
            $testConfigPath = $configDirectory . '/config.test.php';

            if (file_exists($testConfigPath)) {
                return $testConfigPath;
            }
        }

        return $configDirectory . '/config.php';
    }
}