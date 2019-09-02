<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

//

class ItemClone extends Command
{
    protected $signature = 'folio:clone {id} {newName?}';

	protected $description = 'Clone an existing item (and, optionally, provide a title for the copy).';
	
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
            // Clone if item exists
            if ($item) {
                $this->info('Cloning '.$item->title.'..');
                $item->load('properties');
                $new_item = $item->replicate();
                if ($this->argument('newName')) {
                    $new_item->title = $this->argument('newName');
                } else {
                    $new_item->title = $new_item->title.' (copy)';
                }
                $new_item->save();
        
                foreach($item->properties as $property) {
                    $new_property = $property->replicate();
                    $new_property->item_id = $new_item->id;
                    $new_property->save();
                    $this->info(' · '.$property->label.' · '.$property->name.' · '.$property->value);
                }
            } else {
                $this->error('There\'s no item '.$id);
            }

        } catch (ProcessFailedException $exception) {
            return $this->error('Cloning this item failed.');
        }
    }
}