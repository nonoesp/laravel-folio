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

class FolioController extends Controller
{
	// This is an informal test of some dependencies and features.
  public function helloFolio() {
		return view('folio::debug.test')->with(['amount' => 2]);
	}

	public function showHome() {

		// Get user's Twitter handle (or visitor)
		$twitter_handle = Authenticate::isUserLoggedInTwitter();

		// Config variables
		$published_show = Config::get("folio.published-show");
		$expected_show = Config::get("folio.expected-show");

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

		return view(Config::get('folio.view.items'))
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
		$published_show = Config::get("folio.published-show");
		$expected_show = Config::get("folio.expected-show");

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

		return view(Config::get('folio.view.items'))->with(
								        [
		             	  			'items' => $items,
		             	  			'ids' => $ids_array,
		             	  			'tag' => $tag,
		             	  			'items_expected' => $items_expected
		             	  		]);
	}

	public function showItem(Request $request, $slug) {

		if($item = Item::withTrashed()->whereSlug($slug)->first() or
       $item = Item::withTrashed()->whereSlug('/'.$slug)->first() or
       $item = Item::withTrashed()->whereSlug('/'.Folio::path().$slug)->first() ) {
			$item->visits++;
			$item->save();

      if($item->trashed() or \Date::now() < $item->published_at) {
        if(($user = Auth::user() and $user->is_admin) or session('temporary-token')) {
          // private and visible (auth ok)
          session(['temporary-token' => false]);
          $request->session()->flash('notification', trans('folio::base.preview-notification'));
        } else {
          // private and hidden (no auth)
          return response()->view('errors.404', [], 404);
        }
      } else {
        // public
      }

      if($view = $item->templateView()) {
        return view($view, ['item' => $item]);
      }
			return view('folio::template._standard', ['item' => $item]);
		}

	}

	public function showItemWithId($id) {
		$item = Item::withTrashed()->find($id);
    if($item->slug[0] == '/') {
      return redirect($item->slug);
    }
		return redirect(Folio::path().$item->slug);
	}

	public function getItemsWithIds() {

		// Set Item Type
		\Input::get('item_type') ? $item_type = \Input::get('item_type') : $item_type = 'DEFAULT_ITEM_TYPE';

		// Echo Items
		foreach(\Input::get('ids') as $id) {
      echo view('folio::partial.c-item-li')->with(['item' => Item::find($id)]);
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
	           // set item's title, author, url, pubdate, description and content
	       	   $image_src = Config::get('folio.feed.default-image-src');
	       	   $image = '';
             $item_image = '';

             // Make sure $item->image is global (not local like /img/u/image.jpg)
             if ($item->image && substr($item->image, 0, 1) == '/') {
                 $item_image = $request->root().$item->image;
             }

             // link
             $URL = \URL::to($request->root().'/'.$item->path());
			 $itemURL = $URL;
             if($item->link) {
               $URL = $item->link;
             }

			       if ($item->video) {
	       	     $image = '<p><a href="'.$URL.'">'
	       	   	         .'<img src="'.\Thinker::getVideoThumb($item->video)
	       	   	         .'" alt="'.$item->title.'"></a></p>';
	       	   } else if ($item->image) {
	       	     $image = '<p><img src="'.$item_image.'" alt="'.$item->title.'"></p>';
	       	   }

	       	   if ($item->image_src != '') {
	       	   	$image_src = $item->image_src;
						} else if ($item->image != '') {
	       	   	$image_src = $item_image;
	       	   }

			 // text
			 $text = str_replace(['<img', 'src="/'],
			 					 ['<img width="100%"', 'src="'.$request->root().'/'],
			 					 $image.\Markdown::convertToHtml($item->text));

	           $feed->add(
	           	$item->title,
	           	$default_author,
	           	$URL,
	           	$item->published_at,
	           	\Thinker::limitMarkdownText(Markdown::convertToHtml($item->text), 159, ['sup']),
	           	$text,
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
