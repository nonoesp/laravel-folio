<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ItemPropertiesImport extends Command
{
    protected $signature = 'folio:prop:import {id} {json?} {--clear} {--file=}';

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
            
            // File containing JSON string with properties
            $file = $this->option('file');

            // Clear existing properties before importing
            $clear = $this->option('clear');            
            
            // Find item by id
            $item = \Item::withTrashed()->find($id);

            if ($item) {
                $this->info("Found item $id.");
            } else {
                $this->warn("Item $id doesn't exist.");
                return;
            }

            // Delete properties
            if ($clear) {
                foreach ($item->properties as $property) {
                    $property->versions()->forceDelete();
                }
                $item->properties()->forceDelete();
                $this->comment("Cleared item properties.");
            }

            // Exit if no JSON string or file path were provided
            if (!$json && !$file) {
                $this->warn("No JSON string or file path were provided.");
                return;
            }

            if ($file) {
                $json = file_get_contents($file);
                $this->comment('Read JSON string from '.$file.'.');
            }

            // Decode properties as JSON, create collection, sort by order_column
            $json_props = collect(json_decode($json));
            $json_props = $json_props->sortBy('order_column');

            foreach($json_props as $prop) {
                $property = new \Property();
                $property->item_id = $id;
                $property->label = $prop->label;
                $property->name = $prop->name;
                $property->value = $prop->value;
                $property->order_column = $prop->order_column;
                $property->save();
            }

            $this->info('Imported '.count($json_props).' properties.');

        } catch (ProcessFailedException $exception) {
            return $this->error('Import item properties failed.');
        }
    }
}