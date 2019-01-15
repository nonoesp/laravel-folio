<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
{
    protected $signature = 'folio:backup {directory?} {name?} {--email}';

	protected $description = 'Backup the database';

	protected $defaultDirectory = '../storage/app/backup/';
	
    protected $process;
	
    public function __construct()
    {
		parent::__construct();
    }

	public static function constructFilepath($directory, $prefix) {
		return $directory.'/'.BackupDatabase::filename($prefix);
	}

	public static function defaultFilename() {
		return BackupDatabase::filename(BackupDatabase::timestamp());
	}

	public static function filename($prefix = null) {
		if ($prefix) {
			return $prefix.'_'.env('DB_DATABASE').'.sql.gz';
		}
		return env('DB_DATABASE').'.sql.gz';
	}

	public static function timestamp() {
		return \Date::now()->format('ymd').'_'.\Date::now()->format('His');
	}

	public function filepath() {
		if ($this->argument('directory')) {
			if ($this->argument('name')) {
				return $this->argument('directory').'/'.$this->argument('name');
			}
			return $this->argument('directory').'/'.$this->filename($this->timestamp());
		}
		return $this->constructFilepath($this->defaultDirectory, $this->timestamp());
	}

    public function handle()
    {
		$filepath = $this->filepath();

		$this->process = new Process(sprintf(
			'mysqldump -u%s -p%s %s | gzip > %s',
			env('DB_USERNAME'),
			env('DB_PASSWORD'),
			env('DB_DATABASE'),
			$filepath
		));
		
        try {
            $this->process->mustRun();
			$this->info('The backup has been successful.');
	
			$app_url = env('APP_URL');
			$db_name = env('DB_DATABASE');

			if ($this->option('email')) {
				
				$this->info('Sending email.');

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
					->attach($filepath, [
                        'mime' => 'application/gzip',
					]);
				});
			}

        } catch (ProcessFailedException $exception) {
            return $this->error('The backup process has failed.');
        }
    }
}