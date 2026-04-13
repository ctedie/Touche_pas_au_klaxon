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
     * @param string $view
     * @param array<string, mixed> $params
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
