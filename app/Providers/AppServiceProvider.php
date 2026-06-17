<?php

namespace App\Providers;

use App\Helpers\SettingsHelper;
use App\Models\ClientMeetingRequest;
use App\Models\ClientSharedDocument;
use App\Models\FinancialInvoice;
use App\Models\Invoice;
use App\Observers\FinancialInvoiceObserver;
use App\Observers\InvoiceObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Invoice::observe(InvoiceObserver::class);
        FinancialInvoice::observe(FinancialInvoiceObserver::class);

        Route::bind('meetingRequest', function (string $value) {
            return ClientMeetingRequest::where('id', $value)->firstOrFail();
        });

        Route::bind('sharedDocument', function (string $value) {
            return ClientSharedDocument::where('id', $value)->firstOrFail();
        });

        // إظهار/إخفاء عناصر السايدبار حسب الصلاحيات (أي صلاحية من الممرّرة تكفي)
        Blade::if('canNav', function (...$permissions) {
            $user = auth()->user();
            if (! $user) {
                return false;
            }

            foreach ($permissions as $permission) {
                foreach (preg_split('/[|,]/', (string) $permission) as $key) {
                    $key = trim($key);
                    if ($key !== '' && $user->can($key)) {
                        return true;
                    }
                }
            }

            return false;
        });

        View::composer([
            'accounting.*',
            'invoices.*',
            'payments.*',
            'expenses.*',
        ], function ($view) {
            $themeColor = SettingsHelper::getThemeColor();

            $view->with([
                'themeColor' => $themeColor,
                'money' => fn ($v) => SettingsHelper::formatMoney($v),
                'headerStyle' => "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);",
            ]);
        });
    }
}