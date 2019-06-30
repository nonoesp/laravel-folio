<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

//

class ItemPropertiesExport extends Command
{
    protected $signature = 'folio:prop:export {id}';

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
            // Find item by id
            $item = \Item::find($id);
            // Encode properties as JSON
            if ($item->properties) {
                $json = json_encode($item->properties);
                // Print JSON
                $this->info($json);
            } else {
                $this->warn('This item doesn\'t have any properties.');
            }

        } catch (ProcessFailedException $exception) {
            return $this->error('Exporting item properties failed.');
        }
    }
}