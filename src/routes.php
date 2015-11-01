<?php namespace Nonoesp\Writing;

use User; // Must be defined in your aliases
use Route;
use Auth;
use Redirect;
use Config;
use Request;

$path = Writing::path();

/*----------------------------------------------------------------*/
/* WritingController
/*----------------------------------------------------------------*/

if(Writing::isAvailableURI()) {

	Route::get('/@{user_twitter}', function($user_twitter) {
		$user = User::where('twitter', '=', $user_twitter)->first();
		return view('writing::profile')->withUser($user);
	});
	Route::get('/feed', array('as' => 'feed', 'uses' => 'Nonoesp\Writing\Controllers\WritingController@getFeed'));
	Route::any('/articles', 'Nonoesp\Writing\Controllers\WritingController@getArticlesWithIds');
	Route::get($path, array('as' => 'blog', 'uses' => 'Nonoesp\Writing\Controllers\WritingController@showHome'));
	Route::get($path.'tag/{tag}', 'Nonoesp\Writing\Controllers\WritingController@showArticleTag');
	Route::get($path.'{id}', 'Nonoesp\Writing\Controllers\WritingController@showArticleWithId')->where('id', '[0-9]+');	

	if(Writing::isWritingURI()) { // Check this is an actual article route
		Route::get($path.'{slug}', 'Nonoesp\Writing\Controllers\WritingController@showArticle');		
	}
}