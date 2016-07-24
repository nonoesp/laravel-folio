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

/*----------------------------------------------------------------*/
/* WritingController
/*----------------------------------------------------------------*/

Route::group(['middleware' => Config::get("writing.middlewares")], function () {

$path = Writing::path();
//TODO: $path_admin = Writing::pathAdmin();

if(Writing::isAvailableURI()) {

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

Route::group(array('middleware' => []), function(){ // todo: get middleware back to 'login'

  Route::get('/admin', 'Nonoesp\Writing\Controllers\AdminController@getDashboard');

  // Articles
  Route::get('/admin/articles', 'Nonoesp\Writing\Controllers\AdminController@getArticleList');
  Route::any('/admin/article/edit/{id}', array('as' => 'article.edit', 'uses' => 'Nonoesp\Writing\Controllers\AdminController@ArticleEdit'));
  Route::get('/admin/article/add', 'Nonoesp\Writing\Controllers\AdminController@getArticleCreate');
  Route::post('/admin/article/add', 'Nonoesp\Writing\Controllers\AdminController@postArticleCreate');
  Route::get('/admin/article/delete/{id}', 'Nonoesp\Writing\Controllers\AdminController@getArticleDelete');
  Route::get('/admin/article/restore/{id}', 'Nonoesp\Writing\Controllers\AdminController@getArticleRestore');

  // Visits
  Route::get('/admin/visits', 'Nonoesp\Writing\Controllers\AdminController@getVisits');

});

//TODO: Archive

Route::get($path.'archive', function() {
	return 'Blog archive.';
});

});
