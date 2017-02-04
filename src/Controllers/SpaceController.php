<?php

namespace Nonoesp\Space\Controllers;

use Illuminate\Http\Request;
use Item, User; // Must be defined in your aliases
use Nonoesp\Space\Space;
use View;
use Config;
use Authenticate; // Must be installed (nonoesp/authenticate) and defined in your aliases
use App;
use Markdown;

class SpaceController extends Controller
{
	// This is an informal test of some dependencies and features.
  public function helloSpace() {
		return view('space::debug.test')->with(['amount' => 2]);
	}

	public function showHome() {

		// Get user's Twitter handle (or visitor)
		$twitter_handle = Authenticate::isUserLoggedInTwitter();

		// Config variables
		$published_show = Config::get("space.published-show");
		$expected_show = Config::get("space.expected-show");

		$published_existing = Item::published()
    				   		->visibleFor($twitter_handle)
    				   		->count();

		$expected_existing = Item::expected()
    				   	   ->visibleFor($twitter_handle)
    				       ->count();

		if($published_show > $published_existing) {
			$published_show = $published_existing;
		}
		if($expected_show > $expected_existing) {
			$expected_show = $expected_existing;
		}

    	$published_left = $published_existing - $published_show;
    	$expected_left = $expected_existing - $expected_show;

		// Get Items + Items ids

    	$left = Item::published()
    				   ->visibleFor($twitter_handle)
    				   ->count() - $published_show;

    	$items = Item::published()
    					   ->visibleFor($twitter_handle)
    					   ->orderBy('published_at', 'DESC')
    					   ->skip(0)
    					   ->take($published_show)
    					   ->get();

		$ids_array = array();

  		  if ($left > 0)
  		  {
			$ids = Item::published()
	    				  ->visibleFor($twitter_handle)
			              ->select('id','published_at')
	    				  ->orderBy('published_at', 'DESC')
	    				  ->skip($published_show)
	    				  ->take($left)
	    				  ->get();

			foreach($ids as $id)
			{
				array_push($ids_array, $id->id);
			}
	    }

		// Get Expected Items
		$items_expected = Item::expected()
									->visibleFor($twitter_handle)
									->orderBy('published_at', 'DESC')
									->take($expected_show)
									->get();

		return view(Config::get('space.views.admin-menu'))
         ->with([
           'items' => $items,
           'ids' => $ids_array,
           'items_expected' => $items_expected
         ]);
	}

	// Simplify making showHome a generic function, then call it directly from route or from Controller function
	public function showItemTag($tag) {

		// Get user's Twitter handle (or visitor)
		$twitter_handle = Authenticate::isUserLoggedInTwitter();

		// Config variables
		$published_show = Config::get("space.published-show");
		$expected_show = Config::get("space.expected-show");

		$published_existing = Item::withAnyTag($tag)
    					    ->published()
    				   		->visibleFor($twitter_handle)
    				   		->count();

		$expected_existing = Item::withAnyTag($tag)
						   ->expected()
    				   	   ->visibleFor($twitter_handle)
    				       ->count();

		if($published_show > $published_existing) {
			$published_show = $published_existing;
		}
		if($expected_show > $expected_existing) {
			$expected_show = $expected_existing;
		}

    	$published_left = $published_existing - $published_show;
    	$expected_left = $expected_existing - $expected_show;

    	// Get content

    	$items = Item::withAnyTag($tag)
    					   ->published()
    					   ->visibleFor($twitter_handle)
    					   ->orderBy('published_at', 'DESC')
    					   ->take($published_show)
    					   ->get();

		$ids_array = array();

  		if ($published_left > 0)
  		{

			$ids = Item::withAnyTag($tag)
						  ->published()
	    				  ->visibleFor($twitter_handle)
			              ->select('id','published_at')
	    				  ->orderBy('published_at', 'DESC')
	    				  ->skip($published_show)
	    				  ->take($published_left)
	    				  ->get();

			foreach($ids as $id)
			{
				array_push($ids_array, $id->id);
			}
		}

		// Get Expected Items
		$items_expected = Item::withAnyTag($tag)
                          ->expected()
                          ->visibleFor($twitter_handle)
                          ->orderBy('published_at', 'DESC')
                          ->take($expected_show)
                          ->get();

		return view(Config::get('space.views.admin-menu'))->with(
								        [
		             	  			'items' => $items,
		             	  			'ids' => $ids_array,
		             	  			'tag' => $tag,
		             	  			'items_expected' => $items_expected
		             	  		]);
	}

	public function showItem($slug) {

		if($item = Item::whereSlug($slug)->first()) {
			$item->visits++;
			$item->save();
			return view('space::base')->with('item', $item);
		}

	}

	public function showItemWithId($id) {
		$item = Item::withTrashed()->find($id);
		return \Redirect::to(Space::path().$item->slug);
	}

	public function getItemsWithIds() {

		// Set Item Type
		\Input::get('item_type') ? $item_type = \Input::get('item_type') : $item_type = 'DEFAULT_ARTICLE_TYPE';

		// Echo Items
		foreach(\Input::get('ids') as $id) {
			echo view('space::partial.c-item')->
			           with(array('item' => Item::find($id),
			           	  		  'item_type' => $item_type,
			           	  		  'isTitleLinked' => true));
		}
	}

	public function getFeed(Request $request) {

		// create new feed
	    $feed = App::make("feed");

	    // cache the feed for 60 minutes (second parameter is optional)
	    $feed->setCache(60, 'laravelFeedKey');

	    // check if there is cached feed and build new only if is not
	    if (!$feed->isCached())
	    {
	    	$default_author = 'Nono Martínez Alonso';

	       // creating rss feed with our most recent items
		   $feed_show = Config::get('space.feed.show');

	       $items = Item::published()
					          ->public()
					          ->orderBy('published_at', 'DESC')
					          ->rss()
					          ->take($feed_show)
					          ->get();

	       // set your feed's title, description, link, pubdate and language
	       $feed->title = Config::get('space.feed.title');
	       $feed->description = Config::get('space.feed.description');
	       $feed->logo = Config::get('space.feed.logo');
	       $feed->link = \URL::to('/'.Space::path());
	       $feed->setDateFormat('datetime'); // 'datetime', 'timestamp' or 'carbon'
	       $feed->pubdate = $items[0]->published_at;
	       $feed->lang = 'en';
	       $feed->setShortening(false); // true or false
	       $feed->setTextLimit(159); // maximum length of description text
	       $feed->setView("space::feed.rss");

	       foreach ($items as $item)
	       {
	           // set item's title, author, url, pubdate, description and content
	       	   $image_src = Config::get('space.feed.default-image-src');
	       	   $image = '';

			   if ($item->video) {
	       	     $image = '<p><a href="'.$request->root().'/'.Space::path().$item->slug.'">'
	       	   	         .'<img src="'.\Thinker::getVideoThumb($item->video)
	       	   	         .'" alt="'.$item->title.'"></a></p>';
	       	   } else if ($item->image) {
	       	     $image = '<p><img src="'.$item->image.'" alt="'.$item->title.'"></p>';
	       	   }

	       	   if ($item->image_src != '') {
	       	   	$image_src = $item->image_src;
						} else if ($item->image != '') {
	       	   	$image_src = $item->image;
	       	   }

	           $feed->add(
	           	$item->title,
	           	$default_author,
	           	\URL::to(Space::path().$item->slug),
	           	$item->published_at,
	           	\Thinker::limitMarkdownText(Markdown::convertToHtml($item->text), 159, ['sup']),
	           	str_replace('<img', '<img width="100%"', $image.\Markdown::convertToHtml($item->text)),
	           	['url'=>$image_src,'type'=>'image/jpeg']);
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
