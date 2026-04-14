<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

/**
 * ContrÃ´leur de base.
 */
abstract class Controller
{
    /**
     * @param array<string, mixed> $data
     */
    protected function render(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function redirect(string $path): void
    {
        $basePath = '/touche-pas-au-klaxon/public';

        if ($path === '') {
            $path = '/';
        }

        if ($path[0] !== '/') {
            $path = '/' . $path;
        }

        header('Location: ' . $basePath . $path);
        exit;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getAuthenticatedUser(): ?array
    {
        if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
            return null;
        }

        return $_SESSION['user'];
    }

    protected function requireAuth(): void
    {
        if ($this->getAuthenticatedUser() === null) {
            $this->redirect('/login');
        }
    }

    protected function requireGuest(): void
    {
        if ($this->getAuthenticatedUser() !== null) {
            $this->redirect('/');
        }
    }
}