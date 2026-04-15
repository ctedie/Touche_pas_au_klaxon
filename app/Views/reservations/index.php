<?php

declare(strict_types=1);

require __DIR__ . '/../layouts/header.php';

/** @var array<int, array<string, mixed>> $reservations */

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$basePath = '/touche-pas-au-klaxon/public';
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<div class="container py-4">
    <h1 class="mb-4">Mes rÃ©servations</h1>

    <?php if (is_string($flashError) && $flashError !== ''): ?>
        <div class="alert alert-danger">
            <?= $escape($flashError) ?>
        </div>
    <?php endif; ?>

    <?php if (is_string($flashSuccess) && $flashSuccess !== ''): ?>
        <div class="alert alert-success">
            <?= $escape($flashSuccess) ?>
        </div>
    <?php endif; ?>

    <?php if ($reservations === []): ?>
        <p>Vous nâ€™avez aucune rÃ©servation pour le moment.</p>
        <a href="<?= $basePath ?>/" class="btn btn-outline-secondary">Retour Ã  lâ€™accueil</a>
    <?php else: ?>
        <div class="d-grid gap-3">
            <?php foreach ($reservations as $reservation): ?>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h5 mb-3">
                            <?= $escape($reservation['departure_agency'] ?? '') ?>
                            â†’
                            <?= $escape($reservation['arrival_agency'] ?? '') ?>
                        </h2>

                        <dl class="row mb-3">
                            <dt class="col-sm-4">DÃ©part</dt>
                            <dd class="col-sm-8"><?= $escape($reservation['date_depart'] ?? '') ?></dd>

                            <dt class="col-sm-4">ArrivÃ©e</dt>
                            <dd class="col-sm-8"><?= $escape($reservation['date_arrivee'] ?? '') ?></dd>

                            <dt class="col-sm-4">Conducteur</dt>
                            <dd class="col-sm-8">
                                <?= $escape(($reservation['author_first_name'] ?? '') . ' ' . ($reservation['author_last_name'] ?? '')) ?>
                            </dd>

                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8"><?= $escape($reservation['author_email'] ?? '') ?></dd>

                            <dt class="col-sm-4">TÃ©lÃ©phone</dt>
                            <dd class="col-sm-8"><?= $escape($reservation['author_phone'] ?? '') ?></dd>
                        </dl>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="<?= $basePath ?>/trip/show?id=<?= (int) $reservation['trajet_id'] ?>" class="btn btn-outline-primary">
                                Voir le trajet
                            </a>

                            <form
                                action="<?= $basePath ?>/reservations/cancel"
                                method="post"
                                class="d-inline"
                                onsubmit="return confirm('Confirmer lâ€™annulation de cette rÃ©servation ?');"
                            >
                                <input type="hidden" name="reservation_id" value="<?= (int) $reservation['id'] ?>">
                                <button type="submit" class="btn btn-danger">Annuler la rÃ©servation</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
