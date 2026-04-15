<?php

declare(strict_types=1);

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';

$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$basePath = $basePath === '/' ? '' : rtrim($basePath, '/');
?>

<section class="page-section">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="auth-card p-4 p-md-5">
                <h1 class="page-title h2">Connexion</h1>

                <form method="post" action="<?= $basePath ?>/login/submit">
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button class="btn btn-dark w-100" type="submit">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>