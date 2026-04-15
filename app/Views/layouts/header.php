<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Helpers/functions.php';

$user = $_SESSION['user'] ?? null;
$isAuthenticated = is_array($user);
$isAdmin = $isAuthenticated && (($user['role'] ?? 'user') === 'admin');
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Touche pas au klaxon</title>
    <link rel="stylesheet" href="<?= $escape(base_url('assets/css/app.css')) ?>">
</head>
<body>
    <div class="site-shell">
        <header class="site-header px-4 py-3 mb-4">
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <a class="site-brand" href="<?= $escape($isAdmin ? base_url('admin') : base_url('')) ?>">Touche pas au klaxon</a>

                <?php if ($isAdmin): ?>
                    <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                        <nav class="d-flex align-items-center gap-2 flex-wrap">
                            <a class="btn btn-secondary" href="<?= $escape(base_url('admin/users')) ?>">Utilisateurs</a>
                            <a class="btn btn-secondary" href="<?= $escape(base_url('admin/agencies')) ?>">Agences</a>
                            <a class="btn btn-secondary" href="<?= $escape(base_url('admin/trips')) ?>">Trajets</a>
                        </nav>
                        <span class="fw-medium">
                            Bonjour <?= $escape($user['prenom'] ?? '') ?> <?= $escape($user['nom'] ?? '') ?>
                        </span>
                        <a class="btn btn-dark" href="<?= $escape(base_url('logout')) ?>">Déconnexion</a>
                    </div>
                <?php elseif ($isAuthenticated): ?>
                    <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                        <a class="btn btn-dark" href="<?= $escape(base_url('trip/create')) ?>">Créer un trajet</a>
                        <a class="btn btn-outline-secondary" href="<?= $escape(base_url('reservations')) ?>">Mes réservations</a>
                        <span class="fw-medium">
                            Bonjour <?= $escape($user['prenom'] ?? '') ?> <?= $escape($user['nom'] ?? '') ?>
                        </span>
                        <a class="btn btn-dark" href="<?= $escape(base_url('logout')) ?>">Déconnexion</a>
                    </div>
                <?php else: ?>
                    <a class="btn btn-dark" href="<?= $escape(base_url('login')) ?>">Connexion</a>
                <?php endif; ?>
            </div>
        </header>
    </div>

    <main class="site-main pb-4">