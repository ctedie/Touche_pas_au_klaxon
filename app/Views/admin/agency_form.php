<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Helpers/functions.php';

/** @var string $pageTitle */
/** @var string $submitLabel */
/** @var string $formAction */
/** @var array<int, string> $errors */
/** @var array<string, mixed> $formData */

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<section class="page-section">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="form-page-card p-4 p-md-5">
                <h1 class="page-title h3"><?= $escape($pageTitle) ?></h1>

                <?php if ($errors !== []): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= $escape($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= $escape(base_url(ltrim($formAction, '/'))) ?>">
                    <div class="mb-4">
                        <label for="nom" class="form-label">Nom de l'agence</label>
                        <input type="text" id="nom" name="nom" class="form-control" value="<?= $escape($formData['nom'] ?? '') ?>" required>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-dark"><?= $escape($submitLabel) ?></button>
                        <a class="btn btn-outline-secondary" href="<?= $escape(base_url('admin/agencies')) ?>">Retour</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>