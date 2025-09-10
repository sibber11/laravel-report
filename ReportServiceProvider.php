<?php

namespace BlinkerBoy\Report;

use BlinkerBoy\Report\Commands\GenerateReportStyles;
use BlinkerBoy\Report\Commands\MakeReport;
use Illuminate\Support\ServiceProvider;

class ReportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            MakeReport::class,
            GenerateReportStyles::class
        ]);
        $this->app->bind('report-type', function ($app) {
            return new Reporter();
        });
        $this->app->singleton(Service::class, function ($app) {
            return new Service();
        });
    }

    public function boot(): void
    {
        $this->app->make(Service::class)->registerReports();
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'report');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        //load config file
        $this->mergeConfigFrom(__DIR__ . '/config/report-generator.php', 'report-generator');

//        $this->publishes([
//            __DIR__ . '/config/report-generator.php' => config_path('report-generator.php'),
//        ],'config');

        $this->publishes([
            __DIR__ . '/tailwind.config.js' => base_path('report-tailwind.config.js'),
            __DIR__ . '/resources/css/report.css' => resource_path('css/report.css'),
            __DIR__ . '/routes/reports.php' => base_path('routes/reports.php'),
            __DIR__ . '/resources/js/Pages/Reports' => resource_path('js/Pages/Reports'),

        ], 'views');
    }
}
