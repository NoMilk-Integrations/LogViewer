<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use NoMilk\LogViewer\Controllers\LogViewController;

if (config('log-viewer.enabled')) {
    Route::get('/log', LogViewController::class)->name('logs.show');
}