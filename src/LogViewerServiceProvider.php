<?php declare(strict_types=1);

namespace NoMilk\LogViewer;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use NoMilk\LogViewer\Commands\PruneLogsCommand;

class LogViewerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/log-viewer.php',
            'log-viewer'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/config/log-viewer.php' => config_path('log-viewer.php'),
        ], 'log-viewer-config');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/log-viewer'),
        ], 'log-viewer-views');

        $this->loadViewsFrom(__DIR__.'/resources/views', 'log-viewer');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                PruneLogsCommand::class,
            ]);
        }

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $frequency = config('log-viewer.schedule', 'mondays');

            $schedule->command('nomilk:logs:prune')->{$frequency}()->at('00:00');
        });
    }
}
