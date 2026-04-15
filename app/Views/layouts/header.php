<?php

declare(strict_types=1);

$user = $_SESSION['user'] ?? null;
$isAuthenticated = is_array($user);
$isAdmin = $isAuthenticated && (($user['role'] ?? 'user') === 'admin');
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Touche pas au klaxon</title>
</head>
<body>
<header>
    <div>
        <a href="<?= $isAdmin ? '/touche-pas-au-klaxon/public/admin' : '/touche-pas-au-klaxon/public/' ?>">Touche pas au klaxon</a>
    </div>

    <div>
        <?php if ($isAdmin): ?>
            <nav>
                <a href="/touche-pas-au-klaxon/public/admin/users">Utilisateurs</a>
                <a href="/touche-pas-au-klaxon/public/admin/agencies">Agences</a>
                <a href="/touche-pas-au-klaxon/public/admin/trips">Trajets</a>
            </nav>
            <span>
                <?= $escape($user['prenom'] ?? '') ?>
                <?= $escape($user['nom'] ?? '') ?>
            </span>
            <a href="/touche-pas-au-klaxon/public/logout">Déconnexion</a>
        <?php elseif ($isAuthenticated): ?>
            <a href="/touche-pas-au-klaxon/public/trip/create">Proposer un trajet</a>
            <span>
                <?= $escape($user['prenom'] ?? '') ?>
                <?= $escape($user['nom'] ?? '') ?>
            </span>
            <a href="/touche-pas-au-klaxon/public/logout">Déconnexion</a>
        <?php else: ?>
            <a href="/touche-pas-au-klaxon/public/login">Connexion</a>
        <?php endif; ?>
    </div>
</header>
<main>
