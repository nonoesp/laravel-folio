<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

//

class MigrateTemplate extends Command
{
    protected $signature = 'folio:migrateTemplate {from?} {to?} {--force} {--preview}';

	protected $description = 'Switch all appearances of a template to another.';
	
    public function __construct()
    {
		parent::__construct();
    }

    public function handle()
    {
        try {
            $from = $this->argument('from');    // first argument
            $to = $this->argument('to');        // second argument
            $force = $this->option('force');    // --force
            $preview = $this->option('preview');    // --preview
            
            $fromItems = \Item::withTrashed()->where('template', '=', $from)->get();
            $fromCount = count($fromItems);

            $toItems = \Item::withTrashed()->where('template', '=', $to)->get();
            $toCount = count($toItems);

            $this->info('- - - - - - - - - - - - ');

            $this->info('[ '.$from.' → '.$to.' ]');

            $this->info('- - - - - - - - - - - - ');
            
            $this->info(' [ '.$from.' ]');
            if(!$fromCount) {
                $this->info(' No items with origin template.');
            } else {
                $this->info(' '.$fromCount.' items with origin template.');
                $this->info('- - - - - - - - - - - - ');
            }
            
            foreach($fromItems as $item) {
                $this->info(' · '.$item->title.' ('.$item->id.')');
            }

            $this->info('- - - - - - - - - - - - ');


            $this->info(' [ '.$to.' ]');
            if(!$toCount) {
                $this->info(' No items with destination template.');
            } else {
                $this->info(' '.$toCount.' items with destination template.');
                $this->info('- - - - - - - - - - - - ');
            }

            foreach($toItems as $item) {
                $this->info(' · '.$item->title.' ('.$item->id.')');
            }            

            $this->info('- - - - - - - - - - - - ');

            if ($preview) {
                // Just taking a peek
                $this->warn(' Just taking a peek.');

            } else if (!$fromCount) {
                // No origin templates
                $this->warn(' No items have origin template.');
                
            } else if ($fromCount and !$toCount) {
                // Origin templates and no destination templates
                $this->info(' Migrating templates.');
                $this->migrate($fromItems, $to);

            } else if ($toCount and !$force) {
                // Origin and destination templates, must force
                $this->warn(' Some items already have destination template. Use --force to migrate.');

            } else if ($fromCount and $toCount and $force) {
				// Origin and destination templates, forcing
				$this->info('Forcing migration (even when destination template is in use by items).');
                $this->migrate($fromItems, $to);
    
            }
            
            $this->info('- - - - - - - - - - - - ');

        } catch (ProcessFailedException $exception) {
            return $this->error('The migration process has failed.');
        }
    }

    public function migrate($items, $templateTo) {
        foreach($items as $item) {
            $item->template = $templateTo;
            $item->save();
        }
    }
}