<?php
//Reports
use BlinkerBoy\Report\ReportController;

Route::prefix('reports')->as('reports.')
    ->middleware(['web', 'auth'])
    ->controller(ReportController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{type}', 'view')->name('view');
        Route::match(['get', 'post'], '/{type}/show', 'show')->name('show');
    });
