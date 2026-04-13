<?php

declare(strict_types=1);

/** @var string $oldEmail */

require __DIR__ . '/../layouts/header.php';
?>

<h1>Connexion</h1>

<form method="post" action="<?= e(base_url('login')) ?>">
    <div>
        <label for="email">Email</label><br>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= e($oldEmail ?? '') ?>"
            required
        >
    </div>

    <div>
        <label for="password">Mot de passe</label><br>
        <input
            type="password"
            id="password"
            name="password"
            required
        >
    </div>

    <div>
        <button type="submit">Se connecter</button>
    </div>
</form>

<?php require __DIR__ . '/../layouts/footer.php'; ?>