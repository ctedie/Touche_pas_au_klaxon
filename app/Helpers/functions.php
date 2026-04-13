<?php

declare(strict_types=1);

use App\Core\Session;

/**
 * Ã‰chappe une chaÃ®ne pour l'affichage HTML.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Retourne l'URL de base de l'application.
 */
function base_url(string $path = ''): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = str_replace('\\', '/', dirname($scriptName));

    if ($basePath === '/' || $basePath === '\\') {
        $basePath = '';
    }

    $path = ltrim($path, '/');

    if ($path === '') {
        return $basePath === '' ? '/' : $basePath . '/';
    }

    return ($basePath === '' ? '' : $basePath) . '/' . $path;
}

/**
 * Indique si un utilisateur est connectÃ©.
 */
function is_authenticated(): bool
{
    return Session::has('auth');
}

/**
 * Retourne l'utilisateur connectÃ©.
 *
 * @return array<string, mixed>|null
 */
function current_user(): ?array
{
    $user = Session::get('auth');

    return is_array($user) ? $user : null;
}

/**
 * Retourne un message flash puis le supprime.
 */
function flash(string $key): ?string
{
    $value = Session::getFlash($key);

    return is_string($value) ? $value : null;
}