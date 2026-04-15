<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;

/**
 * ContrÃ´leur d''authentification.
 */
final class AuthController extends Controller
{
    public function showLoginForm(): void
    {
        $this->requireGuest();

        $this->render('auth/login', [
            'title' => 'Connexion',
        ]);
    }

    public function login(): void
    {
        $this->requireGuest();

        $email = isset($_POST['email']) ? trim((string) $_POST['email']) : '';
        $password = isset($_POST['password']) ? (string) $_POST['password'] : '';

        if ($email === '' || $password === '') {
            $_SESSION['flash_error'] = 'Veuillez renseigner votre email et votre mot de passe.';
            $this->redirect('/login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user === null) {
            $_SESSION['flash_error'] = 'Identifiants invalides.';
            $this->redirect('/login');
        }

        $storedPassword = $user['mot_de_passe'] ?? null;

        if (!is_string($storedPassword)) {
            $_SESSION['flash_error'] = 'Identifiants invalides.';
            $this->redirect('/login');
        }

        $isValidPassword = password_verify($password, $storedPassword) || $password === $storedPassword;

        if (!$isValidPassword) {
            $_SESSION['flash_error'] = 'Identifiants invalides.';
            $this->redirect('/login');
        }

        $_SESSION['user'] = [
            'id' => (int) ($user['id'] ?? 0),
            'prenom' => (string) ($user['prenom'] ?? ''),
            'nom' => (string) ($user['nom'] ?? ''),
            'email' => (string) ($user['email'] ?? ''),
            'telephone' => (string) ($user['telephone'] ?? ''),
            'role' => (string) ($user['role'] ?? 'user'),
        ];
        $this->redirect('/');
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        $this->redirect('/login');
    }
}