<?php

declare(strict_types=1);

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';

/** @var array<int, array<string, mixed>> $reservations */

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$basePath = '/touche-pas-au-klaxon/public';
?>

<section class="page-section">
    <h1 class="page-title">Mes rÃ©servations</h1>

    <?php if ($reservations === []): ?>
        <div class="empty-state">
            <p class="mb-0">Vous n'avez aucune rÃ©servation pour le moment.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($reservations as $reservation): ?>
                <div class="col-xl-6">
                    <div class="card reservation-card shadow-sm border-0">
                        <div class="card-body">
                            <h2 class="h5 mb-3">
                                <?= $escape($reservation['departure_agency'] ?? '') ?> â†’ <?= $escape($reservation['arrival_agency'] ?? '') ?>
                            </h2>

                            <dl class="row mb-4">
                                <dt class="col-sm-5">DÃ©part</dt>
                                <dd class="col-sm-7 mb-2"><?= $escape($reservation['departure_datetime'] ?? '') ?></dd>

                                <dt class="col-sm-5">ArrivÃ©e</dt>
                                <dd class="col-sm-7 mb-2"><?= $escape($reservation['arrival_datetime'] ?? '') ?></dd>

                                <dt class="col-sm-5">Conducteur</dt>
                                <dd class="col-sm-7 mb-2"><?= $escape(($reservation['author_first_name'] ?? '') . ' ' . ($reservation['author_last_name'] ?? '')) ?></dd>

                                <dt class="col-sm-5">Contact</dt>
                                <dd class="col-sm-7 mb-0"><?= $escape($reservation['author_email'] ?? '') ?></dd>
                            </dl>

                            <div class="d-flex gap-2 flex-wrap">
                                <a class="btn btn-outline-dark" href="<?= $basePath ?>/trip/show?id=<?= (int) ($reservation['trip_id'] ?? 0) ?>">Voir le dÃ©tail</a>

                                <form action="<?= $basePath ?>/reservation/cancel" method="post" class="d-inline" onsubmit="return confirm('Confirmer l\'annulation de cette rÃ©servation ?');">
                                    <input type="hidden" name="trip_id" value="<?= (int) ($reservation['trip_id'] ?? 0) ?>">
                                    <button type="submit" class="btn btn-danger">Annuler la rÃ©servation</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>