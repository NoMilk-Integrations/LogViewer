<?php declare(strict_types=1);

namespace NoMilk\LogViewer\Controllers;

use Illuminate\View\View;
use Illuminate\Routing\Controller;

class LogViewController extends Controller
{
    public function __invoke(): View
    {
        $logPath = storage_path(config('log-viewer.log_path'));

        if (! file_exists($logPath)) {
            return view('log-viewer::logs', ['lines' => []]);
        }

        $lines = array_reverse(file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

        return view('log-viewer::logs', ['lines' => $lines]);
    }
}