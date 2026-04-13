<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);

$files = [
    $projectRoot . '/app/Controllers/Controller.php' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

/**
 * Contrôleur de base.
 */
abstract class Controller
{
    /**
     * Rend une vue.
     *
     * @param string $view chemin de la vue, par exemple home/index
     * @param array<string, mixed> $params données transmises à la vue
     */
    protected function render(string $view, array $params = []): void
    {
        View::render($view, $params);
    }
}
PHP
,
    $projectRoot . '/app/Core/View.php' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Gestion de l'affichage des vues.
 */
class View
{
    /**
     * Rend une vue.
     *
     * @param string $view chemin de la vue, par exemple home/index
     * @param array<string, mixed> $params données transmises à la vue
     */
    public static function render(string $view, array $params = []): void
    {
        extract($params);

        $viewPath = dirname(__DIR__) . '/Views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            throw new RuntimeException('Vue introuvable : ' . $viewPath);
        }

        require $viewPath;
    }
}
PHP
,
    $projectRoot . '/app/Controllers/HomeController.php' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Trip;

/**
 * Contrôleur de la page d'accueil.
 */
class HomeController extends Controller
{
    /**
     * Affiche la liste des trajets disponibles.
     */
    public function index(): void
    {
        $tripModel = new Trip();

        /** @var array<int, array<string, mixed>> $trips */
        $trips = $tripModel->getAvailableTrips();

        $this->render('home/index', [
            'trips' => $trips,
        ]);
    }
}
PHP
,
    $projectRoot . '/app/Views/home/index.php' => <<<'PHP'
<?php

declare(strict_types=1);

/** @var array<int, array<string, mixed>> $trips */
$trips = $trips ?? [];

/**
 * Formate une date pour l'affichage.
 */
$formatDate = static function (mixed $value): string {
    if (!is_string($value) || $value === '') {
        return '';
    }

    try {
        $date = new DateTimeImmutable($value);
        return $date->format('d/m/Y H:i');
    } catch (Exception $exception) {
        return $value;
    }
};
?>

<h1>Trajets disponibles</h1>

<?php if ($trips === []) : ?>
    <p>Aucun trajet disponible pour le moment.</p>
<?php else : ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Agence de départ</th>
                <th>Date de départ</th>
                <th>Agence d’arrivée</th>
                <th>Date d’arrivée</th>
                <th>Places disponibles</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trips as $trip) : ?>
                <tr>
                    <td><?= htmlspecialchars((string) ($trip['departure_agency'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($formatDate($trip['departure_datetime'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($trip['arrival_agency'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($formatDate($trip['arrival_datetime'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($trip['available_seats'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
PHP
];

foreach ($files as $path => $content) {
    $directory = dirname($path);

    if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
        throw new RuntimeException('Impossible de créer le dossier : ' . $directory);
    }

    if (file_put_contents($path, $content) === false) {
        throw new RuntimeException('Impossible d\'écrire le fichier : ' . $path);
    }
}

echo "Étape 3 appliquée avec succès." . PHP_EOL;