<?php

declare(strict_types=1);

/** @var array<string, mixed> $trip */

require __DIR__ . '/../layouts/header.php';
?>

<h1>Détail du trajet</h1>

<ul>
    <li><strong>Départ :</strong> <?= e((string) $trip['departure_agency']) ?></li>
    <li><strong>Date de départ :</strong> <?= e((string) $trip['date_depart']) ?></li>
    <li><strong>Arrivée :</strong> <?= e((string) $trip['arrival_agency']) ?></li>
    <li><strong>Date d’arrivée :</strong> <?= e((string) $trip['date_arrivee']) ?></li>
    <li><strong>Places totales :</strong> <?= e((string) $trip['nombre_places']) ?></li>
    <li><strong>Places disponibles :</strong> <?= e((string) $trip['places_disponibles']) ?></li>
    <li><strong>Nom :</strong> <?= e((string) $trip['user_last_name']) ?></li>
    <li><strong>Prénom :</strong> <?= e((string) $trip['user_first_name']) ?></li>
    <li><strong>Email :</strong> <?= e((string) $trip['user_email']) ?></li>
    <li><strong>Téléphone :</strong> <?= e((string) $trip['user_phone']) ?></li>
</ul>

<p><a href="<?= e(base_url('')) ?>">Retour à l’accueil</a></p>

<?php require __DIR__ . '/../layouts/footer.php'; ?>