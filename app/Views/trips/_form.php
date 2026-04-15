<?php

declare(strict_types=1);

/** @var array<int, array<string, mixed>> $agencies */
/** @var array<string, string> $errors */
/** @var array<string, mixed> $formData */
/** @var array<string, mixed> $currentUser */

$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$selectedDepartureAgency = (int) ($formData['agence_depart_id'] ?? 0);
$selectedArrivalAgency = (int) ($formData['agence_arrivee_id'] ?? 0);
$dateDeparture = (string) ($formData['date_depart_form'] ?? '');
$dateArrival = (string) ($formData['date_arrivee_form'] ?? '');
$totalSeats = (int) ($formData['places_total'] ?? 1);
$availableSeats = (int) ($formData['places_disponibles'] ?? 1);
?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">PrÃ©nom</label>
                <input type="text" class="form-control" value="<?= $escape($currentUser['first_name'] ?? '') ?>" disabled>
            </div>

            <div class="col-md-6">
                <label class="form-label">Nom</label>
                <input type="text" class="form-control" value="<?= $escape($currentUser['last_name'] ?? '') ?>" disabled>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="<?= $escape($currentUser['email'] ?? '') ?>" disabled>
            </div>

            <div class="col-md-6">
                <label class="form-label">TÃ©lÃ©phone</label>
                <input type="text" class="form-control" value="<?= $escape($currentUser['phone'] ?? '') ?>" disabled>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="agence_depart_id" class="form-label">Agence de dÃ©part</label>
                <select name="agence_depart_id" id="agence_depart_id" class="form-select<?= isset($errors['agence_depart_id']) ? ' is-invalid' : '' ?>" required>
                    <option value="">Choisir...</option>
                    <?php foreach ($agencies as $agency): ?>
                        <option value="<?= (int) $agency['id'] ?>" <?= $selectedDepartureAgency === (int) $agency['id'] ? 'selected' : '' ?>>
                            <?= $escape($agency['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['agence_depart_id'])): ?>
                    <div class="invalid-feedback"><?= $escape($errors['agence_depart_id']) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label for="agence_arrivee_id" class="form-label">Agence dâ€™arrivÃ©e</label>
                <select name="agence_arrivee_id" id="agence_arrivee_id" class="form-select<?= isset($errors['agence_arrivee_id']) ? ' is-invalid' : '' ?>" required>
                    <option value="">Choisir...</option>
                    <?php foreach ($agencies as $agency): ?>
                        <option value="<?= (int) $agency['id'] ?>" <?= $selectedArrivalAgency === (int) $agency['id'] ? 'selected' : '' ?>>
                            <?= $escape($agency['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['agence_arrivee_id'])): ?>
                    <div class="invalid-feedback"><?= $escape($errors['agence_arrivee_id']) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label for="date_depart" class="form-label">Date et heure de dÃ©part</label>
                <input type="datetime-local" name="date_depart" id="date_depart" class="form-control<?= isset($errors['date_depart']) ? ' is-invalid' : '' ?>" value="<?= $escape($dateDeparture) ?>" required>
                <?php if (isset($errors['date_depart'])): ?>
                    <div class="invalid-feedback"><?= $escape($errors['date_depart']) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label for="date_arrivee" class="form-label">Date et heure dâ€™arrivÃ©e</label>
                <input type="datetime-local" name="date_arrivee" id="date_arrivee" class="form-control<?= isset($errors['date_arrivee']) ? ' is-invalid' : '' ?>" value="<?= $escape($dateArrival) ?>" required>
                <?php if (isset($errors['date_arrivee'])): ?>
                    <div class="invalid-feedback"><?= $escape($errors['date_arrivee']) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label for="places_total" class="form-label">Nombre total de places</label>
                <input type="number" min="1" name="places_total" id="places_total" class="form-control<?= isset($errors['places_total']) ? ' is-invalid' : '' ?>" value="<?= $escape($totalSeats) ?>" required>
                <?php if (isset($errors['places_total'])): ?>
                    <div class="invalid-feedback"><?= $escape($errors['places_total']) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label for="places_disponibles" class="form-label">Places disponibles</label>
                <input type="number" min="0" name="places_disponibles" id="places_disponibles" class="form-control<?= isset($errors['places_disponibles']) ? ' is-invalid' : '' ?>" value="<?= $escape($availableSeats) ?>" required>
                <?php if (isset($errors['places_disponibles'])): ?>
                    <div class="invalid-feedback"><?= $escape($errors['places_disponibles']) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>