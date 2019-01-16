<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

//

class BackupDatabase extends Command
{
    protected $signature = 'folio:backup {directory?} {name?} {--email}';

	protected $description = 'Backup the database';

	protected $defaultDirectory = '../storage/app/backup/';
	
	protected $process;
	
	protected $words = ["vegetarian", "tumble", "tidy", "terms", "beer", "motorist", "talk", "instruction", "leftovers", "discount", "concern", "promote", "wind", "gown", "flow", "touch", "storage", "stage", "axis", "computer virus", "unfair", "swop", "head", "future", "foundation", "branch", "anxiety", "bishop", "nonremittal", "package", "reality", "dump", "discover", "draft", "space", "climate", "father", "can", "burial", "post", "ladder", "cinema", "research", "vigorous", "debut", "composer", "liberal", "shift", "imagine", "amber", "still", "fool", "firm", "bold", "piano", "constituency", "transparent", "ballet", "replace", "enhance", "nun", "navy", "counter", "carrot", "spokesperson", "country", "degree", "location", "full", "bed", "earthquake", "offer", "break", "ally", "modest", "aisle", "clay", "celebration", "depart", "nail", "minor", "drop", "marine", "hardship", "discovery", "gregarious", "customer", "solution", "holiday", "underline", "constitution", "regular", "recruit", "offset", "build", "capital", "benefit", "improve", "freighter", "restaurant", "grandmother", "riot", "waist", "spot", "theme", "ordinary", "prejudice", "collection", "tie", "trail", "liberty", "experienced", "reform", "architecture", "terrify", "prevalence", "year", "will", "sweater", "forecast"];
	
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
	
			$app_url = env('APP_URL');
			$db_name = env('DB_DATABASE');

			$attachment_name = explode("/", $filepath);
			$random_word_a = $this->words[array_rand($this->words)];
			$random_word_b = $this->words[array_rand($this->words)];
			$attachment_name = str_replace(
				".sql.gz",
				'_'.$random_word_a.'-'.$random_word_b.".sql.gz",
				$attachment_name[count($attachment_name)-1]
			);
			$this->info('Database backed up as '.$attachment_name);

			if ($this->option('email')) {
				
				$this->info('Sending email.');

				\Mail::send('email.text',
				['text' =>
					$app_url.' database <strong>'.
					$db_name.'</strong> backed up as '.
					'<strong>'.$filepath.'</strong>!'
				],
				function ($m) use ($attachment_name) {
					$m->from(config('folio.subscribers.from.email'), config('folio.subscribers.from.name'));
					$m->to(config('folio.subscribers.to.email'), config('folio.subscribers.to.name'))
					->subject('Database backup '.config('folio.title-short'))
					->attach($filepath, [
						'mime' => 'application/gzip',
						'as' => $attachment_name,
					]);
				});
			}

        } catch (ProcessFailedException $exception) {
            return $this->error('The backup process has failed.');
        }
    }
}