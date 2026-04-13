<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Gestion centralisÃ©e de la session.
 */
final class Session
{
    /**
     * DÃ©marre la session si nÃ©cessaire.
     */
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * DÃ©finit une valeur en session.
     *
     * @param mixed $value
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Retourne une valeur de session.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * VÃ©rifie l'existence d'une clÃ© en session.
     */
    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    /**
     * Supprime une clÃ© de session.
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Enregistre un message flash.
     */
    public static function flash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    /**
     * RÃ©cupÃ¨re puis supprime un message flash.
     */
    public static function getFlash(string $key): ?string
    {
        if (
            !isset($_SESSION['_flash']) ||
            !is_array($_SESSION['_flash']) ||
            !array_key_exists($key, $_SESSION['_flash'])
        ) {
            return null;
        }

        $message = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);

        return is_string($message) ? $message : null;
    }

    /**
     * RÃ©gÃ©nÃ¨re l'identifiant de session.
     */
    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * DÃ©connecte l'utilisateur courant.
     */
    public static function forgetAuth(): void
    {
        unset($_SESSION['auth']);
    }
}