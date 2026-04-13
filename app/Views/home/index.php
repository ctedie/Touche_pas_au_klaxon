<?php

declare(strict_types=1);

$trips = $trips ?? [];

$formatDate = static function ($value): string {
    if (!is_string($value) || $value === '') {
        return '';
    }

    try {
        $date = new DateTimeImmutable($value);
        return $date->format('d/m/Y H:i');
    } catch (Exception $e) {
        return $value;
    }
};
?>

<h1>Trajets disponibles</h1>

<?php if (empty($trips)) : ?>
<p>Aucun trajet disponible</p>
<?php else : ?>

<table class="table">
<thead>
<tr>
<th>DÃ©part</th>
<th>Date dÃ©part</th>
<th>ArrivÃ©e</th>
<th>Date arrivÃ©e</th>
<th>Places</th>
</tr>
</thead>

<tbody>

<?php foreach ($trips as $trip) : ?>

<tr>
<td><?= htmlspecialchars($trip['departure_agency']) ?></td>
<td><?= htmlspecialchars($formatDate($trip['departure_datetime'])) ?></td>

<td><?= htmlspecialchars($trip['arrival_agency']) ?></td>
<td><?= htmlspecialchars($formatDate($trip['arrival_datetime'])) ?></td>

<td><?= htmlspecialchars($trip['available_seats']) ?></td>
</tr>

<?php endforeach; ?>

</tbody>
</table>

<?php endif; ?>
