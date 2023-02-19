<?php

namespace Ashiful\Exco\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class ClearLogCommand extends Command
{
    protected $signature = 'log:clear {--keep-last : Whether the last log file should be kept}';

    protected $description = 'Remove every log files in the log directory';

    public function __construct(private Filesystem $disk)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $files = $this->getLogFiles();

        if ($this->option('keep-last') && $files->count() >= 1) {
            $files->shift();
        }

        $deleted = $this->delete($files);

        if (!$deleted) {
            $this->info('There was no log file to delete in the log folder');
        } elseif ($deleted == 1) {
            $this->info('1 log file has been deleted');
        } else {
            $this->info($deleted . ' log files have been deleted');
        }
    }

    private function getLogFiles(): Collection
    {
        return Collection::make(
            $this->disk->allFiles(storage_path('logs'))
        )->sortBy('mtime');
    }

    private function delete(Collection $files): int
    {
        return $files->each(function ($file) {
            $this->disk->delete($file);
        })->count();
    }
}
