<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Agency;
use App\Models\User;
use App\Services\TripService;

/**
 * Contrôleur du tableau de bord administrateur.
 */
final class AdminController extends Controller
{
    private const TRIPS_PER_PAGE = 5;

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
            'pageTitle' => 'Créer une agence',
            'submitLabel' => 'Créer',
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
                'pageTitle' => 'Créer une agence',
                'submitLabel' => 'Créer',
                'formAction' => '/admin/agencies/store',
                'errors' => $errors,
                'formData' => [
                    'nom' => $name,
                ],
            ]);
            return;
        }

        $agencyModel->create($name);
        $_SESSION['flash_success'] = "L'agence a bien été créée.";

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
        $_SESSION['flash_success'] = "L'agence a bien été modifiée.";

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
            $_SESSION['flash_error'] = 'Impossible de supprimer une agence utilisée par au moins un trajet.';
            $this->redirect('/admin/agencies');
            return;
        }

        $agencyModel->delete($agencyId);
        $_SESSION['flash_success'] = "L'agence a bien été supprimée.";

        $this->redirect('/admin/agencies');
    }

    public function trips(): void
    {
        $currentUser = $this->requireAdmin();
        $tripService = new TripService();
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $result = $tripService->getAdminTripsPage($page, self::TRIPS_PER_PAGE);

        $this->render('admin/trips', [
            'currentUser' => $currentUser,
            'trips' => $result['items'],
            'pagination' => $result['pagination'],
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

        $tripService = new TripService();
        $trip = $tripService->getTripById($tripId);

        if ($trip === null) {
            http_response_code(404);
            echo 'Trajet introuvable.';
            return;
        }

        (new \App\Models\Trip())->deleteById($tripId);
        $_SESSION['flash_success'] = 'Le trajet a bien été supprimé.';

        $this->redirect('/admin/trips');
    }

    /**
     * @return array<int, string>
     */
    private function validateAgencyName(string $name, Agency $agencyModel, ?int $excludeId = null): array
    {
        $errors = [];

        if ($name === '') {
            $errors[] = 'Le nom de l’agence est obligatoire.';
        }

        if ($name !== '' && $agencyModel->existsByName($name, $excludeId)) {
            $errors[] = 'Une agence avec ce nom existe déjà.';
        }

        return $errors;
    }
}
