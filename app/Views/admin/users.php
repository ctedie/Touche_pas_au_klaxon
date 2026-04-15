<?php

declare(strict_types=1);

/** @var array<int, array<string, mixed>> $users */

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<div class="container py-4">
    <h1 class="mb-4">Liste des utilisateurs</h1>

    <?php if ($users === []): ?>
        <p>Aucun utilisateur trouvé.</p>
    <?php else: ?>
        <table>
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
                        <td><?= $escape($user['role'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
