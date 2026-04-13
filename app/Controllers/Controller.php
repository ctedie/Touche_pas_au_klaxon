<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

/**
 * ContrÃ´leur de base.
 */
abstract class Controller
{
    /**
     * Rend une vue.
     *
     * @param string $view
     * @param array<string, mixed> $params
     */
    protected function render(string $view, array $params = []): void
    {
        View::render($view, $params);
    }
}
