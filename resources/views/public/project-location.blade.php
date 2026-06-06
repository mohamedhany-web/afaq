<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>موقع {{ $project->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">
@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h1>
        <p class="text-gray-500 mt-1">{{ $project->city }} @if($project->location)— {{ $project->location }}@endif</p>
        @if($project->developer_name)
            <p class="text-sm text-gray-400 mt-1">{{ $project->developer_name }}</p>
        @endif
    </div>

    @include('projects.partials.map-display', ['project' => $project, 'themeColor' => $themeColor])

    <p class="text-center text-xs text-gray-400 mt-6">رابط مشاركة الموقع — {{ config('app.name') }}</p>
</div>
</body>
</html>
