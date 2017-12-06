<?php

namespace Nonoesp\Folio\Controllers;

use Illuminate\Http\Request;
use Item, User; // Must be defined in your aliases
use Nonoesp\Folio\Folio;
use View;
use Config;
use Authenticate; // Must be installed (nonoesp/authenticate) and defined in your aliases
use App;
use Markdown;
use Auth;

class FeedController extends Controller
{
	public function getFeedItems(Request $request) {
		return Item::all();
	}

	public function getFeed(Request $request) {

		// create new feed
	    $feed = App::make("feed");

	    // cache the feed for 60 minutes (second parameter is optional)
	    $feed->setCache(60, 'laravelFeedKey-2');

	    // check if there is cached feed and build new only if is not
	    if (!$feed->isCached())
	    {
	        $default_author = Config::get('folio.feed.default-author');
		    $feed_show = Config::get('folio.feed.show');

	        $items = Item::published()
                            ->public()
                            ->orderBy('published_at', 'DESC')
                            ->rss()
                            ->take($feed_show)
                            ->get();

	       // set your feed's title, description, link, pubdate and language
	       $feed->title = Config::get('folio.feed.title');
	       $feed->description = Config::get('folio.feed.description');
	       $feed->logo = Config::get('folio.feed.logo');
	       $feed->link = \URL::to('/'.Folio::path());
	       $feed->setDateFormat('datetime'); // 'datetime', 'timestamp' or 'carbon'
         if(count($items)) $feed->pubdate = $items[0]->published_at;
	       $feed->lang = 'en';
	       $feed->setShortening(false); // true or false
	       $feed->setTextLimit(159); // maximum length of description text
	       $feed->setView("folio::feed.rss");

	       foreach ($items as $item)
	       {
             // Link
             $URL = \URL::to($request->root().'/'.$item->path());
			 $itemURL = $URL;
             if($item->link) {
                $URL = $item->link;
             }
                
            // Image
            
            $image = '';
            $item_image = $item->image;
            $item_image_src = $item->image_src;

             // Make sure $item->image is global (not local like /img/u/image.jpg)
             if ($item->image && substr($item->image, 0, 1) == '/') {
                 $item_image = $request->root().$item->image;
             }

            // And image_src is global or falls back to default
            if($item_image_src == '' && $item->image) {
                $item_image_src = $item_image;
            } else if ($item->image_src && substr($item->image_src, 0, 1) == '/') {
                $item_image_src = $request->root().$item->image_src;
            } else {
                $item_image_src = config('folio.feed.default-image-src');
            }

            if ($item->video) {
                $image = '<p><a href="'.$URL.'">'
                        .'<img src="'.\Thinker::getVideoThumb($item->video)
                        .'" alt="'.$item->title.'"></a></p>';
            } else if ($item->image) {
                $image = '<p><img src="'.$item_image.'" alt="'.$item->title.'"></p>';
            }

			 // text
			 $html = str_replace(['<img', 'src="/'],
			 					 ['<img width="100%"', 'src="'.$request->root().'/'],
			 					 $image.\Markdown::convertToHtml($item->text));

			$html = str_replace(
				["<p><img", "/></p>"],
				["<p class=\"rss__img-wrapper\"><img", "/></p>"],
				$html);		

	           $feed->add(
	           	$item->title,
	           	$default_author,
	           	$URL,
	           	$item->published_at,
	           	\Thinker::limitMarkdownText(Markdown::convertToHtml($item->text), 159, ['sup']),
	           	$html,
	           	['url'=>$item_image_src,'type'=>'image/jpeg']);
	       }

	    }

	    // first param is the feed format
	    // optional: second param is cache duration (value of 0 turns off caching)
	    // optional: you can set custom cache key with 3rd param as string
	    //return $feed->render('atom');
	    return \Response::make($feed->render('rss', -1), 200, array('Content-Type' => 'text/xml', 'Cache-Control' => 'no-cache'));

	    // to return your feed as a string set second param to -1
	    // $xml = $feed->render('atom', -1);
	}

}