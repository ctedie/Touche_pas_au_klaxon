<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Models\User;

/**
 * GÃ¨re l'authentification.
 */
final class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function showLoginForm(): void
    {
        $this->requireGuest();

        $this->render('auth/login', [
            'pageTitle' => 'Connexion',
            'oldEmail' => '',
        ]);
    }

    /**
     * Traite la connexion.
     */
    public function login(): void
    {
        $this->requireGuest();

        $email = trim((string) filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $password = (string) filter_input(INPUT_POST, 'password');

        if ($email === '' || $password === '') {
            Session::flash('error', 'Veuillez renseigner votre email et votre mot de passe.');
            $this->render('auth/login', [
                'pageTitle' => 'Connexion',
                'oldEmail' => $email,
            ]);

            return;
        }

        $userModel = new User();
        $user = $userModel->authenticate($email, $password);

        if ($user === null) {
            Session::flash('error', 'Identifiants invalides.');
            $this->render('auth/login', [
                'pageTitle' => 'Connexion',
                'oldEmail' => $email,
            ]);

            return;
        }

        Session::regenerate();
        Session::set('auth', [
            'id' => (int) $user['id'],
            'nom' => (string) $user['nom'],
            'prenom' => (string) $user['prenom'],
            'email' => (string) $user['email'],
            'telephone' => (string) $user['telephone'],
            'role' => (string) $user['role'],
        ]);

        Session::flash('success', 'Connexion rÃ©ussie.');
        $this->redirect('');
    }

    /**
     * DÃ©connecte l'utilisateur.
     */
    public function logout(): void
    {
        Session::forgetAuth();
        Session::regenerate();
        Session::flash('success', 'Vous avez Ã©tÃ© dÃ©connectÃ©.');

        $this->redirect('');
    }
}