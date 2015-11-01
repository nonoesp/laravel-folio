<?php namespace Nonoesp\Writing;
use Route;
use Auth;
use Redirect;
use Config;
use Arma\User;;

$path = Config::get('writing.path');

/*----------------------------------------------------------------*/
/* BlogController
/*----------------------------------------------------------------*/

Route::get($path, array('as' => 'blog', 'uses' => 'Nonoesp\Writing\WritingController@showHome'));
Route::get($path.'/tag/{tag}', 'Nonoesp\Writing\WritingController@showArticleTag');
Route::get($path.'/{id}', 'Nonoesp\Writing\WritingController@showArticleWithId')->where('id', '[0-9]+');
Route::get($path.'/{slug}', 'Nonoesp\Writing\WritingController@showArticle');
Route::post('/articles', 'Nonoesp\Writing\WritingController@getArticlesWithIds');
Route::get('/feed', array('as' => 'feed', 'uses' => 'Nonoesp\Writing\WritingController@getFeed'));

Route::get('/@{user_twitter}', function($user_twitter) {
	$user = User::where('twitter', '=', $user_twitter)->first();
	return view('writing::profile')->withUser($user);
});