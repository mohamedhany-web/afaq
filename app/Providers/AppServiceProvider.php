<?php

namespace App\Providers;

use App\Helpers\SettingsHelper;
use App\Models\ClientMeetingRequest;
use App\Models\ClientSharedDocument;
use App\Models\FinancialInvoice;
use App\Models\Invoice;
use App\Observers\FinancialInvoiceObserver;
use App\Observers\InvoiceObserver;
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