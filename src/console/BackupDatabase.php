<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup {path?} {email?}';

	protected $description = 'Backup the database';

	protected $defaultDirectory = '../backup/';
	
    protected $process;
	
    public function __construct()
    {
		parent::__construct();
    }

	public static function constructFilepath($directory, $prefix) {
		return $directory.'/'.$prefix.'_'.env('DB_DATABASE').'.sql.gz';
	}

	public static function timestamp() {
		return \Date::now()->format('ymd').'_'.\Date::now()->format('His');
	}

	public function filepath() {
		if ($this->argument('path')) {
			return $this->argument('path');
		}
		return $this->constructFilepath($this->defaultDirectory, $this->timestamp());
	}

    public function handle()
    {
		$this->process = new Process(sprintf(
			'mysqldump -u%s -p%s %s | gzip > %s',
			env('DB_USERNAME'),
			env('DB_PASSWORD'),
			env('DB_DATABASE'),
			$this->filepath()
		));
		
        try {
            $this->process->mustRun();

			$this->info('The backup has been proceed successfully.');
	
			$app_url = env('APP_URL');
			$db_name = env('DB_DATABASE');
			$filepath = $this->filepath();

			if ($this->argument('email')) {
				\Mail::send('email.text',
				['text' =>
					$app_url.' database <strong>'.
					$db_name.'</strong> backed up as '.
					'<strong>'.$filepath.'</strong>!'
				],
				function ($m) use ($filepath) {
					$m->from(config('folio.subscribers.from.email'), config('folio.subscribers.from.name'));
					$m->to(config('folio.subscribers.to.email'), config('folio.subscribers.to.name'))
					->subject('Database backup '.config('folio.title-short'))
					->attach($filepath);
				});
			}

        } catch (ProcessFailedException $exception) {
            return $this->error('The backup process has been failed.');
        }
    }
}