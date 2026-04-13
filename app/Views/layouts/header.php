<?php

declare(strict_types=1);

$user = current_user();
$successMessage = flash('success');
$errorMessage = flash('error');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= e($pageTitle ?? 'Touche pas au klaxon') ?></title>
</head>
<body>
    <header>
        <div>
            <a href="<?= e(base_url()) ?>">Touche pas au klaxon</a>
        </div>

        <nav>
            <?php if ($user === null): ?>
                <a href="<?= e(base_url('login')) ?>">Connexion</a>
            <?php else: ?>
                <span>
                    <?= e((string) $user['prenom']) ?> <?= e((string) $user['nom']) ?>
                    (<?= e((string) $user['role']) ?>)
                </span>
                |
                <a href="<?= e(base_url('logout')) ?>">DÃ©connexion</a>
            <?php endif; ?>
        </nav>
    </header>

    <?php if ($successMessage !== null): ?>
        <div>
            <?= e($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage !== null): ?>
        <div>
            <?= e($errorMessage) ?>
        </div>
    <?php endif; ?>

    <main>