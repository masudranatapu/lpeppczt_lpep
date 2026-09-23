<?php

namespace App\Providers;

use App\Models\Business;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(512);
        Blade::include('inc.section-title', 'sectionTitle');

        view()->composer('layouts.dashboard', function ($view) {
            $view->with('business_setting', currentBranch());
        });

        view()->composer(
            ['sale.pos', 'agent-sale.pos', 'sale.posInvoice', 'agent-sale.posInvoice'],
            function ($view) {
                $view->with('business_setting', currentBranch());
            }
        );
    }
}
