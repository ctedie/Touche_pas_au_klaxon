<?php

declare(strict_types=1);

/** @var array<int, array<string, mixed>> $users */

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<section class="page-section">
    <h1 class="page-title">Liste des utilisateurs</h1>

    <?php if ($users === []): ?>
        <div class="empty-state">
            <p class="mb-0">Aucun utilisateur trouvé.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap table-responsive">
            <table class="table table-striped table-hover table-app align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= (int) ($user['id'] ?? 0) ?></td>
                            <td><?= $escape($user['nom'] ?? '') ?></td>
                            <td><?= $escape($user['prenom'] ?? '') ?></td>
                            <td><?= $escape($user['email'] ?? '') ?></td>
                            <td><?= $escape($user['telephone'] ?? '') ?></td>
                            <td><span class="badge text-bg-secondary"><?= $escape($user['role'] ?? '') ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>