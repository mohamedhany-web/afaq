@php
    $projectsRoutePrefix = $projectsRoutePrefix ?? 'crm.projects';
    $pr = fn (string $action, mixed $params = []) => route($projectsRoutePrefix . '.' . $action, $params);
@endphp
