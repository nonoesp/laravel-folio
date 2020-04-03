<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use InvalidArgumentException;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'folio:install {--symlink} {--y|force}';

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

        if ($this->option('symlink')) {
            
            $this->symlinkUploadsFolder();
            
        } else {
            
            $this->symlinkUploadsFolder();
            $this->publishImageAssets();
            $this->publishBuildAssets();
            $this->publishWebpackMix();
            $this->info('Folio was installed successfully.');
        }

    }

    /**
     * Copy build assets.
     */
    protected function publishBuildAssets() {

        $from = __DIR__.'/../../resources/build/';
        $to = public_path().'/';

        foreach (\File::glob(__DIR__.'/../../resources/build/*') as $path) {

            if (is_dir($path)) {
                // Directories
                \File::copyDirectory($from, $to);
            } else {
                // Files
                $toPath = str_replace($from, $to, $path);
                copy($path, $toPath);
            }

        }

    }

    /**
     * Copy Folio image resources.
     */
    protected function publishImageAssets() {

        // https://github.com/laravel/ui/blob/2.x/src/AuthCommand.php#L93-L104

        $destination = public_path('folio/images');
        $imageResourcesPath = __DIR__.'/../../resources/images/';
        $imagePaths =  \File::glob($imageResourcesPath.'*');
        foreach ($imagePaths as $index=>$origin) {
            $filename = str_replace($imageResourcesPath, '', $origin);
            $target = $destination.'/'.$filename;

            if (\File::exists($target)) {
                continue;
            }

            copy($origin, $target);
        }

    }

    /**
     * Copy webpack.mix.js.
     */
    protected function publishWebpackMix() {
        $from = __DIR__.'/../../resources/stubs/webpack.mix.stub';
        $to = base_path('webpack.mix.js');
        $backup = $to.'.bak';

        if (!\File::exists($to) ||
            (
                $this->option('force') ||
                $this->confirm('webpack.mix.js already exists. Do you want to overwrite it?')
            )
            ) {

                // if (\File::exists($to) && !\File::exists($backup)) {
                //     rename($to, $backup);
                // }                
                
                copy($from, $to);
        }

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

        if (! is_dir($directory = public_path('folio'))) {
            mkdir($directory, 0755, true);
        }
    
        if (! is_dir($directory = public_path('folio/images'))) {
            mkdir($directory, 0755, true);
        }

        $this->ensurePublicUploadsSymlinkDirectoryExists();
    }

    /**
     * Ensure public uploads symlink containing folder exists;
     */
    protected function ensurePublicUploadsSymlinkDirectoryExists() {

        $uploadsPublicFolder = config('folio.uploader.public-folder');
        $uploadsPublicFolder = Str::finish(Str::start($uploadsPublicFolder, '/'), '/');

        // Ensure folder exists for symlinking
        $path = '';
        $fragments = explode('/', $uploadsPublicFolder);
        $index = 0;
        foreach($fragments as $fragment) {
            $index++;
            if ($fragment === '' || $index == count($fragments) - 1) {
                continue;
            }
            $path .= '/'.$fragment;

            if (! is_dir($directory = public_path($path))) {
                mkdir($directory, 0755, true);
            }
        }

    }

    /**
     * Symlink uploads folder.
     */
    protected function symlinkUploadsFolder()
    {
        $publicFolder = public_path(config('folio.uploader.public-folder'));

        if (is_dir($publicFolder) &&
            (
            $this->option('force') ||
            $this->confirm('Symlink destination is not empty. Do you want to overwrite it?')
            )
        ) {
                try {
                    unlink($publicFolder);
                }
                catch (\Exception $e) {
                    $this->error('Could not delete '.$publicFolder);
                }
        }

        if (! is_dir($publicFolder)) {
            $uploadsFolder = storage_path('app/public/'.config('folio.uploader.uploads-folder'));
            symlink($uploadsFolder, $publicFolder);
            if ($this->option('symlink')) {
                $this->info('Symlink was created successfully.');
            }
        } else {
            $this->comment('Symlink already exists.');
        }
    }

}