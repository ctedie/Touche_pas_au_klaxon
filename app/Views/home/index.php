<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Helpers/functions.php';
require __DIR__ . '/../partials/flash.php';

/** @var array<int, array<string, mixed>> $trips */
/** @var array<string, int|bool> $pagination */

require __DIR__ . '/../layouts/header.php';

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$currentPage = (int) ($pagination['current_page'] ?? 1);
$totalPages = (int) ($pagination['total_pages'] ?? 1);
?>

<h1>Trajets disponibles</h1>

<?php if ($trips === []): ?>
    <p>Aucun trajet disponible pour le moment.</p>
<?php else: ?>
    <ul>
        <?php foreach ($trips as $trip): ?>
            <li>
                <strong>DÃ©part :</strong> <?= $escape($trip['departure_agency']) ?><br>
                <strong>Date de dÃ©part :</strong> <?= $escape($trip['departure_datetime']) ?><br>
                <strong>ArrivÃ©e :</strong> <?= $escape($trip['arrival_agency']) ?><br>
                <strong>Date dâ€™arrivÃ©e :</strong> <?= $escape($trip['arrival_datetime']) ?><br>
                <strong>Places disponibles :</strong> <?= $escape($trip['available_seats']) ?><br>

                <?php if (isset($_SESSION['user']) && is_array($_SESSION['user'])): ?>
                    <a href="<?= $escape(base_url('trip/show?id=' . urlencode((string) $trip['id']))) ?>">Voir le dÃ©tail</a>
                <?php else: ?>
                    <a href="<?= $escape(base_url('login')) ?>">Connectez-vous pour voir le dÃ©tail</a>
                <?php endif; ?>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if ($totalPages > 1): ?>
    <nav aria-label="Pagination des trajets disponibles">
        <p>Page <?= $currentPage ?> sur <?= $totalPages ?></p>

        <div>
            <?php if (!empty($pagination['has_previous_page'])): ?>
                <a href="<?= $escape(base_url('?page=' . (string) $pagination['previous_page'])) ?>">Page prÃ©cÃ©dente</a>
            <?php endif; ?>

            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                <?php if ($page === $currentPage): ?>
                    <strong><?= $page ?></strong>
                <?php else: ?>
                    <a href="<?= $escape(base_url('?page=' . $page)) ?>"><?= $page ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if (!empty($pagination['has_next_page'])): ?>
                <a href="<?= $escape(base_url('?page=' . (string) $pagination['next_page'])) ?>">Page suivante</a>
            <?php endif; ?>
        </div>
    </nav>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php';
