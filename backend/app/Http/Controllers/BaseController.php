<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BaseController extends Controller
{
    protected string $pageRoot = '';

    protected function page(string $page, array $props = []): Response
    {
        $pageName = trim($this->pageRoot . '/' . ltrim($page, '/'), '/');

        return Inertia::render($pageName, $props);
    }
}
