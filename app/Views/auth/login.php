<?php

declare(strict_types=1);

require __DIR__ . '/../layouts/header.php';
?>

<h1>Connexion</h1>

<?php if (isset($_SESSION['flash_error']) && is_string($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<form action="/touche-pas-au-klaxon/public/login/submit" method="post">
    <div>
        <label for="email">Adresse email</label><br>
        <input type="email" id="email" name="email" required>
    </div>

    <div style="margin-top: 12px;">
        <label for="password">Mot de passe</label><br>
        <input type="password" id="password" name="password" required>
    </div>

    <div style="margin-top: 16px;">
        <button type="submit">Se connecter</button>
    </div>
</form>

<?php require __DIR__ . '/../layouts/footer.php'; ?>