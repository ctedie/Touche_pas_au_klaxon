<?php

declare(strict_types=1);

/** @var array<int, array<string, mixed>> $agencies */
/** @var array<string, string> $errors */
/** @var array<string, mixed> $formData */
/** @var array<string, mixed> $currentUser */

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';

$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$basePath = $basePath === '/' ? '' : rtrim($basePath, '/');
?>

<section class="page-section">
    <div class="container px-0">
        <h1 class="page-title">CrÃ©er un trajet</h1>

        <form method="post" action="<?= $basePath ?>/trip/store" novalidate>
            <?php require __DIR__ . '/_form.php'; ?>

            <div class="mt-4 d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-dark">CrÃ©er le trajet</button>
                <a href="<?= $basePath ?>/" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>