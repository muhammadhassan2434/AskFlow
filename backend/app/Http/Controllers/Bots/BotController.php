<?php

namespace App\Http\Controllers\Bots;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bot\BotStoreRequest;
use App\Http\Requests\Bot\BotUpdateRequest;
use App\Models\Bot;
use App\Services\Bot\BotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class BotController extends BaseController
{
    protected string $pageRoot = 'Bots';

    public function __construct(
        private BotService $botService
    ) {
    }

    public function index(Request $request): Response
    {
        return $this->page('Index', [
            'bots' => $this->botService->list(
                $request->user()
            ),
        ]);
    }

    public function create(): Response
    {
        return $this->page('Create', [
            'workspaces' => $this->botService->workspaceOptions(
                Auth::user()
            ),
        ]);
    }

   public function store(BotStoreRequest $request): RedirectResponse
{
    $bot = $this->botService->create(
        $request->user(),
        $request->validated()
    );

    return $this->redirectResponse(
        route('bots.index', $bot),
        'success',
        'Bot created successfully. Knowledge sources are being processed.'
    );
}

    public function show(Request $request, Bot $bot): Response
    {
        return $this->page('Show', [
            'bot' => $this->botService->show(
                $request->user(),
                $bot
            ),
        ]);
    }

    public function edit(Request $request, Bot $bot): Response
    {
        return $this->page('Edit', [
            'bot' => $this->botService->show(
                $request->user(),
                $bot
            ),
            'workspaces' => $this->botService->workspaceOptions(
                $request->user()
            ),
        ]);
    }

    public function update(
        BotUpdateRequest $request,
        Bot $bot
    ): RedirectResponse {
        $this->botService->update(
            $request->user(),
            $bot,
            $request->validated()
        );

        return $this->redirectResponse(
            route('bots.index'),
            'success',
            'Bot updated successfully.'
        );
    }

    public function destroy(
        Request $request,
        Bot $bot
    ): RedirectResponse {
        $this->botService->delete(
            $request->user(),
            $bot
        );

        return $this->redirectResponse(
            route('bots.index'),
            'success',
            'Bot deleted successfully.'
        );
    }
}
