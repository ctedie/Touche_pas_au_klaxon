<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Helpers/functions.php';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<section class="page-section">
    <h1 class="page-title">Tableau de bord administrateur</h1>
    <p class="mb-4">AccÃ©dez aux principales listes d'administration.</p>

    <div class="dashboard-links">
        <a class="dashboard-link-card" href="<?= base_url('admin/users') ?>">
            <h2 class="h5 mb-2">Utilisateurs</h2>
            <p class="mb-0">Consulter la liste complÃ¨te des utilisateurs.</p>
        </a>

        <a class="dashboard-link-card" href="<?= base_url('admin/agencies') ?>">
            <h2 class="h5 mb-2">Agences</h2>
            <p class="mb-0">CrÃ©er, modifier ou supprimer une agence.</p>
        </a>

        <a class="dashboard-link-card" href="<?= base_url('admin/trips') ?>">
            <h2 class="h5 mb-2">Trajets</h2>
            <p class="mb-0">Consulter et supprimer les trajets publiÃ©s.</p>
        </a>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>