<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Crm\CrmProjectUnitController;
use Illuminate\Support\Facades\Auth;

class OperationsProjectUnitController extends CrmProjectUnitController
{
    public function __construct(
        \App\Services\ProjectManagementService $projects,
        \App\Services\ProjectUnitGeneratorService $generator,
    ) {
        parent::__construct($projects, $generator);

        $this->middleware(function ($request, $next) {
            if (! Auth::user()?->canAccessOperations()) {
                abort(403);
            }

            return $next($request);
        });
    }
}
