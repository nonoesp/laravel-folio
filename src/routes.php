<?php namespace Nonoesp\Writing;

use User; // Must be defined in your aliases
use Item; // Must be defined in your aliases
use HTML;
use Route;
use Auth;
use Redirect;
use Config;
use Request;
use Markdown;
use Authenticate; // nonoesp/authenticate
use Recipient;
use Hashids;

/*----------------------------------------------------------------*/
/* WritingController
/*----------------------------------------------------------------*/

Route::group(['middleware' => Config::get("writing.middlewares")], function () {

	$path = Writing::path();

	Route::get('/e/{hash}', function($hash) use ($path) {
		return Redirect::to($path.Hashids::decode($hash)[0]);
	});

if(Writing::isAvailableURI()) {

	Route::get('/@{user_twitter}', function($user_twitter) {
		$user = User::where('twitter', '=', $user_twitter)->first();
		return view('writing::profile')->withUser($user);
	});
	Route::post('items', 'Nonoesp\Writing\Controllers\WritingController@getItemsWithIds');
	Route::get($path, array('as' => 'blog', 'uses' => 'Nonoesp\Writing\Controllers\WritingController@showHome'));
	Route::get($path.'tag/{tag}', 'Nonoesp\Writing\Controllers\WritingController@showItemTag');
	Route::get($path.'{id}', 'Nonoesp\Writing\Controllers\WritingController@showItemWithId')->where('id', '[0-9]+');

	if(Writing::isWritingURI()) { // Check this is an actual item route
		Route::get($path.'{slug}', 'Nonoesp\Writing\Controllers\WritingController@showItem');
	}

	// Feed
	Route::get(Config::get('writing.feed.route'), array('as' => 'feed', 'uses' => 'Nonoesp\Writing\Controllers\WritingController@getFeed'));

	// Experimental - layer routes from config file

	// foreach(Config::get("writing.layers") as $layer) {
	//
	// 	Route::get($layer['path'], function() use ($layer) {
	// 		$items = Item::withAnyTag($layer['tags'])->orderBy('published_at', 'DESC')->get();
	// 		return view($layer['view'])->with(
	// 			[
	// 			'items' => $items,
	// 			'layer' => $layer
	// 			]);
	// 	});
	// }
}

/*----------------------------------------------------------------*/
/* AdminController
/*----------------------------------------------------------------*/

Route::group(['middleware' => Config::get("writing.middlewares-admin")], function() { // todo: get middleware back to 'login'

  $admin_path = Writing::adminPath();

  Route::get($admin_path, 'Nonoesp\Writing\Controllers\AdminController@getDashboard');

  // Items
  Route::get($admin_path.'items', 'Nonoesp\Writing\Controllers\AdminController@getItemList');
	Route::get($admin_path.'items/{tag}', 'Nonoesp\Writing\Controllers\AdminController@getItemList');
  Route::any($admin_path.'item/edit/{id}', array('as' => 'item.edit', 'uses' => 'Nonoesp\Writing\Controllers\AdminController@ItemEdit'));
  Route::get($admin_path.'item/add', 'Nonoesp\Writing\Controllers\AdminController@getItemCreate');
  Route::post($admin_path.'item/add', 'Nonoesp\Writing\Controllers\AdminController@postItemCreate');
  Route::get($admin_path.'item/delete/{id}', 'Nonoesp\Writing\Controllers\AdminController@getItemDelete');
  Route::get($admin_path.'item/restore/{id}', 'Nonoesp\Writing\Controllers\AdminController@getItemRestore');

  // Visits
  Route::get($admin_path.'visits', 'Nonoesp\Writing\Controllers\AdminController@getVisits');

  Route::get($admin_path, function() use ($admin_path) {
  	return redirect()->to($admin_path.'items');
  });
});

});
