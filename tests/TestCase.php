<?php

namespace Tests;

use NoMilk\LogViewer\LogViewerServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected array $files = [
        'laravel',
        'application',
    ];

    protected function getPackageProviders($app): array
    {
        return [
            LogViewerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('log-viewer.enabled', true);
        $app['config']->set('log-viewer.log_files', $this->files);
        $app['config']->set('log-viewer.retention_weeks', 3);

        if (! is_dir(storage_path('logs'))) {
            mkdir(storage_path('logs'), 0777, true);
        }
    }
}