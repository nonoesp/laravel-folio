<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';

	protected $description = 'Backup the database';

	
    protected $process;
	
    public function __construct()
    {
		parent::__construct();
		
		$db_name = env('DB_DATABASE');
		$db_username = env('DB_USERNAME');
		$db_password = env('DB_PASSWORD');
		$filename = $this->filename();
	
		// Execute shell command to dump gzipped database to file
		$this->process = new Process(sprintf(
			'mysqldump -u%s -p%s %s | gzip > %s',
			$db_username,
			$db_password,
			$db_name,
			'../tmp/'.$filename.'.sql.gz'
		));
    }

	public function filename() {
		$db_name = env('DB_DATABASE');
		return \Date::now()->format('ymd').'_'.
			   \Date::now()->format('His').'_'.
				$db_name;
	}

    public function handle()
    {
        try {
            $this->process->mustRun();

			$this->info('The backup has been proceed successfully.');
	
			$app_url = env('APP_URL');
			$db_name = env('DB_DATABASE');
			$filename = $this->filename();

			// Send database via email
			\Mail::send('email.text',
			['text' =>
				$app_url.' database <strong>'.
				$db_name.'</strong> backed up as '.
				'<strong>'.$filename.'.sql.gz</strong>!'
			],
			function ($m) use ($filename) {
				$m->from(config('folio.subscribers.from.email'), config('folio.subscribers.from.name'));
				$m->to(config('folio.subscribers.to.email'), config('folio.subscribers.to.name'))
				->subject('Database backup '.config('folio.title-short'))
				->attach('../tmp/'.$filename.'.sql.gz');
			});
        } catch (ProcessFailedException $exception) {
            $this->error('The backup process has been failed.');
        }
    }
}