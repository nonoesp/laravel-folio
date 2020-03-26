<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'folio:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup a fresh Folio install.';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        $this->ensureDirectoriesExist();
        $this->symlinkUploadsFolder();

        $this->info('Folio was installed successfully.');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function ensureDirectoriesExist()
    {
        if (! is_dir($directory = storage_path('app/public/uploads'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = public_path('img'))) {
            mkdir($directory, 0755, true);
        }        
    }

    /**
     * Symlink uploads folder.
     */
    protected function symlinkUploadsFolder()
    {
        $uploadsFolder = storage_path('app/public/'.config('folio.uploader.uploads-folder'));
        $publicFolder = public_path(config('folio.uploader.public-folder'));
        $this->info($uploadsFolder.' → '.$publicFolder);
        if (! is_dir($publicFolder)) {
            symlink($uploadsFolder, $publicFolder);
        } else {
            $this->comment('Symlink already exists.');
        }
    }

}