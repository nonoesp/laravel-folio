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
  public function helloFolio($domain) {
		return view('folio::debug.test')->with(['amount' => 2]);
	}

	public function showHome($domain) {

		// Get user's Twitter handle (or visitor)
		$twitter_handle = Authenticate::isUserLoggedInTwitter();

		// Config variables
		$published_show = Config::get("folio.published-show");
		$expected_show = Config::get("folio.expected-show");

		$published_existing = Item::published()
							->blog()
    				   		->visibleFor($twitter_handle)
    				   		->count();

		$expected_existing = Item::expected()
						   ->blog()
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
					   ->blog()
    				   ->visibleFor($twitter_handle)
    				   ->count() - $published_show;

    	$items = Item::published()
						   ->blog()
    					   ->visibleFor($twitter_handle)
    					   ->orderBy('published_at', 'DESC')
    					   ->skip(0)
    					   ->take($published_show)
    					   ->get();

		$ids_array = array();

  		  if ($left > 0)
  		  {
			$ids = Item::published()
						  ->blog()
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
								->blog()
								->visibleFor($twitter_handle)
								->orderBy('published_at', 'DESC')
								->take($expected_show)
								->get();

		return view(config('folio.view.collection'))
         ->with([
           'collection' => $items,
           'ids' => $ids_array,
           'items_expected' => $items_expected,
		   'header_description' => trans('folio.slogan')
         ]);
	}

	// Simplify making showHome a generic function, then call it directly from route or from Controller function
	public function showItemTag($domain, $tag) {

		// Get user's Twitter handle (or visitor)
		$twitter_handle = Authenticate::isUserLoggedInTwitter();

		// Config variables
		$published_show = Config::get("folio.published-show");
		$expected_show = Config::get("folio.expected-show");

		$published_existing = Item::withAnyTag($tag)
    					    ->published()
    				   		->visibleFor($twitter_handle)
    				   		->count();

		if($published_existing <= 0) {
			return response()->view('errors.404', [], 404);
		}

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

		return view(config('folio.view.collection'))
         ->with([
           'collection' => $items,
           'ids' => $ids_array,
           'items_expected' => $items_expected,
		   'tag' => $tag,
		   'header_description' => trans('folio.slogan')
         ]);

	}

	public static function showItem($domain, Request $request, $slug) {
		
				if($item = Item::withTrashed()->whereSlug($slug)->first() or
			   $item = Item::withTrashed()->whereSlug('/'.$slug)->first() or
			   $item = Item::withTrashed()->whereSlug('/'.Folio::path().$slug)->first() ) {
					$item->timestamps = false;
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
				  
			  if($view = $item->templateView() && view()->exists($item->templateView())) {
					return view($view, ['item' => $item]);
				  }
				return view(config('folio.view.item'), ['item' => $item]);
			}
		
			}

	public function showItemWithId($domain, $id) {
		$item = Item::withTrashed()->find($id);
    if($item->slug[0] == '/') {
      return redirect($item->slug);
    }
		return redirect(Folio::path().$item->slug);
	}

	public function getItemsWithIds($domain) {

		// Set Item Type
		\Input::get('item_type') ? $item_type = \Input::get('item_type') : $item_type = 'DEFAULT_ITEM_TYPE';

		// Echo Items
		foreach(\Input::get('ids') as $id) {
      echo view('folio::partial.c-item-li')->with(['item' => Item::find($id)]);
		}

	}
	
}
