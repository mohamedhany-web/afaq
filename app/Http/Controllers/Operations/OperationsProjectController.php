<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Crm\CrmProjectController;
use Illuminate\Support\Facades\Auth;

class OperationsProjectController extends CrmProjectController
{
    protected string $projectsRoutePrefix = 'operations.projects';

    public function __construct(
        \App\Services\ProjectManagementService $projects,
        \App\Services\ProjectApprovalService $approval,
    ) {
        parent::__construct($projects, $approval);

        $this->middleware(function ($request, $next) {
            if (! Auth::user()?->canAccessOperations()) {
                abort(403);
            }

            return $next($request);
        });
    }
}
