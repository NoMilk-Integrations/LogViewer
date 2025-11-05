<?php declare(strict_types=1);

describe('PruneLogsCommand', function () {
    beforeEach(function () {
        $lines = [
            '['.now()->subWeeks(5)->format('Y-m-d').' 12:00:00] old log',
            '['.now()->subWeeks(2)->format('Y-m-d').' 12:00:00] recent log',
            '['.now()->format('H:i:s').'] malformed line',
        ];

        File::put($this->logPath, implode("\n", $lines) . "\n");
    });

    it('exits early when log files does not exist.', function () {
        File::delete($this->logPath);

        $this->artisan('nomilk:logs:prune')
            ->assertExitCode(0)
            ->expectsOutputToContain('Log file not found');
    });

    it('keeps logs from within retention period', function () {
        $this->artisan('nomilk:logs:prune')
            ->assertExitCode(0);

        $content = File::get($this->logPath);

        expect($content)->toContain('recent log');
    });

    it('prunes logs older than retention period', function () {
        $this->artisan('nomilk:logs:prune')
            ->assertExitCode(0);

        $content = File::get($this->logPath);

        expect($content)->not->toContain('old log')
            ->and($content)->toContain('recent log');
    });

    it('keeps malformed lines', function () {
        $this->artisan('nomilk:logs:prune')
            ->assertExitCode(0);

        $content = File::get($this->logPath);

        expect($content)->toContain('malformed line');
    });

    it('handles custom retention period', function () {
        config()->set('log-viewer.retention_weeks', 1);

        $this->artisan('nomilk:logs:prune')
            ->assertExitCode(0);

        $content = File::get($this->logPath);

        expect($content)->not->toContain('old log')
            ->and($content)->not->toContain('recent log');
    });

    it('handles empty log file gracefully', function () {
        File::put($this->logPath, '');

        $this->artisan('nomilk:logs:prune')
            ->assertExitCode(0)
            ->expectsOutputToContain('Log pruned successfully.');
    });
});
