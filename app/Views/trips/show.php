<?php

declare(strict_types=1);

/** @var array<string, mixed> $trip */
/** @var array<string, mixed> $currentUser */

$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$basePath = $basePath === '/' ? '' : rtrim($basePath, '/');

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$isAuthor = (int) ($currentUser['id'] ?? 0) === (int) ($trip['author_id'] ?? 0);

$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);
?>

<div class="container py-4">
    <h1 class="mb-4">DÃ©tail du trajet</h1>

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
                <dd class="col-sm-8"><?= $escape($trip['places_totales'] ?? '') ?></dd>

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

    <div class="mt-4 d-flex gap-2">
        <a href="<?= $basePath ?>/" class="btn btn-outline-secondary">Retour</a>

        <?php if ($isAuthor): ?>
            <a href="<?= $basePath ?>/trip/edit?id=<?= (int) $trip['id'] ?>" class="btn btn-primary">
                Modifier
            </a>
        <?php endif; ?>
    </div>
</div>