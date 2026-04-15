<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Helpers/functions.php';

/** @var array<int, array<string, mixed>> $agencies */

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<div class="container py-4">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1 style="margin:0;">Liste des agences</h1>
        <a href="<?= $escape(base_url('admin/agencies/create')) ?>">CrÃ©er une agence</a>
    </div>

    <?php if (count($agencies) === 0): ?>
        <p>Aucune agence trouvÃ©e.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agencies as $agency): ?>
                    <tr>
                        <td><?= (int) ($agency['id'] ?? 0) ?></td>
                        <td><?= $escape($agency['nom'] ?? '') ?></td>
                        <td>
                            <a href="<?= $escape(base_url('admin/agencies/edit?id=' . (int) ($agency['id'] ?? 0))) ?>">Modifier</a>

                            <form method="post" action="<?= $escape(base_url('admin/agencies/delete')) ?>" style="display:inline;" onsubmit="return confirm('Confirmer la suppression de cette agence ?');">
                                <input type="hidden" name="id" value="<?= (int) ($agency['id'] ?? 0) ?>">
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>