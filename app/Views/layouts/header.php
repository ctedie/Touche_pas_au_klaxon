<?php

declare(strict_types=1);

$user = $_SESSION['user'] ?? null;
$isAuthenticated = is_array($user);
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
        <a href="/touche-pas-au-klaxon/public/">Touche pas au klaxon</a>
    </div>

    <div>
        <?php if ($isAuthenticated): ?>
            <a href="/touche-pas-au-klaxon/public/trip/create">Proposer un trajet</a>
            <span>
                <?= htmlspecialchars((string) ($user['prenom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                <?= htmlspecialchars((string) ($user['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            </span>
            <a href="/touche-pas-au-klaxon/public/logout">DÃ©connexion</a>
        <?php else: ?>
            <a href="/touche-pas-au-klaxon/public/login">Connexion</a>
        <?php endif; ?>
    </div>
</header>