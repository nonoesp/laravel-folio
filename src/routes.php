<?php namespace Nonoesp\Space;

use User; // Must be defined in your aliases
use Item; // Must be defined in your aliases
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
/* SpaceController
/*----------------------------------------------------------------*/

Route::group(['middleware' => Config::get("space.middlewares")], function () {

	$path = Space::path();

	Route::get('/e/{hash}', function($hash) use ($path) {
		return Redirect::to($path.Hashids::decode($hash)[0]);
	});

if(Space::isAvailableURI()) {

	Route::get('/@{user_twitter}', function($user_twitter) {
		$user = User::where('twitter', '=', $user_twitter)->first();
		return view('space::profile')->withUser($user);
	});
	Route::post('items', 'Nonoesp\Space\Controllers\SpaceController@getItemsWithIds');
	Route::get($path, array('as' => 'space', 'uses' => 'Nonoesp\Space\Controllers\SpaceController@showHome'));
	Route::get($path.'tag/{tag}', 'Nonoesp\Space\Controllers\SpaceController@showItemTag');
	Route::get($path.'{id}', 'Nonoesp\Space\Controllers\SpaceController@showItemWithId')->where('id', '[0-9]+');

	if($path_type = Space::isSpaceURI()) { // Check this is an actual item route
		Route::get($path.'{slug}', 'Nonoesp\Space\Controllers\SpaceController@showItem')->
					 where('slug', '[A-Za-z0-9\-\/]+');
		Route::get('{slug}', 'Nonoesp\Space\Controllers\SpaceController@showItem')->
					 where('slug', '[A-Za-z0-9\-\/]+');
	}

	// Feed
	Route::get(Config::get('space.feed.route'), array('as' => 'feed', 'uses' => 'Nonoesp\Space\Controllers\SpaceController@getFeed'));

	// Debug: Hello, Space!
	Route::get('debug/space', 'Nonoesp\Space\Controllers\SpaceController@helloSpace');

	// SubscriptionController
	Route::post('subscriber/create', 'Nonoesp\Space\Controllers\SubscriptionController@postSubscriber');
}

/*----------------------------------------------------------------*/
/* AdminController
/*----------------------------------------------------------------*/

Route::group(['middleware' => Config::get("space.middlewares-admin")], function() { // todo: get middleware back to 'login'

  $admin_path = Space::adminPath();

  Route::get($admin_path, 'Nonoesp\Space\Controllers\AdminController@getDashboard');

  // Items
  Route::get($admin_path.'items', 'Nonoesp\Space\Controllers\AdminController@getItemList');
	Route::get($admin_path.'items/{tag}', 'Nonoesp\Space\Controllers\AdminController@getItemList');
  Route::any($admin_path.'item/edit/{id}', array('as' => 'item.edit', 'uses' => 'Nonoesp\Space\Controllers\AdminController@ItemEdit'));
  Route::get($admin_path.'item/add', 'Nonoesp\Space\Controllers\AdminController@getItemCreate');
  Route::post($admin_path.'item/add', 'Nonoesp\Space\Controllers\AdminController@postItemCreate');
  Route::get($admin_path.'item/delete/{id}', 'Nonoesp\Space\Controllers\AdminController@getItemDelete');
  Route::get($admin_path.'item/restore/{id}', 'Nonoesp\Space\Controllers\AdminController@getItemRestore');

	Route::get($admin_path.'subscribers', 'Nonoesp\Space\Controllers\AdminController@getSubscribers');

  // Visits
  Route::get($admin_path.'visits', 'Nonoesp\Space\Controllers\AdminController@getVisits');

  Route::get($admin_path, function() use ($admin_path) {
  	return redirect()->to($admin_path.'items');
  });

	// Properties (API)
	Route::post('/api/property/update', 'Nonoesp\Space\Controllers\AdminController@postPropertyUpdate');
	Route::post('/api/property/delete', 'Nonoesp\Space\Controllers\AdminController@postPropertyDelete');
	Route::post('/api/property/create', 'Nonoesp\Space\Controllers\AdminController@postPropertyCreate');

	Route::post('/api/item/update', 'Nonoesp\Space\Controllers\AdminController@postItemUpdate');
	Route::post('/api/item/delete', 'Nonoesp\Space\Controllers\AdminController@postItemDelete');
	Route::post('/api/item/restore', 'Nonoesp\Space\Controllers\AdminController@postItemRestore');

}); // close space admin

}); // close space general
