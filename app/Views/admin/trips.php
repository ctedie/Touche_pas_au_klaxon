<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Helpers/functions.php';

/** @var array<int, array<string, mixed>> $trips */

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<div class="container py-4">
    <h1>Liste des trajets</h1>

    <?php if (count($trips) === 0): ?>
        <p>Aucun trajet trouvÃ©.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>DÃ©part</th>
                    <th>ArrivÃ©e</th>
                    <th>Date dÃ©part</th>
                    <th>Date arrivÃ©e</th>
                    <th>Places</th>
                    <th>Auteur</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $trip): ?>
                    <tr>
                        <td><?= (int) ($trip['id'] ?? 0) ?></td>
                        <td><?= $escape($trip['departure_agency'] ?? '') ?></td>
                        <td><?= $escape($trip['arrival_agency'] ?? '') ?></td>
                        <td><?= $escape($trip['departure_datetime'] ?? '') ?></td>
                        <td><?= $escape($trip['arrival_datetime'] ?? '') ?></td>
                        <td><?= (int) ($trip['available_seats'] ?? 0) ?> / <?= (int) ($trip['places_total'] ?? 0) ?></td>
                        <td><?= $escape(($trip['author_first_name'] ?? '') . ' ' . ($trip['author_last_name'] ?? '')) ?></td>
                        <td>
                            <form method="post" action="<?= $escape(base_url('admin/trips/delete')) ?>" style="display:inline;" onsubmit="return confirm('Confirmer la suppression de ce trajet ?');">
                                <input type="hidden" name="id" value="<?= (int) ($trip['id'] ?? 0) ?>">
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>