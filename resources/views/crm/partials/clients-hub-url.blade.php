@php
    $clientsHubUrl = auth()->user()?->clientsHubUrl($query ?? []) ?? route('crm.pipeline.index');
@endphp
