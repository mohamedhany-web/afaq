<?php

namespace App\Http\Controllers\Crm\Concerns;

use App\Models\Client;
use App\Services\Crm\CrmFilterService;
use Illuminate\Http\Request;

trait UsesCrmFilters
{
    protected function crmFilters(Request $request): CrmFilterService
    {
        return CrmFilterService::for($request->user());
    }

    /** @return array<string, mixed> */
    protected function clientFilterViewData(CrmFilterService $filters, Request $request, array $stageLabels, array $statusLabels): array
    {
        $keys = $filters->clientFilterKeys();
        $advanced = ['deal_stage', 'has_deals', 'unassigned', 'client_type', 'lead_source', 'created_from', 'created_to'];

        return [
            'mode' => 'clients',
            'filterKeys' => $keys,
            'advancedKeys' => $advanced,
            'hasActive' => $filters->hasActiveFilters($request, $keys),
            'salesReps' => $filters->salesReps(),
            'showSalesRepFilter' => $filters->showSalesRepFilter(),
            'stageLabels' => $stageLabels,
            'statusOptions' => $statusLabels,
            'leadSources' => Client::leadSourceLabels(),
            'searchPlaceholder' => 'الاسم، الهاتف، البريد، الشركة، أو الملاحظات...',
        ];
    }

    /** @return array<string, mixed> */
    protected function saleFilterViewData(CrmFilterService $filters, Request $request, array $stageLabels, array $preserve = []): array
    {
        $keys = $filters->saleFilterKeys();
        $advanced = ['project_id', 'min_value', 'max_value', 'updated_from', 'updated_to', 'show_closed'];

        return [
            'mode' => 'sales',
            'filterKeys' => $keys,
            'advancedKeys' => $advanced,
            'preserve' => $preserve,
            'hasActive' => $filters->hasActiveFilters($request, $keys),
            'salesReps' => $filters->salesReps(),
            'showSalesRepFilter' => $filters->showSalesRepFilter(),
            'stageLabels' => $stageLabels,
            'stagePlaceholder' => 'كل المراحل النشطة',
            'projects' => $filters->projectsForFilter(),
            'searchPlaceholder' => 'اسم العميل، المشروع، أو وصف الصفقة...',
        ];
    }

    /** @return array<string, mixed> */
    protected function projectFilterViewData(CrmFilterService $filters, Request $request): array
    {
        $keys = $filters->projectFilterKeys();
        $advanced = ['property_type', 'city'];

        return [
            'mode' => 'projects',
            'filterKeys' => $keys,
            'advancedKeys' => $advanced,
            'hasActive' => $filters->hasActiveFilters($request, $keys),
            'listingStatuses' => \App\Models\Project::LISTING_STATUSES,
            'ownershipTypes' => \App\Models\Project::OWNERSHIP_TYPES,
            'propertyTypes' => \App\Models\Project::PROPERTY_TYPES ?? [],
            'searchPlaceholder' => 'اسم المشروع، المدينة، المطور...',
        ];
    }
}
