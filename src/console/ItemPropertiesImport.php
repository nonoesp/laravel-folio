<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

//

class ItemPropertiesImport extends Command
{
    protected $signature = 'folio:prop:import {id} {json}';

	protected $description = 'Import item properties from a JSON string.';
	
    public function __construct()
    {
		parent::__construct();
    }

    public function handle()
    {
        try {
            
            // Item id
            $id = $this->argument('id');
            // JSON string with properties
            $json = $this->argument('json');            
            // Find item by id
            $item = \Item::find($id);
            if ($item) {
                $this->info("Found item $id.");
            } else {
                $this->warn("Item $id doesn't exist.");
                return;
            }
            // Decode properties as JSON
            foreach(json_decode($json) as $prop) {
                $property = new \Property();
                $property->item_id = $id;
                $property->label = $prop->label;
                $property->name = $prop->name;
                $property->value = $prop->value;
                $property->save();
            }            

        } catch (ProcessFailedException $exception) {
            return $this->error('Import item properties failed.');
        }
    }
}