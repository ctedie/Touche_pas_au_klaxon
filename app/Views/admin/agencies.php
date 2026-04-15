<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Helpers/functions.php';

/** @var array<int, array<string, mixed>> $agencies */

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../partials/flash.php';

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<section class="page-section">
    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
        <h1 class="page-title mb-0">Liste des agences</h1>
        <a class="btn btn-dark" href="<?= $escape(base_url('admin/agencies/create')) ?>">CrÃ©er une agence</a>
    </div>

    <?php if ($agencies === []): ?>
        <div class="empty-state">
            <p class="mb-0">Aucune agence trouvÃ©e.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap table-responsive">
            <table class="table table-striped table-hover table-app align-middle mb-0">
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
                                <div class="table-actions">
                                    <a class="btn btn-sm btn-outline-dark" href="<?= $escape(base_url('admin/agencies/edit?id=' . (int) ($agency['id'] ?? 0))) ?>">Modifier</a>

                                    <form method="post" action="<?= $escape(base_url('admin/agencies/delete')) ?>" class="table-inline-form" onsubmit="return confirm('Confirmer la suppression de cette agence ?');">
                                        <input type="hidden" name="id" value="<?= (int) ($agency['id'] ?? 0) ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>