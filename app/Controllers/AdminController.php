<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Agency;
use App\Models\Trip;
use App\Models\User;

/**
 * Contrôleur du tableau de bord administrateur.
 */
final class AdminController extends Controller
{
    public function dashboard(): void
    {
        $currentUser = $this->requireAdmin();

        $this->render('admin/dashboard', [
            'currentUser' => $currentUser,
        ]);
    }

    public function users(): void
    {
        $currentUser = $this->requireAdmin();
        $userModel = new User();

        $this->render('admin/users', [
            'currentUser' => $currentUser,
            'users' => $userModel->findAll(),
        ]);
    }

    public function agencies(): void
    {
        $currentUser = $this->requireAdmin();
        $agencyModel = new Agency();

        $this->render('admin/agencies', [
            'currentUser' => $currentUser,
            'agencies' => $agencyModel->findAll(),
        ]);
    }

    public function trips(): void
    {
        $currentUser = $this->requireAdmin();
        $tripModel = new Trip();

        $this->render('admin/trips', [
            'currentUser' => $currentUser,
            'trips' => $tripModel->findAllForAdmin(),
        ]);
    }
}
