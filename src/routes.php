<?php namespace Nonoesp\Writing;

use User; // Must be defined in your aliases
use Article; // Must be defined in your aliases
use HTML;
use Route;
use Auth;
use Redirect;
use Config;
use Request;
use Markdown;
use Authenticate; // nonoesp/authenticate
use Recipient;

// Experimental

foreach(Config::get("writing.layers") as $layer) {
	Route::get($layer['path'], function() use ($layer) {
		$items = Article::withAnyTag($layer['tags'])->orderBy('published_at', 'DESC')->get();
		return view($layer['view'])->withItems($items);
	});
}

/*----------------------------------------------------------------*/
/* WritingController
/*----------------------------------------------------------------*/

Route::group(['middleware' => Config::get("writing.middlewares")], function () {

if(Writing::isAvailableURI()) {

	$path = Writing::path();

	Route::get('/@{user_twitter}', function($user_twitter) {
		$user = User::where('twitter', '=', $user_twitter)->first();
		return view('writing::profile')->withUser($user);
	});
	Route::post('articles', 'Nonoesp\Writing\Controllers\WritingController@getArticlesWithIds');
	Route::get($path, array('as' => 'blog', 'uses' => 'Nonoesp\Writing\Controllers\WritingController@showHome'));
	Route::get($path.'tag/{tag}', 'Nonoesp\Writing\Controllers\WritingController@showArticleTag');
	Route::get($path.'{id}', 'Nonoesp\Writing\Controllers\WritingController@showArticleWithId')->where('id', '[0-9]+');	

	if(Writing::isWritingURI()) { // Check this is an actual article route
		Route::get($path.'{slug}', 'Nonoesp\Writing\Controllers\WritingController@showArticle');		
	}

	// Feed
	Route::get(Config::get('writing.feed.route'), array('as' => 'feed', 'uses' => 'Nonoesp\Writing\Controllers\WritingController@getFeed'));	
}

/*----------------------------------------------------------------*/
/* AdminController
/*----------------------------------------------------------------*/

Route::group(['middleware' => Config::get("writing.middlewares-admin")], function() { // todo: get middleware back to 'login'

  $admin_path = Writing::adminPath();

  Route::get($admin_path, 'Nonoesp\Writing\Controllers\AdminController@getDashboard');

  // Articles
  Route::get($admin_path.'articles', 'Nonoesp\Writing\Controllers\AdminController@getArticleList');
  Route::any($admin_path.'article/edit/{id}', array('as' => 'article.edit', 'uses' => 'Nonoesp\Writing\Controllers\AdminController@ArticleEdit'));
  Route::get($admin_path.'article/add', 'Nonoesp\Writing\Controllers\AdminController@getArticleCreate');
  Route::post($admin_path.'article/add', 'Nonoesp\Writing\Controllers\AdminController@postArticleCreate');
  Route::get($admin_path.'article/delete/{id}', 'Nonoesp\Writing\Controllers\AdminController@getArticleDelete');
  Route::get($admin_path.'article/restore/{id}', 'Nonoesp\Writing\Controllers\AdminController@getArticleRestore');

  // Visits
  Route::get($admin_path.'visits', 'Nonoesp\Writing\Controllers\AdminController@getVisits');

  Route::get($admin_path, function() use ($admin_path) {
  	return redirect()->to($admin_path.'articles');
  });
});

});
