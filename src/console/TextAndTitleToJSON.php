<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

//

class TextAndTitleToJSON extends Command
{
    protected $signature = 'folio:toJSON';

	protected $description = 'Migrate title and text fields to JSON format for localization support.';
	
    public function __construct()
    {
		parent::__construct();
    }

    public function handle()
    {
        try {
            
            $items = \Item::withTrashed()->get();
            app()->setLocale('en');
            foreach($items as $item) {
                $this->info("Migrated item $item->id.");
                $item->title = $item->title_plain;
                $item->text = $item->text_plain;
                $item->save();
            }
            $this->info('Migration completed.');

        } catch (ProcessFailedException $exception) {
            return $this->error('The migration process has failed.');
        }
    }
}