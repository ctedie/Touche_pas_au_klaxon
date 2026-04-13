<?php

declare(strict_types=1);

/** @var array<int, array<string, mixed>> $trips */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$isConnected = isset($_SESSION['user']) && is_array($_SESSION['user']);
$basePath = '/trip/show';

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
if (str_starts_with($requestUri, '/touche-pas-au-klaxon/public')) {
    $basePath = '/touche-pas-au-klaxon/public/trip/show';
}
?>

<h1>Trajets disponibles</h1>

<?php if ($trips === []): ?>
    <p>Aucun trajet disponible pour le moment.</p>
<?php else: ?>
    <?php foreach ($trips as $trip): ?>
        <article>
            <p><strong>DÃ©part :</strong> <?= htmlspecialchars((string) $trip['departure_agency']) ?></p>
            <p><strong>Date de dÃ©part :</strong> <?= htmlspecialchars((string) $trip['departure_datetime']) ?></p>
            <p><strong>ArrivÃ©e :</strong> <?= htmlspecialchars((string) $trip['arrival_agency']) ?></p>
            <p><strong>Date dâ€™arrivÃ©e :</strong> <?= htmlspecialchars((string) $trip['arrival_datetime']) ?></p>
            <p><strong>Places disponibles :</strong> <?= htmlspecialchars((string) $trip['available_seats']) ?></p>

            <?php if ($isConnected): ?>
                <p>
                    <a href="<?= htmlspecialchars($basePath) ?>?id=<?= urlencode((string) $trip['id']) ?>">
                        Voir le dÃ©tail
                    </a>
                </p>
            <?php endif; ?>
        </article>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>