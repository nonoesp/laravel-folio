<?php namespace Nonoesp\Folio;

use User;
use Item;
use Html;
use Route;
use Auth;
use Redirect;
use Config;
use Request;
use Markdown;
use Authenticate; // nonoesp/authenticate
use Recipient;
use Property;
use Input;
use Hashids;

/*----------------------------------------------------------------*/
/* FolioController
/*----------------------------------------------------------------*/

	// SubscriptionController (outside controller to allow cross-domain subscription)
	Route::post('subscriber/create', 'Nonoesp\Folio\Controllers\SubscriptionController@create');

	/*
	* Create a domain pattern if provided in config.php
	* Otherwise allow the current domain (i.e., any domain)
	*/
	$domainPattern = config('folio.domain-pattern');
	if(
		$domainPattern == null or
		$domainPattern == '' or
		!$domainPattern
	) {
		Route::pattern('foliodomain', Request::getHost());
	} else {
		Route::pattern('foliodomain', config('folio.domain-pattern'));
	}

	/*
	* A pattern to allow (only) items to be domain specific
	* and be rendered on another domain
	*/
	$domainPatternItems = config('folio.domain-pattern-items');
	if(
		$domainPatternItems == null or
		$domainPatternItems == '' or
		!$domainPatternItems
	) {
		Route::pattern('foliodomainitems', Request::getHost());
	} else {
		Route::pattern('foliodomainitems', config('folio.domain-pattern').'|'.config('folio.domain-pattern-items'));
	}

// DOMAIN-PATTERN + DOMAIN-PATTERN-ITEMS

Route::group([
	'domain' => '{foliodomainitems}',
	'middleware' => config("folio.middlewares")
], function () {

	$path = Folio::path();

	// Item
	if($path_type = Folio::isFolioURI()) { // Check this is an actual item route

		Route::get($path.'{slug}', 'Nonoesp\Folio\Controllers\FolioController@showItem')->
					where('slug', '[A-Za-z0-9.\-\/]+');
		Route::get('{slug}', 'Nonoesp\Folio\Controllers\FolioController@showItem')->
					where('slug', '[A-Za-z0-9.\-\/]+');
	}

	// Item redirections
	if ($folioRedirectionItemId = Folio::pathIsItemRedirection()) {
		
		$itemURL = '/'.Folio::permalinkPrefix().$folioRedirectionItemId;

		Route::get(Request::path(), function() use ($itemURL) {
			return Redirect::to($itemURL);
		});

	}

}); // close folio general domain pattern group


// DOMAIN-PATTERN + DOMAIN-PATTERN-ITEMS

Route::group([
	'domain' => '{foliodomain}',
	'middleware' => config("folio.middlewares")
], function () {

	$path = Folio::path();

	Route::get('/e/{hash}', function($domain, $hash) use ($path) {
		$decode = Hashids::decode($hash);
		if(count($decode)) {
			$item = Item::withTrashed()->find($decode[0]);
			if($item) {
				session(['temporary-token' => true]);
				return Redirect::to($item->path());
			}
		}
		return response()->view('errors.404', [], 404);
	});

	if(!Folio::isReservedURI()) {

		Route::get('@{handle}', 'Nonoesp\Folio\Controllers\FolioController@getUserProfile');

		Route::post('items', 'Nonoesp\Folio\Controllers\FolioController@getItemsWithIds');
		Route::get($path, array('as' => 'folio', 'uses' => 'Nonoesp\Folio\Controllers\FolioController@showHome'));
		Route::get($path.'tag/{tag}', 'Nonoesp\Folio\Controllers\FolioController@showItemTag');

		// Permalinks
		Route::get(Folio::permalinkPrefix().'{id}', 'Nonoesp\Folio\Controllers\FolioController@showItemWithId')->where('id', '[0-9]+');
		Route::get('disqus/'.'{id}', 'Nonoesp\Folio\Controllers\FolioController@showItemWithId')->where('id', '[0-9]+');

		// Feed
		Route::get(config('folio.feed.route'), ['as' => 'feed', 'uses' => 'Nonoesp\Folio\Controllers\FeedController@makeFeed']);

		// Debug: Hello, Folio!
		Route::get('debug/folio', 'Nonoesp\Folio\Controllers\FolioController@helloFolio');

	}

}); // close folio general domain pattern group

/*----------------------------------------------------------------*/
/* AdminController
/*----------------------------------------------------------------*/

// DOMAIN-PATTERN + DOMAIN-PATTERN-ITEMS

Route::group([
	'middleware' => config("folio.middlewares-admin")
], function() {
	
	$admin_path = Folio::adminPath();

	Route::get($admin_path, 'Nonoesp\Folio\Controllers\AdminController@getDashboard');

	// Items
	Route::get($admin_path.'items', 'Nonoesp\Folio\Controllers\AdminController@getItemList');
	Route::get($admin_path.'items/{tag}', 'Nonoesp\Folio\Controllers\AdminController@getItemList');
	Route::any($admin_path.'item/edit/{id}', ['as' => 'item.edit', 'uses' => 'Nonoesp\Folio\Controllers\AdminController@ItemEdit']);
	Route::any($admin_path.'item/versions/{id}', ['as' => 'item.version', 'uses' => 'Nonoesp\Folio\Controllers\AdminController@ItemVersions']);
	Route::get($admin_path.'item/add', 'Nonoesp\Folio\Controllers\AdminController@getItemCreate');
	Route::post($admin_path.'item/add', 'Nonoesp\Folio\Controllers\AdminController@postItemCreate');
	Route::get($admin_path.'item/delete/{id}', 'Nonoesp\Folio\Controllers\AdminController@getItemDelete');
	Route::get($admin_path.'item/restore/{id}', 'Nonoesp\Folio\Controllers\AdminController@getItemRestore');
	Route::get($admin_path.'item/destroy/{id}', 'Nonoesp\Folio\Controllers\AdminController@ItemDestroy');
	Route::get($admin_path.'item/force-delete/{id}', 'Nonoesp\Folio\Controllers\AdminController@ItemForceDelete');

	// Item Update with Ajax (new)
	Route::post('item/update/{id}', 'Nonoesp\Folio\Controllers\AdminController@postItemUpdateAjax');

	Route::get($admin_path.'subscribers', 'Nonoesp\Folio\Controllers\AdminController@getSubscribers');
	Route::get($admin_path.'visits', 'Nonoesp\Folio\Controllers\AdminController@getVisits');
	Route::get($admin_path.'redirections', 'Nonoesp\Folio\Controllers\AdminController@getRedirections');

	Route::get($admin_path, function() use ($admin_path) {
		return redirect()->to($admin_path.'items');
	});

	// Properties (API)
	Route::post('/api/property/update', 'Nonoesp\Folio\Controllers\AdminController@postPropertyUpdate');
	Route::post('/api/property/delete', 'Nonoesp\Folio\Controllers\AdminController@postPropertyDelete');
	Route::post('/api/property/create', 'Nonoesp\Folio\Controllers\AdminController@postPropertyCreate');
	Route::post('/api/property/swap', 'Nonoesp\Folio\Controllers\AdminController@postPropertySwap');
	Route::post('/api/property/sort', 'Nonoesp\Folio\Controllers\AdminController@postPropertySort');

	Route::post('/api/item/update', 'Nonoesp\Folio\Controllers\AdminController@postItemUpdate');
	Route::post('/api/item/delete', 'Nonoesp\Folio\Controllers\AdminController@postItemDelete');
	Route::post('/api/item/restore', 'Nonoesp\Folio\Controllers\AdminController@postItemRestore');

	// UploadController
	Route::any($admin_path.'upload', 'Nonoesp\Folio\Controllers\UploadController@getUploadForm');
	Route::get($admin_path.'upload/list', 'Nonoesp\Folio\Controllers\UploadController@getMediaList');
	Route::get($admin_path.'upload/delete/{name}', 'Nonoesp\Folio\Controllers\UploadController@postDeleteMedia');
	
	// SubscriptionController
	Route::post('subscriber/delete', 'Nonoesp\Folio\Controllers\SubscriptionController@delete');
	Route::post('subscriber/restore', 'Nonoesp\Folio\Controllers\SubscriptionController@restore');

	// Logs
	Route::get('admin/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

}); // close folio admin