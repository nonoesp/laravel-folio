<?php namespace Nonoesp\Writing;

use User; // Must be defined in your aliases
use Article; // Must be defined in your aliases
use HTML;
use Route;
use Auth;
use Redirect;
use Config;
use Request;


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
	Route::get('/feed', array('as' => 'feed', 'uses' => 'Nonoesp\Writing\Controllers\WritingController@getFeed'));
	Route::post('articles', 'Nonoesp\Writing\Controllers\WritingController@getArticlesWithIds');
	Route::get($path, array('as' => 'blog', 'uses' => 'Nonoesp\Writing\Controllers\WritingController@showHome'));
	Route::get($path.'tag/{tag}', 'Nonoesp\Writing\Controllers\WritingController@showArticleTag');
	Route::get($path.'{id}', 'Nonoesp\Writing\Controllers\WritingController@showArticleWithId')->where('id', '[0-9]+');	

	if(Writing::isWritingURI()) { // Check this is an actual article route
		Route::get($path.'{slug}', 'Nonoesp\Writing\Controllers\WritingController@showArticle');		
	}
}

//TODO: archive

Route::get($path.'archive', function() {
	return 'Blog archive.';
});

//TODO: WritingAdminController.php

	$temp_menu = "<a href='/admin/writing/'>Main</a>"
				 .' '
				 ."<a href='/admin/writing/add'>Add</a>";

	Route::get('/admin/writing', function() {

		//TODO: Add menu
	$temp_menu = "<a href='/admin/writing/'>Main</a>"
				 .' '
				 ."<a href='/admin/writing/add'>Add</a>";
		echo $temp_menu;
		echo '<br><br>';

		$articles = Article::orderBy('id', 'DESC')->take(10)->get();

		foreach($articles as $article) {
			echo HTML::link('/admin/writing/edit/'.$article->id, $article->title).'<br>';
		}
	});

	// make it any, and detect post/get
	Route::get('/admin/writing/edit/{id}', function($id) {

		//TODO: Add menu
	$temp_menu = "<a href='/admin/writing/'>Main</a>"
				 .' '
				 ."<a href='/admin/writing/add'>Add</a>";
		echo $temp_menu;
		echo '<br><br>';
		echo "Edit article $id";
		echo '<br><br>';

		$article = Article::find($id);

		echo $article->title.'<br>';
	});	

	Route::get('/admin/writing/add', function() {

		//TODO: Add menu
	$temp_menu = "<a href='/admin/writing/'>Main</a>"
				 .' '
				 ."<a href='/admin/writing/add'>Add</a>";
		echo $temp_menu;
		echo '<br><br>';


		return "Add new article";
	});	

});