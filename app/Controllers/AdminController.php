<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Agency;
use App\Models\Trip;
use App\Models\User;

/**
 * ContrÃ´leur du tableau de bord administrateur.
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

    public function createAgency(): void
    {
        $currentUser = $this->requireAdmin();

        $this->render('admin/agency_form', [
            'currentUser' => $currentUser,
            'pageTitle' => 'CrÃ©er une agence',
            'submitLabel' => 'CrÃ©er',
            'formAction' => '/admin/agencies/store',
            'errors' => [],
            'formData' => [
                'nom' => '',
            ],
        ]);
    }

    public function storeAgency(): void
    {
        $currentUser = $this->requireAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/admin/agencies');
            return;
        }

        $agencyModel = new Agency();
        $name = trim((string) ($_POST['nom'] ?? ''));
        $errors = $this->validateAgencyName($name, $agencyModel);

        if ($errors !== []) {
            $this->render('admin/agency_form', [
                'currentUser' => $currentUser,
                'pageTitle' => 'CrÃ©er une agence',
                'submitLabel' => 'CrÃ©er',
                'formAction' => '/admin/agencies/store',
                'errors' => $errors,
                'formData' => [
                    'nom' => $name,
                ],
            ]);
            return;
        }

        $agencyModel->create($name);
        $_SESSION['flash_success'] = "L'agence a bien Ã©tÃ© crÃ©Ã©e.";

        $this->redirect('/admin/agencies');
    }

    public function editAgency(): void
    {
        $currentUser = $this->requireAdmin();
        $agencyId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($agencyId <= 0) {
            http_response_code(404);
            echo 'Agence introuvable.';
            return;
        }

        $agencyModel = new Agency();
        $agency = $agencyModel->findById($agencyId);

        if ($agency === null) {
            http_response_code(404);
            echo 'Agence introuvable.';
            return;
        }

        $this->render('admin/agency_form', [
            'currentUser' => $currentUser,
            'pageTitle' => 'Modifier une agence',
            'submitLabel' => 'Enregistrer',
            'formAction' => '/admin/agencies/update?id=' . $agencyId,
            'errors' => [],
            'formData' => [
                'nom' => (string) $agency['nom'],
            ],
        ]);
    }

    public function updateAgency(): void
    {
        $currentUser = $this->requireAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/admin/agencies');
            return;
        }

        $agencyId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($agencyId <= 0) {
            http_response_code(404);
            echo 'Agence introuvable.';
            return;
        }

        $agencyModel = new Agency();
        $agency = $agencyModel->findById($agencyId);

        if ($agency === null) {
            http_response_code(404);
            echo 'Agence introuvable.';
            return;
        }

        $name = trim((string) ($_POST['nom'] ?? ''));
        $errors = $this->validateAgencyName($name, $agencyModel, $agencyId);

        if ($errors !== []) {
            $this->render('admin/agency_form', [
                'currentUser' => $currentUser,
                'pageTitle' => 'Modifier une agence',
                'submitLabel' => 'Enregistrer',
                'formAction' => '/admin/agencies/update?id=' . $agencyId,
                'errors' => $errors,
                'formData' => [
                    'nom' => $name,
                ],
            ]);
            return;
        }

        $agencyModel->update($agencyId, $name);
        $_SESSION['flash_success'] = "L'agence a bien Ã©tÃ© modifiÃ©e.";

        $this->redirect('/admin/agencies');
    }

    public function deleteAgency(): void
    {
        $this->requireAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/admin/agencies');
            return;
        }

        $agencyId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($agencyId <= 0) {
            http_response_code(404);
            echo 'Agence introuvable.';
            return;
        }

        $agencyModel = new Agency();
        $agency = $agencyModel->findById($agencyId);

        if ($agency === null) {
            http_response_code(404);
            echo 'Agence introuvable.';
            return;
        }

        if ($agencyModel->isUsed($agencyId)) {
            $_SESSION['flash_error'] = 'Impossible de supprimer une agence utilisÃ©e par au moins un trajet.';
            $this->redirect('/admin/agencies');
            return;
        }

        $agencyModel->delete($agencyId);
        $_SESSION['flash_success'] = "L'agence a bien Ã©tÃ© supprimÃ©e.";

        $this->redirect('/admin/agencies');
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

    public function deleteTrip(): void
    {
        $this->requireAdmin();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/admin/trips');
            return;
        }

        $tripId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($tripId <= 0) {
            http_response_code(404);
            echo 'Trajet introuvable.';
            return;
        }

        $tripModel = new Trip();
        $trip = $tripModel->findById($tripId);

        if ($trip === null) {
            http_response_code(404);
            echo 'Trajet introuvable.';
            return;
        }

        $tripModel->deleteById($tripId);
        $_SESSION['flash_success'] = 'Le trajet a bien Ã©tÃ© supprimÃ©.';

        $this->redirect('/admin/trips');
    }

    /**
     * @return array<int, string>
     */
    private function validateAgencyName(string $name, Agency $agencyModel, ?int $excludeId = null): array
    {
        $errors = [];

        if ($name === '') {
            $errors[] = 'Le nom de lâ€™agence est obligatoire.';
        }

        if ($name !== '' && $agencyModel->existsByName($name, $excludeId)) {
            $errors[] = 'Une agence avec ce nom existe dÃ©jÃ .';
        }

        return $errors;
    }
}