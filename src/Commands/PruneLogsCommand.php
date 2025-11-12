<?php declare(strict_types=1);

namespace NoMilk\LogViewer\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use NoMilk\LogViewer\FileResolver;

class PruneLogsCommand extends Command
{
    protected $signature = 'nomilk:logs:prune';
    protected $description = 'Prune old log entries based on configured retention period';

    public function handle(): void
    {
        $logFiles = collect(FileResolver::getAvailableFiles())
            ->map(fn ($file) => FileResolver::resolveFilePath($file))
            ->toArray();

        $retentionWeeks = config('log-viewer.retention_weeks', 3);

        if (empty($logFiles)) {
            $this->info('No log files configured.');

            return;
        }

        $prunedCount = 0;
        $cutoffDate = Carbon::now()->subWeeks($retentionWeeks);

        foreach ($logFiles as $file) {
            if (! file_exists($file)) {
                $this->warn("Log file not found at: " . basename($file));

                continue;
            }

            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            $recentLines = array_filter($lines, function ($line) use ($cutoffDate) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2})\s/', $line, $matches)) {
                    $lineDate = Carbon::createFromFormat('Y-m-d', $matches[1]);

                    return $lineDate->isAfter($cutoffDate);
                }

                return true;
            });

            file_put_contents($file, implode("\n", $recentLines) . "\n");

            $prunedCount++;

            $this->info("Pruned log file " . basename($file) . " - kept logs from the last {$retentionWeeks} weeks.");
        }

        $this->info("Successfully pruned {$prunedCount} log file(s).");
    }
}