<?php declare(strict_types=1);

use Illuminate\Support\Facades\File;

describe('prune logs command', function () {
    it('exits early when log files do not exist', function () {
        foreach ($this->files as $file) {
            File::delete($file);
        }

        $this->artisan('nomilk:logs:prune')
            ->assertExitCode(0)
            ->expectsOutputToContain('No log files configured');
    });

    it('keeps logs from within retention period for all files', function () {
        $this->artisan('nomilk:logs:prune')
            ->assertExitCode(0);

        foreach ($this->files as $file) {
            expect(File::get($file))->toContain('recent log');
        }
    });

    it('prunes logs older than retention period for all files', function () {
        $this->artisan('nomilk:logs:prune')
            ->assertExitCode(0);

        foreach ($this->files as $file) {
            expect(File::get($file))->not->toContain('old log')
                ->and(File::get($file))->toContain('recent log');
        }
    });

    it('keeps malformed lines in all files', function () {
        File::append($this->files[0], '['.now()->format('H:i:s').'] malformed line');

        $this->artisan('nomilk:logs:prune')
            ->assertExitCode(0);

        expect(File::get($this->files[0]))->toContain('malformed line');
    });

    it('handles custom retention period', function () {
        config()->set('log-viewer.retention_weeks', 1);

        $this->artisan('nomilk:logs:prune')
            ->assertExitCode(0);

        foreach ($this->files as $file) {
            expect(File::get($file))->not->toContain('old log')
                ->and(File::get($file))->not->toContain('recent log');
        }
    });

    it('handles empty log files gracefully', function () {
        foreach ($this->files as $file) {
            File::put($file, '');
        }

        $this->artisan('nomilk:logs:prune')
            ->assertExitCode(0)
            ->expectsOutputToContain('Successfully pruned');
    });

    it('outputs status for each log file', function () {
        $this->artisan('nomilk:logs:prune')
            ->assertExitCode(0)
            ->expectsOutputToContain('laravel')
            ->expectsOutputToContain('application')
            ->expectsOutputToContain('Successfully pruned 2 log file(s)');
    });
});