<?php

namespace Nonoesp\Folio\Controllers;

use Illuminate\Http\Request;
use Nonoesp\Folio\Folio;
use Item, User;
use Auth;

class FolioController extends Controller
{
	// This is an informal test of some dependencies and features.
  	public function helloFolio($domain) {
		return view('folio::debug.test')->with(['amount' => 2]);
	}

	public function showHome($domain) {

		// Get user's Twitter handle (or visitor)
		// $twitter_handle = Authenticate::isUserLoggedInTwitter();
		$twitter_handle = Auth::check() ? Auth::user()->twitter : null;

		// Config variables
		$published_show = config("folio.published-show");
		$expected_show = config("folio.expected-show");
		
		$blog_items = Item::blog()
							->visibleFor($twitter_handle)
							->orderBy('published_at', 'DESC');

		$blog_items_count = $blog_items->count();
		$blog_items_published_count = $blog_items->published()->count();
		$blog_items_expected_count = $blog_items_count - $blog_items_published_count;

		// Cap showed items to existing or specified in config
		$published_show_count = min([$published_show, $blog_items_published_count]);
		$expected_show_count = min([$expected_show, $blog_items_expected_count]);

    	$published_left_count = $blog_items_published_count - $published_show;
    	$expected_left_count = $blog_items_expected_count - $expected_show;

		// Get published items
    	$collection_published = Item::published()
									->blog()
									->visibleFor($twitter_handle)
									->orderBy('published_at', 'DESC')
									->take($published_show_count)
									->get();

		// Get ids of published items left
		$ids = [];
		if ($published_left_count > 0)
		{
			$ids = Item::published()
						->blog()
						->visibleFor($twitter_handle)
						->select('id','published_at')
						->orderBy('published_at', 'DESC')
						->skip($published_show)
						->take($published_left_count)
						// Only keep an array of ids
						// e.g. [23, 19, 5, 2]
						->pluck('id')
						->toArray();
		}

		// Get expected items
		$collection_expected = Item::expected()
								->blog()
								->visibleFor($twitter_handle)
								->orderBy('published_at', 'DESC')
								->take($expected_show)
								->get();

		return view(config('folio.view.collection'), [
			'collection' => $collection_published,
			'collection_expected' => $collection_expected,
			'ids' => $ids,
			'header_description' => trans('folio.slogan'),
		]);
	}	

	// Simplify making showHome a generic function, then call it directly from route or from Controller function
	public static function showItemTag($domain, $tag) {

		// Get user's Twitter handle (or visitor)
		// $twitter_handle = Authenticate::isUserLoggedInTwitter();
		$twitter_handle = Auth::check() ? Auth::user()->twitter : null;

		// Config variables
		$published_show = config("folio.published-show");
		$expected_show = config("folio.expected-show");

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

    	$published_left_count = $published_existing - $published_show;
    	$expected_left_count = $expected_existing - $expected_show;

    	// Get content

    	$items = Item::withAnyTag($tag)
    					   ->published()
    					   ->visibleFor($twitter_handle)
    					   ->orderBy('published_at', 'DESC')
    					   ->take($published_show)
    					   ->get();

		$ids_array = array();

  		if ($published_left_count > 0)
  		{

			$ids = Item::withAnyTag($tag)
						  ->published()
	    				  ->visibleFor($twitter_handle)
			              ->select('id','published_at')
	    				  ->orderBy('published_at', 'DESC')
	    				  ->skip($published_show)
	    				  ->take($published_left_count)
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

		if($item = Item::bySlug($slug)) {
			if(Auth::guest()) {
				// Count item visit without altering updated_at
				$item->timestamps = false;
				$item->visits++;
				$item->save();
			}

			// $domain returns domain without port, e.g. localhost
			// \Request::getHttpHost() returns domain with port (e.g., localhost:8000)
			// $itemDomain returns domain with port
			// We compare $itemDomain to \Request::getHttpHost()
			$itemDomain = $item->domain();
			if($itemDomain != \Request::getHttpHost()) {
				return \Redirect::to('//'.$itemDomain.'/'.$request->path());
			}
		
				if(
					$item->trashed() or
					\Date::now() < $item->published_at
				) {
					if(($user = Auth::user() and $user->is_admin) or session('temporary-token')) {
					// private and visible (auth ok)
					session(['temporary-token' => false]);
					
					if(
						$user = Auth::user() and
						$user->is_admin
					) {
						if($item->trashed()) {
							$notification = '<a href="/e/'.\Hashids::encode($item->id).'">'.
											'<i class="[ fa fa-link fa--social ]"></i></a>&nbsp;&nbsp;'.
											trans('folio::base.this-page-is-hidden');
						} else {
							$date = Item::formatDate($item->published_at, 'l, F j, Y');
							$is_blog = $item->is_blog ? 'blog' : '<span style="text-decoration: line-through;">blog</span>';
							$is_rss = $item->rss ? 'rss' : '<span style="text-decoration: line-through;">rss</span>';
							$notification = '<a href="/e/'.\Hashids::encode($item->id).'">'.
							'<i class="[ fa fa-link fa--social ]"></i></a>&nbsp;&nbsp;'.
							trans('folio::base.scheduled-for').' '.$date.' &nbsp;·&nbsp; '.$is_blog.' &nbsp;·&nbsp; '.$is_rss;
						}
					} else {
						$notification = trans('folio::base.preview-of-unpublished-page');
					}

					$request->session()->flash('notification', $notification);
				} else {
					// private and hidden (no auth)
					return response()->view('errors.404', [], 404);
				}
			} else {
			// public
			}

			// Get the template view for this item
			$itemTemplateView = $item->templateView();

			// Set to default Folio item view if empty or non-existing
			if(
				!$itemTemplateView ||
				!view()->exists($itemTemplateView)
			) {
				$itemTemplateView = config('folio.view.item');
			} else {
				// Template view $itemTemplateView is good to go!
			}

			// Return XML feed directy if Item is-feed
			if($item->boolProperty('is-feed')) {
				return \Nonoesp\Folio\Controllers\FeedController::makeFeed($request, $domain, $item);
			}

			return view($itemTemplateView, ['item' => $item]);
		}
		
	}

	public function showItemWithId($domain, $id) {
		$item = Item::withTrashed()->find($id);
		if ($item) {
			if ($item->slug[0] == '/') {
    	  		return redirect($item->slug);
    		}
			return redirect(Folio::path().$item->slug);
		}
		return response()->view('errors.404', [], 404);
	}

	public function getItemsWithIds($domain, Request $request) {

		// Set Item Type
		$request->input('item_type') ? $item_type = $request->input('item_type') : $item_type = 'DEFAULT_ITEM_TYPE';

		// Echo Items
		foreach($request->input('ids') as $id) {
      		echo view('folio::partial.c-item-li')->with(['item' => Item::find($id)]);
		}

	}

	public function getUserProfile(Request $request, $domain, $handle) {
		$user = User::where('twitter', '=', $handle)->first();
		if(!$user) {
			return response()->view('errors.404', [], 404);
		}
		return view('folio::profile', ['user' => $user]);
	}

	public static function getItemPreview($domain, Request $request,
	$id, $template, $stripTags = '', $locale = 'undefined', $hash = '') {

		if ($locale && $locale != 'undefined') {
			app()->setLocale($locale);
		}

		$pathStripTags = $stripTags == '' ? $pathStripTags = 'undefined' : $stripTags;

		$item = Item::withTrashed()->find($id);
		
		if (!$item) {
			return 'Item id '.$id.' does not exist.';
		}

		$template = 'template.'.$template;
		$stripTags = explode(',', $stripTags);
		$isAdmin = $user = \Auth::user() and $user->is_admin;

		$pathArray = explode('/', $request->path());
		$path = join(array_slice($pathArray, 0, 3), "/");

		$pathHash = \Hashids::encode($item->id);

		$secretLink = '//'.$request->getHttpHost().'/'.$path.'/'.$pathStripTags.'/'.$locale.'/'.$pathHash;

		if ($isAdmin or $pathHash == $hash) {
			echo '<a href="'.$secretLink.'"><center><span class="u-text-align--center u-opacity--half u-font-size--a">'.$secretLink.'</span></center></a>';
		} else {
			return response()->view('errors.404', [], 404);
		}

		if (!view()->exists($template)) {
			return "Template ".$template." doesn't exist";
		}

		return view($template, ['item' => $item, 'stripTags' => $stripTags]);

	}
	
}
