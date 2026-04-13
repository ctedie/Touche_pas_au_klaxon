<?php

declare(strict_types=1);

/** @var array<string, mixed> $trip */

require __DIR__ . '/../layouts/header.php';
?>

<h1>Détail du trajet</h1>

<p><strong>Agence de départ :</strong> <?= e((string) $trip['departure_agency']) ?></p>
<p><strong>Date de départ :</strong> <?= e((string) $trip['departure_datetime']) ?></p>
<p><strong>Agence d’arrivée :</strong> <?= e((string) $trip['arrival_agency']) ?></p>
<p><strong>Date d’arrivée :</strong> <?= e((string) $trip['arrival_datetime']) ?></p>
<p><strong>Places disponibles :</strong> <?= e((string) $trip['available_seats']) ?></p>
<p><strong>Places totales :</strong> <?= e((string) $trip['total_seats']) ?></p>
<p><strong>Personne à contacter :</strong> <?= e((string) $trip['first_name']) ?> <?= e((string) $trip['last_name']) ?></p>
<p><strong>Téléphone :</strong> <?= e((string) $trip['phone']) ?></p>
<p><strong>Email :</strong> <?= e((string) $trip['email']) ?></p>

<p>
    <a href="<?= e(base_url()) ?>">Retour à l’accueil</a>
</p>

<?php require __DIR__ . '/../layouts/footer.php'; ?>