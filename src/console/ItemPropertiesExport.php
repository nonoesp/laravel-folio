<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

//

class ItemPropertiesExport extends Command
{
    protected $signature = 'folio:prop:export {id} {--dir=} {--replace}';

	protected $description = 'Export item properties to a JSON string.';
	
    public function __construct()
    {
		parent::__construct();
    }

    public function handle()
    {
        try {
            
            // Item id
            $id = $this->argument('id');
            // Save directory            
            $dir = $this->option('dir');
            // Replace output file if it exists
            $replace = $this->option('replace');
            // Find item by id
            $item = \Item::find($id);
            // Encode properties as JSON
            if ($item->properties) {

                $export_name = 'folio-props-'.$item->id.'.json';

                foreach ($item->properties as $p) {
                    if ($p->name == 'export-name') {
                        $export_name = $p->value;
                    }
                }

                $json = json_encode($item->properties);

                // Print JSON
                $this->comment($json);                
                
                if ($dir) {
                    $this->newLine();
                    $export_path = $dir;
                    if (!\Str::contains($dir, ['.json'])) {
                        $export_path = \Str::finish($dir,'/').$export_name;
                    }
                    if (\File::exists($export_path) && !$replace) {
                        $this->error('File exists at '.$export_path);
                        $this->comment('Use --replace flag to override file.');
                    } else {
                        \File::put($export_path, $json, );
                        $this->info('Saved at '.$export_path);
                    }
                }

            } else {
                $this->warn('This item doesn\'t have any properties.');
            }

        } catch (ProcessFailedException $exception) {
            return $this->error('Exporting item properties failed.');
        }
    }
}