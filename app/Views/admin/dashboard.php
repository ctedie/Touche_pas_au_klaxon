<?php

declare(strict_types=1);

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';
?>

<div class="container py-4">
    <h1 class="mb-4">Tableau de bord administrateur</h1>

    <p>Accédez aux principales listes d'administration.</p>

    <ul>
        <li><a href="/touche-pas-au-klaxon/public/admin/users">Liste des utilisateurs</a></li>
        <li><a href="/touche-pas-au-klaxon/public/admin/agencies">Liste des agences</a></li>
        <li><a href="/touche-pas-au-klaxon/public/admin/trips">Liste des trajets</a></li>
    </ul>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
