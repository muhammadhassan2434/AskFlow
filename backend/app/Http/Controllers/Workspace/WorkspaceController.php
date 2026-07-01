<?php

namespace App\Http\Controllers\Workspace;

use App\Exceptions\WorkspaceInUseException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceRequest;
use App\Models\Workspace;
use App\Services\Workspace\WorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class WorkspaceController extends BaseController
{
    protected string $pageRoot = 'Workspaces';

    public function __construct(private WorkspaceService $workspaceService)
    {
    }

    public function index(Request $request): Response
    {
        return $this->page('Index', [
            'workspaces' => $this->workspaceService->list($request->user()),
        ]);
    }

    public function create(): Response
    {
        return $this->page('Create');
    }

    public function store(StoreWorkspaceRequest $request): RedirectResponse
    {
        $this->workspaceService->create($request->user(), $request->validated());

        return $this->redirectResponse(
            route('workspaces.index'),
            'success',
            'Workspace created successfully.'
        );
    }

    public function edit(Request $request, Workspace $workspace): Response
    {
        abort_if($workspace->owner_id !== $request->user()->id, 403);

        $workspace->loadCount('bots');

        return $this->page('Edit', [
            'workspace' => $workspace,
        ]);
    }

    public function update(UpdateWorkspaceRequest $request, Workspace $workspace): RedirectResponse
    {
        try {
            $this->workspaceService->update(
                $request->user(),
                $workspace,
                $request->validated()
            );
        } catch (WorkspaceInUseException $exception) {
            return $this->redirectResponse(
                route('workspaces.edit', $workspace),
                'error',
                $exception->getMessage()
            );
        }

        return $this->redirectResponse(
            route('workspaces.index'),
            'success',
            'Workspace updated successfully.'
        );
    }

    public function destroy(Request $request, Workspace $workspace): RedirectResponse
    {
        abort_if($workspace->owner_id !== $request->user()->id, 403);

        try {
            $this->workspaceService->delete($request->user(), $workspace);
        } catch (WorkspaceInUseException $exception) {
            return $this->redirectResponse(
                route('workspaces.index'),
                'error',
                $exception->getMessage()
            );
        }

        return $this->redirectResponse(
            route('workspaces.index'),
            'success',
            'Workspace deleted successfully.'
        );
    }
}
