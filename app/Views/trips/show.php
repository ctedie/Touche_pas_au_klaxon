<?php

declare(strict_types=1);

require __DIR__ . '/../layouts/header.php';

/** @var array<string, mixed> $trip */
/** @var array<string, mixed> $currentUser */
/** @var bool $hasReservation */

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$basePath = '/touche-pas-au-klaxon/public';
$isAuthor = (int) ($trip['author_id'] ?? 0) === (int) ($currentUser['id'] ?? 0);
$hasAvailableSeats = (int) ($trip['places_disponibles'] ?? 0) > 0;
$canReserve = !$isAuthor && !$hasReservation && $hasAvailableSeats;
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<div class="container py-4">
    <h1 class="mb-4">DÃ©tail du trajet</h1>

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

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-4">Agence de dÃ©part</dt>
                <dd class="col-sm-8"><?= $escape($trip['departure_agency'] ?? '') ?></dd>

                <dt class="col-sm-4">Date de dÃ©part</dt>
                <dd class="col-sm-8"><?= $escape($trip['date_depart'] ?? '') ?></dd>

                <dt class="col-sm-4">Agence dâ€™arrivÃ©e</dt>
                <dd class="col-sm-8"><?= $escape($trip['arrival_agency'] ?? '') ?></dd>

                <dt class="col-sm-4">Date dâ€™arrivÃ©e</dt>
                <dd class="col-sm-8"><?= $escape($trip['date_arrivee'] ?? '') ?></dd>

                <dt class="col-sm-4">Places totales</dt>
                <dd class="col-sm-8"><?= $escape($trip['places_total'] ?? '') ?></dd>

                <dt class="col-sm-4">Places disponibles</dt>
                <dd class="col-sm-8"><?= $escape($trip['places_disponibles'] ?? '') ?></dd>

                <dt class="col-sm-4">Conducteur</dt>
                <dd class="col-sm-8">
                    <?= $escape(($trip['author_first_name'] ?? '') . ' ' . ($trip['author_last_name'] ?? '')) ?>
                </dd>

                <dt class="col-sm-4">Email</dt>
                <dd class="col-sm-8"><?= $escape($trip['author_email'] ?? '') ?></dd>

                <dt class="col-sm-4">TÃ©lÃ©phone</dt>
                <dd class="col-sm-8"><?= $escape($trip['author_phone'] ?? '') ?></dd>
            </dl>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2 flex-wrap">
        <a href="<?= $basePath ?>/" class="btn btn-outline-secondary">Retour</a>

        <?php if ($canReserve): ?>
            <form action="<?= $basePath ?>/trip/reserve" method="post" class="d-inline">
                <input type="hidden" name="trip_id" value="<?= (int) $trip['id'] ?>">
                <button type="submit" class="btn btn-success">RÃ©server</button>
            </form>
        <?php endif; ?>

        <?php if ($hasReservation): ?>
            <span class="btn btn-outline-success disabled" aria-disabled="true">DÃ©jÃ  rÃ©servÃ©</span>
        <?php endif; ?>

        <?php if (!$isAuthor && !$hasReservation && !$hasAvailableSeats): ?>
            <span class="btn btn-outline-secondary disabled" aria-disabled="true">Trajet complet</span>
        <?php endif; ?>

        <?php if ($isAuthor): ?>
            <span class="btn btn-outline-secondary disabled" aria-disabled="true">Votre trajet</span>
            <a href="<?= $basePath ?>/trip/edit?id=<?= (int) $trip['id'] ?>" class="btn btn-primary">
                Modifier
            </a>

            <form
                action="<?= $basePath ?>/trip/delete"
                method="post"
                class="d-inline"
                onsubmit="return confirm('Confirmer la suppression dÃ©finitive de ce trajet ?');"
            >
                <input type="hidden" name="id" value="<?= (int) $trip['id'] ?>">
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </form>
        <?php endif; ?>
    </div>
</div>
