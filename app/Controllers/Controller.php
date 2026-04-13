<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;

/**
 * ContrÃ´leur de base.
 */
abstract class Controller
{
    /**
     * Affiche une vue.
     *
     * @param array<string, mixed> $data
     */
    protected function render(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    /**
     * Redirige vers une URL interne.
     */
    protected function redirect(string $path): never
    {
        header('Location: ' . base_url($path));
        exit;
    }

    /**
     * Retourne l'utilisateur connectÃ©.
     *
     * @return array<string, mixed>|null
     */
    protected function getAuthenticatedUser(): ?array
    {
        $user = Session::get('auth');

        return is_array($user) ? $user : null;
    }

    /**
     * VÃ©rifie qu'un utilisateur est connectÃ©.
     */
    protected function requireAuth(): void
    {
        if ($this->getAuthenticatedUser() === null) {
            Session::flash('error', 'Vous devez Ãªtre connectÃ© pour accÃ©der Ã  cette page.');
            $this->redirect('login');
        }
    }

    /**
     * EmpÃªche l'accÃ¨s Ã  une page si l'utilisateur est dÃ©jÃ  connectÃ©.
     */
    protected function requireGuest(): void
    {
        if ($this->getAuthenticatedUser() !== null) {
            $this->redirect('');
        }
    }
}