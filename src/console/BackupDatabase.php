<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';

	protected $description = 'Backup the database';

	protected $directory = 'backup/';
	
    protected $process;
	
    public function __construct()
    {
		parent::__construct();
	
		$this->process = new Process(sprintf(
			'mysqldump -u%s -p%s %s | gzip > %s',
			env('DB_DATABASE'),
			env('DB_USERNAME'),
			env('DB_PASSWORD'),
			$this->filepath()
		));
    }

	public function filename() {
		$db_name = env('DB_DATABASE');
		return \Date::now()->format('ymd').'_'.
			   \Date::now()->format('His').'_'.
				$db_name;
	}

	public function filepath() {
		return $this->directory.$this->filename().'.sql.gz';
	}

    public function handle()
    {
        try {
			// return 2;
            $this->process->mustRun();

			$this->info('The backup has been proceed successfully.');
	
			$app_url = env('APP_URL');
			$db_name = env('DB_DATABASE');
			$filename = $this->filename();
			$filepath = $this->filepath();

			// Send database via email
			\Mail::send('email.text',
			['text' =>
				$app_url.' database <strong>'.
				$db_name.'</strong> backed up as '.
				'<strong>'.$filename.'.sql.gz</strong>!'
			],
			function ($m) use ($filepath) {
				$m->from(config('folio.subscribers.from.email'), config('folio.subscribers.from.name'));
				$m->to(config('folio.subscribers.to.email'), config('folio.subscribers.to.name'))
				->subject('Database backup '.config('folio.title-short'))
				->attach($filepath);
			});
        } catch (ProcessFailedException $exception) {
            return $this->error('The backup process has been failed.');
        }
    }
}