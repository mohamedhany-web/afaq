<?php

namespace App\Http\Controllers;

use App\Helpers\GoogleMapsHelper;
use App\Helpers\SettingsHelper;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectLocationController extends Controller
{
    /** صفحة عامة — مشاركة موقع المشروع (بدون تسجيل دخول) */
    public function show(Project $project): View
    {
        abort_unless($project->hasMapLocation() || $project->mapPins()->exists(), 404);

        $project->load('mapPins');

        return view('public.project-location', compact('project'));
    }

    /** نافذة عرض من نطاقنا — خريطة / جوي / Street View مدمج */
    public function viewer(Request $request, Project $project): View
    {
        abort_unless($project->hasMapLocation() || $project->mapPins()->exists(), 404);

        $project->load('mapPins');

        $mode = in_array($request->query('mode'), ['map', 'satellite', 'streetview'], true)
            ? $request->query('mode')
            : 'satellite';

        $pins = $this->collectPins($project);

        $streetEmbed = null;
        if ($mode === 'streetview' && $pins->isNotEmpty()) {
            $p = $pins->first();
            $streetEmbed = GoogleMapsHelper::streetViewEmbedUrl($p['lat'], $p['lng']);
            abort_unless($streetEmbed, 404, 'Street View غير متاح — أضف GOOGLE_MAPS_API_KEY');
        }

        $modeLabel = match ($mode) {
            'streetview' => 'Street View',
            'map' => 'خريطة',
            default => 'عرض جوي',
        };

        return view('public.project-map-viewer', [
            'project' => $project,
            'mode' => $mode,
            'modeLabel' => $modeLabel,
            'pins' => $pins,
            'streetEmbed' => $streetEmbed,
            'themeColor' => SettingsHelper::getThemeColor(),
        ]);
    }

    protected function collectPins(Project $project)
    {
        $pins = collect();

        if ($project->hasMapLocation()) {
            $pins->push(['title' => $project->name, 'pin_type' => 'project', 'lat' => (float) $project->latitude, 'lng' => (float) $project->longitude]);
        }

        foreach ($project->mapPins as $pin) {
            if ($pin->pin_type === 'project' && $project->hasMapLocation()) {
                continue;
            }
            $pins->push(['title' => $pin->title, 'pin_type' => $pin->pin_type, 'lat' => (float) $pin->latitude, 'lng' => (float) $pin->longitude]);
        }

        return $pins->values();
    }
}
