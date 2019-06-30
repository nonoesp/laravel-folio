<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

//

class ItemRetag extends Command
{
    protected $signature = 'folio:retag';

	protected $description = 'Empty existing tags and retag all items.';
	
    public function __construct()
    {
		parent::__construct();
    }

    public function handle()
    {
        try {
            
     // Wipe existing tags
     \DB::table('tagging_tagged')->delete();
     $this->info('Emptied tagging_tagged table.');
     \DB::table('tagging_tags')->delete();
     $this->info('Emptied tagging_tags table.');
 
     // Tag all items
     $items = \Item::withTrashed()->get();
     foreach($items as $item) {
         if($tags_str = $item->tags_str) {
             $tags = explode(",", $tags_str);
             $this->info('Item has '.count($tags).' - '.$tags_str);
             $item->retag($tags);
             $item->save();
         }
     }

        } catch (ProcessFailedException $exception) {
            return $this->error('Re-tagging all items failed.');
        }
    }
}