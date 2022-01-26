<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ItemPropertiesClear extends Command
{
    protected $signature = 'folio:prop:clear {id}';

	protected $description = 'Clear item properties from a given item.';
	
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
            $item = \Item::withTrashed()->find($id);

            if ($item) {
                $this->info("Found item $id.");
            } else {
                $this->warn("Item $id doesn't exist.");
                return;
            }

            // Delete properties
            foreach ($item->properties as $property) {
                $property->versions()->forceDelete();
            }
            $item->properties()->forceDelete();
            $this->comment("Cleared item properties.");

        } catch (ProcessFailedException $exception) {
            return $this->error('Clear item properties failed.');
        }
    }
}