<?php declare(strict_types=1);

namespace NoMilk\LogViewer;

use Illuminate\Support\Facades\File;

class FileResolver
{
    public static function getAvailableFiles(): array
    {
        $files = [];
        $directory = 'logs';
        $baseNames = config('log-viewer.log_files', []);

        foreach ($baseNames as $baseName) {
            $pattern = storage_path("{$directory}/{$baseName}*.log");
            $matches = glob($pattern);

            foreach ($matches as $file) {
                $fileName = basename($file);

                $files[$fileName] = $fileName;
            }
        }

        ksort($files);

        return $files;
    }

    public static function resolveFilePath(string $fileName): ?string
    {
        $directory = 'logs';
        $filePath = storage_path("{$directory}/{$fileName}");

        if (File::exists($filePath)) {
            return $filePath;
        }

        return null;
    }
}