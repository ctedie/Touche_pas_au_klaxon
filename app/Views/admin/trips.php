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

<section class="page-section">
    <h1 class="page-title">Liste des trajets</h1>

    <?php if ($trips === []): ?>
        <div class="empty-state">
            <p class="mb-0">Aucun trajet trouvé.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap table-responsive">
            <table class="table table-striped table-hover table-app align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Départ</th>
                        <th>Arrivée</th>
                        <th>Date départ</th>
                        <th>Date arrivée</th>
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
                            <td><?= $escape(trim((string) (($trip['author_first_name'] ?? '') . ' ' . ($trip['author_last_name'] ?? '')))) ?></td>
                            <td>
                                <form method="post" action="<?= $escape(base_url('admin/trips/delete')) ?>" class="table-inline-form" onsubmit="return confirm('Confirmer la suppression de ce trajet ?');">
                                    <input type="hidden" name="id" value="<?= (int) ($trip['id'] ?? 0) ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
        <div class="pagination-wrap">
            <p class="mb-0">Page <?= $currentPage ?> sur <?= $totalPages ?></p>

            <nav aria-label="Pagination des trajets administrateur">
                <ul class="pagination mb-0">
                    <?php if (!empty($pagination['has_previous_page'])): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $escape(base_url('admin/trips?page=' . (string) $pagination['previous_page'])) ?>">Précédente</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                        <li class="page-item<?= $page === $currentPage ? ' active' : '' ?>">
                            <?php if ($page === $currentPage): ?>
                                <span class="page-link"><?= $page ?></span>
                            <?php else: ?>
                                <a class="page-link" href="<?= $escape(base_url('admin/trips?page=' . $page)) ?>"><?= $page ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endfor; ?>

                    <?php if (!empty($pagination['has_next_page'])): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $escape(base_url('admin/trips?page=' . (string) $pagination['next_page'])) ?>">Suivante</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>