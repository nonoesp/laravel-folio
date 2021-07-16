<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

//

class ItemPropertiesExport extends Command
{
    protected $signature = 'folio:prop:export {id} {--dir=}';

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
            // Find item by id
            $item = \Item::find($id);
            // Encode properties as JSON
            if ($item->properties) {

                $export_name = 'export.json';

                foreach ($item->properties as $p) {
                    if ($p->name == 'export-name') {
                        $export_name = $p->value;
                    }
                }

                $json = json_encode($item->properties);
                // Print JSON
                $this->info($json);

                if ($dir) {
                    \File::put(\Str::finish($dir,'/').$export_name, $json, );
                }
            } else {
                $this->warn('This item doesn\'t have any properties.');
            }

        } catch (ProcessFailedException $exception) {
            return $this->error('Exporting item properties failed.');
        }
    }
}