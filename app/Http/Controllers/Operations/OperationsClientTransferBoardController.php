<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Crm\CrmClientTransferBoardController;
use Illuminate\Support\Facades\Auth;

class OperationsClientTransferBoardController extends CrmClientTransferBoardController
{
    protected string $clientsRoutePrefix = 'operations.clients';

    public function __construct(\App\Services\Crm\ClientTransferBoardService $board)
    {
        parent::__construct($board);

        $this->middleware(function ($request, $next) {
            if (! Auth::user()?->canAccessOperations()) {
                abort(403);
            }

            return $next($request);
        });
    }
}
