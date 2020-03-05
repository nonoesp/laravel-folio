<?php

namespace Nonoesp\Folio\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

//

use Spatie\Sitemap\Tags\Url;
use Spatie\Sitemap\SitemapGenerator;
use Nonoesp\Folio\Folio;

class GenerateSitemap extends Command
{
    protected $signature = 'folio:sitemap {url?} {filepath?} {disk?}';

	protected $description = 'Generate a sitemap of the site';

    protected $defaultFilepath = 'public/sitemap.xml';

	protected $process;
	
    public function __construct()
    {
		parent::__construct();
    }

    public function handle()
    {
        // Get defaults
        $filepath = $this->defaultFilepath;
        $url = \Request::getHttpHost();
        if (\Request::secure()) {
            $url = 'https://'.$url;
        } else {
            $url = 'http://'.$url;
        }

        // Get arguments
        $url = $this->argument('url') ? $this->argument('url') : $url;
        $filepath = $this->argument('filepath') ? $this->argument('filepath') : $filepath;
            
        $this->info(' #######################');
        $this->info(' ## Sitemap Generator ##');
        $this->info(' #######################');

        // if ($this->argument('disk')) {
            // TODO - restore when SitemapGenerator gets writeToDisk method back!
            // $disk = $this->argument('disk');
            // $disk = null;
        // }

        try {
            $cmd = $this;
            // Get sitemap
            $sitemap = SitemapGenerator::create($url)
                    ->hasCrawled(function (Url $url) use ($cmd) {

                        $urlType = 'FREE';

                        // Sample to get Urls by segment
                        // if ($url->segment(1) === 'contact') {
                        //     return;
                        // }

                        // Find Folio base path
                        $folioPath = '/'.Folio::path();
                        if ($url->path().'/' == $folioPath) {
                            $urlType = 'ROOT';
                            $url->setPriority(1);
                        }

                        // Find Folio tag paths
                        $tagPath = '/'.Folio::path().'tag/';
                        if (substr($url->path(), 0, strlen($tagPath)) == $tagPath) {
                            $urlType = 'TAG';
                            $url->setPriority(0.5);
                        }
                        
                        // Find Folio item paths
                        if (Folio::isFolioURI($url->path())) {
                            $urlType = 'ITEM';
                            $url->setPriority(1);
                        }

                        // Find Folio item edit paths
                        $itemEditPath = '/'.Folio::adminPath().'item/edit/';
                        if (substr($url->path(), 0, strlen($itemEditPath)) == $itemEditPath) {
                            $urlType = 'EDIT';
                        }                   

                        // Write to console
                        $message = ' '.$urlType.' · '.$url->path().' · '.$url->priority;
                        switch ($urlType) {
                            case 'EDIT':
                                $cmd->error($message);
                                break;
                            case 'ITEM':
                            case 'TAG':
                                $cmd->info($message);
                                break;
                            default:
                                $cmd->comment($message);
                                break;
                        }

                        return $url;
                    })
                    ->getSitemap();
            
            // Sample to manually add a Url
            // $sitemap->add(Url::create('/manually-created')
            // ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            // ->setPriority(0.9));

            // Save sitemap
            $sitemap->writeToFile($filepath);
            
            // Write to the console
            $this->info("");
            $this->info(" Saving sitemap of $url to $filepath path..");
            $this->info("");

        } catch (ProcessFailedException $exception) {
            return $this->error('The sitemap generation process has failed.');
        }
    }
}