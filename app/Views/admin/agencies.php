<?php

declare(strict_types=1);

/** @var array<int, array<string, mixed>> $agencies */

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<div class="container py-4">
    <h1 class="mb-4">Liste des agences</h1>

    <?php if ($agencies === []): ?>
        <p>Aucune agence trouvée.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agencies as $agency): ?>
                    <tr>
                        <td><?= (int) ($agency['id'] ?? 0) ?></td>
                        <td><?= $escape($agency['nom'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
