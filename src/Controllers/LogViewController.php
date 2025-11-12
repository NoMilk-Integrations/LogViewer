<?php declare(strict_types=1);

namespace NoMilk\LogViewer\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use NoMilk\LogViewer\FileResolver;

class LogViewController extends Controller
{
    public function __invoke(Request $request): View
    {
        $files = FileResolver::getAvailableFiles();
        $selectedFile = $request->query('file', array_key_first($files));

        if (! isset($files[$selectedFile])) {
            $selectedFile = array_key_first($files) ?? null;
        }

        $lines = [];

        if ($selectedFile && isset($files[$selectedFile])) {
            $filePath = FileResolver::resolveFilePath($selectedFile);

            if ($filePath && file_exists($filePath)) {
                $lines = array_reverse(file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
            }
        }

        return view('log-viewer::logs', [
            'lines' => $lines,
            'files' => $files,
            'selectedFile' => $selectedFile,
        ]);
    }
}