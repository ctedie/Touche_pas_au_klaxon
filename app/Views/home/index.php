<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Helpers/functions.php';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';

/** @var array<int, array<string, mixed>> $trips */
/** @var array<string, int|bool> $pagination */

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$currentPage = (int) ($pagination['current_page'] ?? 1);
$totalPages = (int) ($pagination['total_pages'] ?? 1);
$isAuthenticated = isset($_SESSION['user']) && is_array($_SESSION['user']);
?>

<section class="page-section">
    <div class="home-intro">
        <h1 class="page-title">Trajets proposés</h1>
        <?php if (!$isAuthenticated): ?>
            <p class="lead mb-0">Pour obtenir plus d'informations sur un trajet, veuillez vous connecter.</p>
        <?php endif; ?>
    </div>

    <?php if ($trips === []): ?>
        <div class="empty-state">
            <p class="mb-0">Aucun trajet disponible pour le moment.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap table-responsive">
            <table class="table table-striped table-hover table-app align-middle mb-0">
                <thead>
                    <tr>
                        <th>Départ</th>
                        <th>Date départ</th>
                        <th>Arrivée</th>
                        <th>Date arrivée</th>
                        <th>Places disponibles</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trips as $trip): ?>
                        <tr>
                            <td><?= $escape($trip['departure_agency']) ?></td>
                            <td><?= $escape($trip['departure_datetime']) ?></td>
                            <td><?= $escape($trip['arrival_agency']) ?></td>
                            <td><?= $escape($trip['arrival_datetime']) ?></td>
                            <td><?= $escape($trip['available_seats']) ?></td>
                            <td>
                                <?php if ($isAuthenticated): ?>
                                    <a class="btn btn-sm btn-outline-dark" href="<?= $escape(base_url('trip/show?id=' . urlencode((string) $trip['id']))) ?>">Voir le détail</a>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-dark" href="<?= $escape(base_url('login')) ?>">Connexion</a>
                                <?php endif; ?>
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

            <nav aria-label="Pagination des trajets disponibles">
                <ul class="pagination mb-0">
                    <?php if (!empty($pagination['has_previous_page'])): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $escape(base_url('?page=' . (string) $pagination['previous_page'])) ?>">Précédente</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                        <li class="page-item<?= $page === $currentPage ? ' active' : '' ?>">
                            <?php if ($page === $currentPage): ?>
                                <span class="page-link"><?= $page ?></span>
                            <?php else: ?>
                                <a class="page-link" href="<?= $escape(base_url('?page=' . $page)) ?>"><?= $page ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endfor; ?>

                    <?php if (!empty($pagination['has_next_page'])): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $escape(base_url('?page=' . (string) $pagination['next_page'])) ?>">Suivante</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>