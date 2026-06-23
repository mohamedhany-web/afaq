<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\Crm\ClientTransferBoardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmClientTransferBoardController extends Controller
{
    protected string $clientsRoutePrefix = 'crm.clients';

    public function __construct(
        protected ClientTransferBoardService $board,
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($this->board->canAccess($user), 403);

        $tab = $request->get('tab', 'clients');
        $search = $request->filled('search') ? trim((string) $request->search) : null;

        $clientBoard = $this->board->clientBoard($user, $search);
        $taskBoard = $tab === 'tasks' ? $this->board->taskBoard($user, $search) : ['columns' => []];

        return view('crm.clients.transfer-board', [
            'tab' => $tab,
            'search' => $search,
            'clientColumns' => $clientBoard['columns'],
            'taskColumns' => $taskBoard['columns'],
            'recentLogs' => $clientBoard['recent_logs'],
            'transferTasksDefault' => $clientBoard['transfer_tasks_default'],
            'clientsRoutePrefix' => $this->clientsRoutePrefix,
            'tasksRoutePrefix' => 'crm.tasks',
        ]);
    }

    protected function boardRoute(array $params = []): string
    {
        return route($this->clientsRoutePrefix . '.transfer-board', $params);
    }
}
