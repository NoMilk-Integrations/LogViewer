<?php declare(strict_types=1);

use NoMilk\LogViewer\FileResolver;

describe('file resolution', function () {
    it('finds available log files correctly', function () {
        $files = FileResolver::getAvailableFiles();

        expect($files)->toHaveCount(2)
            ->toHaveKeys(['laravel.log', 'application.log']);
    });

    it('resolves log file paths correctly', function () {
        $files = FileResolver::getAvailableFiles();

        expect($files)->toHaveKey('laravel.log');

        $resolvedFile = FileResolver::resolveFilePath($files['laravel.log']);

        expect($resolvedFile)->toBe(storage_path("logs/laravel.log"));
    });
});
