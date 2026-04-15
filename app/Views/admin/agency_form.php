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

<div class="container py-4">
    <h1><?= $escape($pageTitle) ?></h1>

    <?php if ($errors !== []): ?>
        <div style="border:1px solid red; padding:12px; margin:16px 0;">
            <ul style="margin:0;">
                <?php foreach ($errors as $error): ?>
                    <li><?= $escape($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= $escape(base_url(ltrim($formAction, '/'))) ?>">
        <div style="margin-bottom:16px;">
            <label for="nom">Nom de lâ€™agence</label><br>
            <input
                type="text"
                id="nom"
                name="nom"
                value="<?= $escape($formData['nom'] ?? '') ?>"
                required
            >
        </div>

        <button type="submit"><?= $escape($submitLabel) ?></button>
        <a href="<?= $escape(base_url('admin/agencies')) ?>">Retour</a>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>