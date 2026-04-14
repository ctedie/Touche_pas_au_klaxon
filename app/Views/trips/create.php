<?php
/** @var array<string, mixed> $user */
/** @var array<int, array<string, mixed>> $agencies */
/** @var array<string, string> $errors */
/** @var array<string, mixed> $old */
?>

<section class="container py-4">
    <h1 class="mb-4">Proposer un trajet</h1>

    <form action="/touche-pas-au-klaxon/public/trip/store" method="post" class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 mb-3">Informations utilisateur</h2>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label" for="last_name">Nom</label>
                    <input class="form-control" id="last_name" type="text" value="<?= htmlspecialchars((string) $user['nom'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="first_name">PrÃ©nom</label>
                    <input class="form-control" id="first_name" type="text" value="<?= htmlspecialchars((string) $user['prenom'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control" id="email" type="email" value="<?= htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="phone">TÃ©lÃ©phone</label>
                    <input class="form-control" id="phone" type="text" value="<?= htmlspecialchars((string) $user['telephone'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                </div>
            </div>

            <h2 class="h5 mb-3">Informations du trajet</h2>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="departure_agency_id">Agence de dÃ©part</label>
                    <select class="form-select<?= isset($errors['departure_agency_id']) ? ' is-invalid' : '' ?>" id="departure_agency_id" name="departure_agency_id" required>
                        <option value="">Choisir une agence</option>
                        <?php foreach ($agencies as $agency): ?>
                            <option value="<?= (int) $agency['id'] ?>" <?= ((int) ($old['departure_agency_id'] ?? 0) === (int) $agency['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $agency['nom'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['departure_agency_id'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['departure_agency_id'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="arrival_agency_id">Agence dâ€™arrivÃ©e</label>
                    <select class="form-select<?= isset($errors['arrival_agency_id']) ? ' is-invalid' : '' ?>" id="arrival_agency_id" name="arrival_agency_id" required>
                        <option value="">Choisir une agence</option>
                        <?php foreach ($agencies as $agency): ?>
                            <option value="<?= (int) $agency['id'] ?>" <?= ((int) ($old['arrival_agency_id'] ?? 0) === (int) $agency['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $agency['nom'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['arrival_agency_id'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['arrival_agency_id'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="departure_datetime">Date/heure de dÃ©part</label>
                    <input class="form-control<?= isset($errors['departure_datetime']) ? ' is-invalid' : '' ?>" id="departure_datetime" name="departure_datetime" type="datetime-local" value="<?= htmlspecialchars((string) ($old['departure_datetime'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    <?php if (isset($errors['departure_datetime'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['departure_datetime'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="arrival_datetime">Date/heure dâ€™arrivÃ©e</label>
                    <input class="form-control<?= isset($errors['arrival_datetime']) ? ' is-invalid' : '' ?>" id="arrival_datetime" name="arrival_datetime" type="datetime-local" value="<?= htmlspecialchars((string) ($old['arrival_datetime'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    <?php if (isset($errors['arrival_datetime'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['arrival_datetime'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="seat_count">Nombre de places</label>
                    <input class="form-control<?= isset($errors['seat_count']) ? ' is-invalid' : '' ?>" id="seat_count" name="seat_count" type="number" min="1" value="<?= htmlspecialchars((string) ($old['seat_count'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    <?php if (isset($errors['seat_count'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['seat_count'], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary" type="submit">CrÃ©er le trajet</button>
                <a class="btn btn-outline-secondary" href="/touche-pas-au-klaxon/public/">Annuler</a>
            </div>
        </div>
    </form>
</section>