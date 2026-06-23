<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

/**
 * Dumps the full MySQL database (the merged mh4u.db + Kiranico data) to a single
 * portable SQL script. This is the complete dataset / deploy artifact: a fresh
 * environment can load it directly instead of re-importing from both sources.
 *
 * The dump is written to a gitignored location (never committed).
 */
class ExportSql extends Command
{
    protected $signature = 'mh4u:export-sql {--output= : Destination path (default: database/source/mh4u_full.sql)}';

    protected $description = 'Export the full MySQL database to a portable SQL dump';

    public function handle(): int
    {
        if (config('database.default') !== 'mysql') {
            $this->error('mh4u:export-sql requires the mysql connection (DB_CONNECTION=mysql).');

            return self::FAILURE;
        }

        $host = (string) config('database.connections.mysql.host');
        $port = (string) config('database.connections.mysql.port');
        $database = (string) config('database.connections.mysql.database');
        $username = (string) config('database.connections.mysql.username');
        $password = (string) config('database.connections.mysql.password');

        $output = (string) ($this->option('output') ?: database_path('source/mh4u_full.sql'));
        File::ensureDirectoryExists(dirname($output));

        $this->info("Dumping {$database} to {$output} ...");

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --no-tablespaces --skip-comments %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($output),
        );

        // Pass the password via MYSQL_PWD so it never appears in the process list.
        $result = Process::env(['MYSQL_PWD' => $password])->timeout(300)->run($command);

        if (! $result->successful()) {
            $this->error('mysqldump failed: '.trim($result->errorOutput()));

            return self::FAILURE;
        }

        $this->info(sprintf('Wrote %s (%.1f MB).', $output, File::size($output) / 1_048_576));

        return self::SUCCESS;
    }
}
