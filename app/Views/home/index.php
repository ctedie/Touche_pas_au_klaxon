<?php

declare(strict_types=1);

require __DIR__ . '/../partials/flash.php';

/** @var array<int, array<string, mixed>> $trips */

require __DIR__ . '/../layouts/header.php';
?>

<h1>Trajets disponibles</h1>

<?php if ($trips === []): ?>
    <p>Aucun trajet disponible pour le moment.</p>
<?php else: ?>
    <ul>
        <?php foreach ($trips as $trip): ?>
            <li>
                <strong>DÃ©part :</strong> <?= htmlspecialchars((string) $trip['departure_agency'], ENT_QUOTES, 'UTF-8') ?><br>
                <strong>Date de dÃ©part :</strong> <?= htmlspecialchars((string) $trip['departure_datetime'], ENT_QUOTES, 'UTF-8') ?><br>
                <strong>ArrivÃ©e :</strong> <?= htmlspecialchars((string) $trip['arrival_agency'], ENT_QUOTES, 'UTF-8') ?><br>
                <strong>Date dâ€™arrivÃ©e :</strong> <?= htmlspecialchars((string) $trip['arrival_datetime'], ENT_QUOTES, 'UTF-8') ?><br>
                <strong>Places disponibles :</strong> <?= htmlspecialchars((string) $trip['available_seats'], ENT_QUOTES, 'UTF-8') ?><br>

                <?php if (isset($_SESSION['user']) && is_array($_SESSION['user'])): ?>
                    <a href="/touche-pas-au-klaxon/public/trip/show?id=<?= urlencode((string) $trip['id']) ?>">Voir le dÃ©tail</a>
                <?php else: ?>
                    <a href="/touche-pas-au-klaxon/public/login">Connectez-vous pour voir le dÃ©tail</a>
                <?php endif; ?>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>