<?php

declare(strict_types=1);

/** @var array<string, mixed>|null $trip */
/** @var string|null $error */

$homePath = '/';

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
if (str_starts_with($requestUri, '/touche-pas-au-klaxon/public')) {
    $homePath = '/touche-pas-au-klaxon/public/';
}
?>

<h1>DÃ©tail du trajet</h1>

<?php if ($error !== null): ?>
    <p><?= htmlspecialchars($error) ?></p>
    <p><a href="<?= htmlspecialchars($homePath) ?>">Retour Ã  lâ€™accueil</a></p>
<?php elseif ($trip === null): ?>
    <p>Trajet introuvable.</p>
    <p><a href="<?= htmlspecialchars($homePath) ?>">Retour Ã  lâ€™accueil</a></p>
<?php else: ?>
    <p><strong>Agence de dÃ©part :</strong> <?= htmlspecialchars((string) $trip['departure_agency']) ?></p>
    <p><strong>Date de dÃ©part :</strong> <?= htmlspecialchars((string) $trip['departure_datetime']) ?></p>
    <p><strong>Agence dâ€™arrivÃ©e :</strong> <?= htmlspecialchars((string) $trip['arrival_agency']) ?></p>
    <p><strong>Date dâ€™arrivÃ©e :</strong> <?= htmlspecialchars((string) $trip['arrival_datetime']) ?></p>
    <p><strong>Places disponibles :</strong> <?= htmlspecialchars((string) $trip['available_seats']) ?></p>

    <hr>

    <p><strong>Conducteur :</strong> <?= htmlspecialchars((string) $trip['first_name']) ?> <?= htmlspecialchars((string) $trip['last_name']) ?></p>
    <p><strong>TÃ©lÃ©phone :</strong> <?= htmlspecialchars((string) $trip['phone']) ?></p>
    <p><strong>Email :</strong> <?= htmlspecialchars((string) $trip['email']) ?></p>
    <p><strong>Nombre total de places :</strong> <?= htmlspecialchars((string) $trip['total_seats']) ?></p>

    <p><a href="<?= htmlspecialchars($homePath) ?>">Retour Ã  lâ€™accueil</a></p>
<?php endif; ?>