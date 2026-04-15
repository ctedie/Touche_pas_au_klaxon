<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Helpers/functions.php';

/** @var array<int, array<string, mixed>> $trips */
/** @var array<string, int|bool> $pagination */

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$currentPage = (int) ($pagination['current_page'] ?? 1);
$totalPages = (int) ($pagination['total_pages'] ?? 1);
?>

<div class="container py-4">
    <h1>Liste des trajets</h1>

    <?php if ($trips === []): ?>
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

    <?php if ($totalPages > 1): ?>
        <nav aria-label="Pagination des trajets administrateur">
            <p>Page <?= $currentPage ?> sur <?= $totalPages ?></p>

            <div>
                <?php if (!empty($pagination['has_previous_page'])): ?>
                    <a href="<?= $escape(base_url('admin/trips?page=' . (string) $pagination['previous_page'])) ?>">Page prÃ©cÃ©dente</a>
                <?php endif; ?>

                <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                    <?php if ($page === $currentPage): ?>
                        <strong><?= $page ?></strong>
                    <?php else: ?>
                        <a href="<?= $escape(base_url('admin/trips?page=' . $page)) ?>"><?= $page ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if (!empty($pagination['has_next_page'])): ?>
                    <a href="<?= $escape(base_url('admin/trips?page=' . (string) $pagination['next_page'])) ?>">Page suivante</a>
                <?php endif; ?>
            </div>
        </nav>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
