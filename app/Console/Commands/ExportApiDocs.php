<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Regenerates the Scribe API docs and copies the OpenAPI spec + Postman
 * collection out of the gitignored storage/ build dir into docs/, so a
 * portable, version-controlled copy can be committed and imported into
 * Bruno / Postman without running the server.
 */
class ExportApiDocs extends Command
{
    protected $signature = 'mh4u:export-docs {--skip-generate : Copy the existing Scribe output without regenerating first}';

    protected $description = 'Generate the API spec and export openapi.yaml + Postman collection to docs/';

    /** Scribe output filename => committed filename. */
    private const FILES = [
        'openapi.yaml' => 'openapi.yaml',
        'collection.json' => 'postman_collection.json',
    ];

    public function handle(): int
    {
        if (! $this->option('skip-generate')) {
            $this->info('Generating API documentation...');

            if ($this->call('scribe:generate') !== self::SUCCESS) {
                $this->error('scribe:generate failed; aborting export.');

                return self::FAILURE;
            }
        }

        $source = storage_path('app/private/scribe');
        $target = base_path('docs');

        File::ensureDirectoryExists($target);

        foreach (self::FILES as $from => $to) {
            $fromPath = "{$source}/{$from}";

            if (! File::exists($fromPath)) {
                $this->error("Expected Scribe output not found: {$fromPath}");
                $this->line('Run without --skip-generate, or check config/scribe.php has openapi + postman enabled.');

                return self::FAILURE;
            }

            File::copy($fromPath, "{$target}/{$to}");
            $this->line("  exported  docs/{$to}");
        }

        $this->info('API spec exported to docs/. Commit docs/openapi.yaml and docs/postman_collection.json.');

        return self::SUCCESS;
    }
}
