<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

abstract class Controller
{
    protected string $pageRoot = '';

    protected function page(string $page, array $props = []): Response
    {
        $pageName = trim($this->pageRoot . '/' . ltrim($page, '/'), '/');

        return Inertia::render($pageName, $props);
    }
}