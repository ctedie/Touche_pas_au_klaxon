<?php

declare(strict_types=1);

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
                <strong>Départ :</strong> <?= e((string) $trip['departure_agency']) ?><br>
                <strong>Date de départ :</strong> <?= e((string) $trip['departure_datetime']) ?><br>
                <strong>Arrivée :</strong> <?= e((string) $trip['arrival_agency']) ?><br>
                <strong>Date d’arrivée :</strong> <?= e((string) $trip['arrival_datetime']) ?><br>
                <strong>Places disponibles :</strong> <?= e((string) $trip['available_seats']) ?><br>

                <?php if (is_authenticated()): ?>
                    <a href="<?= e(base_url('trip/show?id=' . (string) $trip['id'])) ?>">Voir le détail</a>
                <?php else: ?>
                    <a href="<?= e(base_url('login')) ?>">Connectez-vous pour voir le détail</a>
                <?php endif; ?>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>