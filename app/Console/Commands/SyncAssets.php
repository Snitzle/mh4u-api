<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

/**
 * Flattens the vendored image tree (assets-source/<category>/...) into
 * public/assets/<category>/, so icon_url / map_url resolve locally. In
 * production these files are instead synced to a CDN/object store.
 */
class SyncAssets extends Command
{
    protected $signature = 'mh4u:sync-assets {--fresh : Delete existing target files first}';

    protected $description = 'Flatten the vendored image assets into public/assets for serving';

    public function handle(): int
    {
        $source = base_path('assets-source');

        if (! File::isDirectory($source)) {
            $this->error("Vendored asset directory not found: {$source}");

            return self::FAILURE;
        }

        $target = public_path('assets');
        $totalCopied = 0;

        foreach (File::directories($source) as $categoryPath) {
            $category = basename($categoryPath);
            $destination = "{$target}/{$category}";

            if ($this->option('fresh') && File::isDirectory($destination)) {
                File::deleteDirectory($destination);
            }

            File::ensureDirectoryExists($destination);

            $copied = 0;

            foreach (Finder::create()->files()->in($categoryPath) as $file) {
                File::copy($file->getRealPath(), "{$destination}/{$file->getFilename()}");
                $copied++;
            }

            $this->line(sprintf('  %-14s %4d files', $category, $copied));
            $totalCopied += $copied;
        }

        $this->info("Synced {$totalCopied} asset files to {$target}.");

        return self::SUCCESS;
    }
}
