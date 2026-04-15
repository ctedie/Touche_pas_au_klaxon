<?php

declare(strict_types=1);

/** @var array<int, array<string, mixed>> $trips */

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<div class="container py-4">
    <h1 class="mb-4">Liste des trajets</h1>

    <?php if ($trips === []): ?>
        <p>Aucun trajet trouvé.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Auteur</th>
                    <th>Départ</th>
                    <th>Date départ</th>
                    <th>Arrivée</th>
                    <th>Date arrivée</th>
                    <th>Places totales</th>
                    <th>Places disponibles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $trip): ?>
                    <tr>
                        <td><?= (int) ($trip['id'] ?? 0) ?></td>
                        <td><?= $escape(trim((string) (($trip['author_first_name'] ?? '') . ' ' . ($trip['author_last_name'] ?? '')))) ?></td>
                        <td><?= $escape($trip['departure_agency'] ?? '') ?></td>
                        <td><?= $escape($trip['date_depart'] ?? '') ?></td>
                        <td><?= $escape($trip['arrival_agency'] ?? '') ?></td>
                        <td><?= $escape($trip['date_arrivee'] ?? '') ?></td>
                        <td><?= $escape($trip['places_total'] ?? '') ?></td>
                        <td><?= $escape($trip['places_disponibles'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
