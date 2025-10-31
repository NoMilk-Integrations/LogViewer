<?php declare(strict_types=1);

namespace NoMilk\LogViewer\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class PruneLogsCommand extends Command
{
    protected $signature = 'nomilk:logs:prune';
    protected $description = 'Prune old log entries based on configured retention period';

    public function handle(): void
    {
        $logPath = storage_path(config('log-viewer.log_path'));
        $retentionWeeks = config('log-viewer.retention_weeks', 3);

        if (! file_exists($logPath)) {
            $this->info('Log file not found at: ' . $logPath);

            return;
        }

        $cutoffDate = Carbon::now()->subWeeks($retentionWeeks);

        $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $recentLines = array_filter($lines, function ($line) use ($cutoffDate) {
            if (preg_match('/\[(\d{4}-\d{2}-\d{2})\s/', $line, $matches)) {
                $lineDate = Carbon::createFromFormat('Y-m-d', $matches[1]);

                return $lineDate->isAfter($cutoffDate);
            }
            return true;
        });

        file_put_contents($logPath, implode("\n", $recentLines) . "\n");

        $this->info("Log pruned successfully. Kept logs from the last {$retentionWeeks} weeks.");
    }
}