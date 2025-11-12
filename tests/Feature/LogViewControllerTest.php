<?php declare(strict_types=1);

use Illuminate\Support\Facades\File;

describe('log page rendering', function () {
    it('displays log page with lines when file exists', function () {
        $response = $this->get('/log');

        $response->assertStatus(200)
            ->assertViewIs('log-viewer::logs')
            ->assertViewHasAll(['lines', 'files', 'selectedFile']);
    });

    it('displays log page with empty lines when file does not exist', function () {
        foreach ($this->files as $file) {
            File::delete($file);
        }

        $response = $this->get('/log');

        $response->assertStatus(200)
            ->assertViewIs('log-viewer::logs')
            ->assertViewHas('lines', []);
    });

    it('displays log lines in reverse chronological order', function () {
        $response = $this->get('/log');

        $lines = $response->viewData('lines');

        expect($lines[0])->toContain('recent log')
            ->and($lines[1])->toContain('old log');
    });

    it('loads the correct log file when file query parameter is provided', function () {
        $laravelFileLines = [
            '['.now()->format('Y-m-d').' 12:00:00] laravel specific log',
        ];

        File::put($this->files[0], implode("\n", $laravelFileLines) . "\n");

        $response = $this->get('/log?file=laravel.log');

        $response->assertStatus(200);
        $lines = $response->viewData('lines');
        $selectedFile = $response->viewData('selectedFile');

        expect($selectedFile)->toBe('laravel.log')
            ->and($lines[0])->toContain('laravel specific log');
    });

    it('defaults to first log file when invalid file parameter is provided', function () {
        $response = $this->get('/log?file=nonexistent');

        $response->assertStatus(200);
        $selectedFile = $response->viewData('selectedFile');

        expect($selectedFile)->toBe('application.log');
    });

    it('passes all available log files to the view', function () {
        $response = $this->get('/log');

        $files = $response->viewData('files');

        expect($files)->toHaveKeys(['laravel.log', 'application.log']);
    });

    it('does not display the log selector when there is only one log file', function () {
        File::delete($this->files[1]);

        $response = $this->get('/log');

        $response->assertDontSee('Vælg logfil');
    });
});