<?php

declare(strict_types=1);

/** @var array<string, mixed> $trip */

require __DIR__ . '/../layouts/header.php';
?>

<h1>DÃ©tail du trajet</h1>

<ul>
    <li><strong>DÃ©part :</strong> <?= htmlspecialchars((string) $trip['departure_agency'], ENT_QUOTES, 'UTF-8') ?></li>
    <li><strong>Date de dÃ©part :</strong> <?= htmlspecialchars((string) $trip['date_depart'], ENT_QUOTES, 'UTF-8') ?></li>
    <li><strong>ArrivÃ©e :</strong> <?= htmlspecialchars((string) $trip['arrival_agency'], ENT_QUOTES, 'UTF-8') ?></li>
    <li><strong>Date dâ€™arrivÃ©e :</strong> <?= htmlspecialchars((string) $trip['date_arrivee'], ENT_QUOTES, 'UTF-8') ?></li>
    <li><strong>Places totales :</strong> <?= htmlspecialchars((string) $trip['places_total'], ENT_QUOTES, 'UTF-8') ?></li>
    <li><strong>Places disponibles :</strong> <?= htmlspecialchars((string) $trip['places_disponibles'], ENT_QUOTES, 'UTF-8') ?></li>
    <li><strong>Nom :</strong> <?= htmlspecialchars((string) $trip['user_last_name'], ENT_QUOTES, 'UTF-8') ?></li>
    <li><strong>PrÃ©nom :</strong> <?= htmlspecialchars((string) $trip['user_first_name'], ENT_QUOTES, 'UTF-8') ?></li>
    <li><strong>Email :</strong> <?= htmlspecialchars((string) $trip['user_email'], ENT_QUOTES, 'UTF-8') ?></li>
    <li><strong>TÃ©lÃ©phone :</strong> <?= htmlspecialchars((string) $trip['user_phone'], ENT_QUOTES, 'UTF-8') ?></li>
</ul>

<p>
    <a href="/touche-pas-au-klaxon/public/">Retour Ã  lâ€™accueil</a>
</p>

<?php require __DIR__ . '/../layouts/footer.php'; ?>