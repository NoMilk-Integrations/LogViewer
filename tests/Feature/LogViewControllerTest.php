<?php declare(strict_types=1);

describe('LogViewController', function () {
    beforeEach(function () {
        $lines = [
            '['.now()->subWeeks(5)->format('Y-m-d').' 12:00:00] old log',
            '['.now()->subWeeks(2)->format('Y-m-d').' 12:00:00] recent log',
        ];

        File::put($this->logPath, implode("\n", $lines) . "\n");
    });

    it('displays log page with lines when file exists', function () {
        $response = $this->get('/log');

        $response->assertStatus(200);
        $response->assertViewIs('log-viewer::logs');
        $response->assertViewHas('lines');
    });

    it('displays log page with empty lines when file does not exist', function () {
        File::delete($this->logPath);

        $response = $this->get('/log');

        $response->assertStatus(200);
        $response->assertViewIs('log-viewer::logs');
        $response->assertViewHas('lines', []);
    });

    it('displays log lines in reverse chronological order', function () {
        $response = $this->get('/log');

        $lines = $response->viewData('lines');

        expect($lines[0])->toContain('recent log')
            ->and($lines[1])->toContain('old log');
    });
});
