<?php

declare(strict_types=1);

$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$basePath = $basePath === '/' ? '' : rtrim($basePath, '/');

$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

$escape = static fn ($value) =>
    htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<div class="container py-5" style="max-width: 500px">

    <h1 class="mb-4">Connexion</h1>

    <?php if ($flashError): ?>
        <div class="alert alert-danger">
            <?= $escape($flashError) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= $basePath ?>/login/submit">

        <div class="mb-3">
            <label class="form-label">Email</label>

            <input
                type="email"
                name="email"
                class="form-control"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Mot de passe</label>

            <input
                type="password"
                name="password"
                class="form-control"
                required
            >
        </div>

        <button class="btn btn-primary w-100">
            Se connecter
        </button>

    </form>

</div>