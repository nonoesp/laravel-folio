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
use Recipient;
use Property;
use Input;
use Hashids;
use \Illuminate\Support\Str;

// ███████╗     ██████╗     ██╗         ██╗     ██████╗ 
// ██╔════╝    ██╔═══██╗    ██║         ██║    ██╔═══██╗
// █████╗      ██║   ██║    ██║         ██║    ██║   ██║
// ██╔══╝      ██║   ██║    ██║         ██║    ██║   ██║
// ██║         ╚██████╔╝    ███████╗    ██║    ╚██████╔╝
// ╚═╝          ╚═════╝     ╚══════╝    ╚═╝     ╚═════╝ 

/*----------------------------------------------------------------*/
/* 404 with session store
/*----------------------------------------------------------------*/

Route::fallback(function(\Illuminate\Http\Request $request) {

	// Log 404 errors to 404.log if channel exists (otherwise fallback to default)
	$channel = \Illuminate\Support\Arr::has(config('logging.channels'), '404') ? '404' : 'single';
	\Log::channel($channel)->info('404 → [ '.\Thinker::clientIp().' ] [ '.url()->current().' ] [ previous → '.url()->previous().' ]');
	return response()->view('errors.404', [], 404);

})->middleware('web');

/*----------------------------------------------------------------*/
/* LoginController
/*----------------------------------------------------------------*/

Route::group(['middleware' => 'web'], function () {

	Route::view('login', 'folio::login.login')->middleware(\Nonoesp\Folio\Middleware\RedirectIfAuthenticated::class);
	Route::post('login', 'Nonoesp\Folio\Controllers\LoginController@login')->name('login');
	Route::post('logout', 'Nonoesp\Folio\Controllers\LoginController@logout')->name('logout');

});

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
	Route::get($admin_path.'items', 'Nonoesp\Folio\Controllers\AdminController@getItemList')->name('admin.items');
	Route::get($admin_path.'items/{tag}', 'Nonoesp\Folio\Controllers\AdminController@getItemList');
	Route::any($admin_path.'item/edit/{id}', ['as' => 'item.edit', 'uses' => 'Nonoesp\Folio\Controllers\AdminController@ItemEdit']);
	Route::any($admin_path.'item/versions/{id}', ['as' => 'item.version', 'uses' => 'Nonoesp\Folio\Controllers\AdminController@ItemVersions']);
	Route::get($admin_path.'item/add', ['as' => 'item.create', 'uses' => 'Nonoesp\Folio\Controllers\AdminController@getItemCreate']);
	Route::post($admin_path.'item/add', 'Nonoesp\Folio\Controllers\AdminController@postItemCreate');
	Route::get($admin_path.'item/delete/{id}', 'Nonoesp\Folio\Controllers\AdminController@getItemDelete');
	Route::get($admin_path.'item/restore/{id}', 'Nonoesp\Folio\Controllers\AdminController@getItemRestore');
	Route::get($admin_path.'item/destroy/{id}', 'Nonoesp\Folio\Controllers\AdminController@ItemDestroy');
	Route::get($admin_path.'item/force-delete/{id}', 'Nonoesp\Folio\Controllers\AdminController@ItemForceDelete');
	Route::get($admin_path.'item/clone/{id}', 'Nonoesp\Folio\Controllers\AdminController@ItemClone');

	// Item Update with Ajax
	Route::post('item/update/{id}', 'Nonoesp\Folio\Controllers\AdminController@postItemUpdateAjax');

	Route::get($admin_path.'subscribers', 'Nonoesp\Folio\Controllers\AdminController@getSubscribers');
	Route::get($admin_path.'visits', 'Nonoesp\Folio\Controllers\AdminController@getVisits');
	Route::get($admin_path.'redirections', 'Nonoesp\Folio\Controllers\AdminController@getRedirections');

	Route::redirect($admin_path, $admin_path.'items');

	// Properties (API)
	Route::post('/api/property/update', 'Nonoesp\Folio\Controllers\AdminController@postPropertyUpdate');
	Route::post('/api/property/delete', 'Nonoesp\Folio\Controllers\AdminController@postPropertyDelete');
	Route::post('/api/property/create', 'Nonoesp\Folio\Controllers\AdminController@postPropertyCreate');
	Route::post('/api/property/swap', 'Nonoesp\Folio\Controllers\AdminController@postPropertySwap');
	Route::post('/api/property/sort', 'Nonoesp\Folio\Controllers\AdminController@postPropertySort');

	Route::post('/api/item/update', 'Nonoesp\Folio\Controllers\AdminController@postItemUpdate');
	Route::post('/api/item/delete', 'Nonoesp\Folio\Controllers\AdminController@postItemDelete');
	Route::post('/api/item/restore', 'Nonoesp\Folio\Controllers\AdminController@postItemRestore');
	Route::post('/api/item/revert', 'Nonoesp\Folio\Controllers\AdminController@postItemRevertToVersion');

	// UploadController
	Route::any($admin_path.'upload', ['as' => 'uploader.form', 'uses' => 'Nonoesp\Folio\Controllers\UploadController@getUploadForm']);
	Route::get($admin_path.'upload/list', ['as' => 'uploader.list', 'uses' => 'Nonoesp\Folio\Controllers\UploadController@getMediaList']);
	Route::get($admin_path.'upload/delete/{name}', 'Nonoesp\Folio\Controllers\UploadController@postDeleteMedia');
	
	// SubscriptionController
	Route::post('subscriber/delete', 'Nonoesp\Folio\Controllers\SubscriptionController@delete');
	Route::post('subscriber/restore', 'Nonoesp\Folio\Controllers\SubscriptionController@restore');

	// Logs
	Route::get('admin/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

	// Debug
	Route::get('debug/templates', 'Nonoesp\Folio\Controllers\DebugController@templateStats');

}); // close folio admin

/*----------------------------------------------------------------*/
/* Redirects
/* Defined after admin to have access to named routes.
/*----------------------------------------------------------------*/

Route::group(['middleware' => ['web']], function () {

	if (config('redirects')) {

		// Redirects from config/redirects.php
		$path = \Request::path();
		$redirects = config('redirects');

		// Look for current host specific redirects
		foreach ($redirects as $key => $to) {
			if (Str::is('{*}', $key)) {
				$hosts = explode("|", preg_replace('/{(.*?)}/is', '$1', $key));
				if (in_array(\Request::getHost(), $hosts) && is_array($to)) {
					$redirects = array_merge($redirects, $redirects[$key]);
				}
			}
		}

		// Catch global (and current host) redirects
		if ($redirects && array_key_exists($path, $redirects)) {
			$to = $redirects[$path];
			if (Str::is('{*}', $to)) {
				$to = route(str_replace(['{','}'],'',$to));
			}
			Route::redirect($path, $to);
		}

	}

});

/*----------------------------------------------------------------*/
/* FolioController
/*----------------------------------------------------------------*/

// ALL DOMAINS

use Spatie\Honeypot\ProtectAgainstSpam;
// SubscriptionController (outside controller to allow cross-domain subscription)
Route::post('subscriber/create', 'Nonoesp\Folio\Controllers\SubscriptionController@create')
->middleware(ProtectAgainstSpam::class);

// Remote API · Requires to be excluded in VerifyCrsfToken middleware
Route::post('api/item/set-text', 'Nonoesp\Folio\Controllers\AdminController@postItemSetText');

/*
* Create a domain pattern if provided in config.php
* Otherwise allow the current domain (i.e., any domain)
*/
$domainPattern = config('folio.domain-pattern');
if(
	$domainPattern == null ||
	$domainPattern == '' ||
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
	$domainPatternItems == null ||
	$domainPatternItems == '' ||
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

		Route::get($path.'{slug}', 'Nonoesp\Folio\Controllers\FolioController@showItemBySlug')->
			   where('slug', '[A-Za-z0-9.\-\/]+');
		Route::get('{slug}', 'Nonoesp\Folio\Controllers\FolioController@showItemBySlug')->
			   where('slug', '[A-Za-z0-9.\-\/]+');
	}

	// Item redirections
	if ($folioRedirectionItemId = Folio::pathIsItemRedirection()) {
		
		$itemURL = '/'.Folio::permalinkPrefix().$folioRedirectionItemId;

		Route::get(Request::path(), function() use ($itemURL) {
			return redirect($itemURL);
		});

	}

	// Item preview (beta)
	Route::get('/preview/{id}/{template}/{stripTags?}/{locale?}/{hash?}', 'Nonoesp\Folio\Controllers\FolioController@getItemPreview');

}); // close folio general domain pattern group


// FOLIO-DOMAIN-PATTERN

Route::group([
	'domain' => '{foliodomain}',
	'middleware' => config("folio.middlewares")
], function () {

	$path = Folio::path();

	Route::get('/e/{hash}', function($domain, $hash) use ($path) {
		$decode = Folio::hashids()->decode($hash);
		if(count($decode)) {
			$item = Item::withTrashed()->find($decode[0]);
			if ($item) {
				session(['temporary-token' => true]);
				return redirect($item->path());
			}
		}
		return response()->view('errors.404', [], 404);
	});

	if(!Folio::isReservedURI()) {

		Route::get('@{handle}', 'Nonoesp\Folio\Controllers\FolioController@getUserProfile');

		Route::post('items', 'Nonoesp\Folio\Controllers\FolioController@getItemsWithIds');

		$home_item = config('folio.home-item');

		if ($home_item) {
			// Specific home item
			Route::get($path, function(\Illuminate\Http\Request $request, string $domain) use ($home_item) {
				if (is_numeric($home_item)) {
					return \Nonoesp\Folio\Controllers\FolioController::showItemById($domain, $request, $home_item);
				}
				return \Nonoesp\Folio\Controllers\FolioController::showItemBySlug($domain, $request, $home_item);
			})->name('folio');
		} else {
			// Default home collection
			Route::get($path, ['as' => 'folio', 'uses' => 'Nonoesp\Folio\Controllers\FolioController@showHome']);
		}
		Route::get($path.'tag/{tag}', 'Nonoesp\Folio\Controllers\FolioController@showItemTag');

		// Permalinks
		Route::get(Folio::permalinkPrefix().'{id}', 'Nonoesp\Folio\Controllers\FolioController@redirectToItemWithId')->where('id', '[0-9]+');
		Route::get('disqus/'.'{id}', 'Nonoesp\Folio\Controllers\FolioController@redirectToItemWithId')->where('id', '[0-9]+');

		// Feed
		Route::get(config('folio.feed.route'), ['as' => 'feed', 'uses' => 'Nonoesp\Folio\Controllers\FeedController@makeFeed']);

		// Debug
		Route::get('debug/folio', 'Nonoesp\Folio\Controllers\DebugController@helloFolio');
		Route::get('debug/load-time', 'Nonoesp\Folio\Controllers\DebugController@loadTime');
		Route::get('debug/time', 'Nonoesp\Folio\Controllers\DebugController@time');

	}

}); // close folio general domain pattern group
