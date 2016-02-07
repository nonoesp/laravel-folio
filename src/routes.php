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
	Route::get('/feed', array('as' => 'feed', 'uses' => 'Nonoesp\Writing\Controllers\WritingController@getFeed'));
	Route::post('articles', 'Nonoesp\Writing\Controllers\WritingController@getArticlesWithIds');
	Route::get($path, array('as' => 'blog', 'uses' => 'Nonoesp\Writing\Controllers\WritingController@showHome'));
	Route::get($path.'tag/{tag}', 'Nonoesp\Writing\Controllers\WritingController@showArticleTag');
	Route::get($path.'{id}', 'Nonoesp\Writing\Controllers\WritingController@showArticleWithId')->where('id', '[0-9]+');	

	if(Writing::isWritingURI()) { // Check this is an actual article route
		Route::get($path.'{slug}', 'Nonoesp\Writing\Controllers\WritingController@showArticle');		
	}
}

/*----------------------------------------------------------------*/
/* AdminController
/*----------------------------------------------------------------*/

Route::group(array('middleware' => 'login'), function(){

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

/*----------------------------------------------------------------*/


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


// ------------------------------------------------------------


Route::get('article/{id}', function($id) {
  
    echo "<style>img {max-width:100%;}</style>"
        .'<div style="max-width:600px;margin-left:auto;margin-right:auto;">';

  $article = Article::find($id);

  echo "<title>".$article->title."</title>";

  if($article->isPublic()) {
    // SHOW ARTICLE
    echo "Public article. Show.";
        echo '<br><br>';
        echo $article->title;
        echo Markdown::string($article->text);
  } else {

    echo "<p>Private article.</p>";
    echo "<h2>".$article->title."</h2>";

    if($twitter_handle = Authenticate::isUserLoggedInTwitter())
    {
      echo "User is logged with $twitter_handle, ".HTML::link('/logout', "logout").".<br><br>";

      // Check if logged user can see content
      if($article->visibleFor($twitter_handle))
      {
        // Logged twitter can see content
        echo "$twitter_handle can view this content. Display.";
        echo '<br><br>';
        echo $article->title;
        echo Markdown::string($article->text);
      } else {

        // Logged twitter can't see content
        echo "$twitter_handle can't view this content.";
      }

    } else {

      // Private content, please log in
      echo "User is not logged in twitter. Please, ".HTML::link('/twitter/login', "log in").".";
    }

    echo "</div>";
  }
});


// Testing what a user would see depending on how he's logged.

// SAMPLE FOR HOW HOME PAGE AND TAGS SHOULD WORK

Route::get('/articles/archive', function() {

	if($twitter_handle = Authenticate::isUserLoggedInTwitter())
	{
		echo "Logged as @$twitter_handle, ".HTML::link('/logout', 'logout').".<br><br>";
	} else {
		echo "Guest, not logged in, ".HTML::link('/twitter/login', 'login').".<br><br>";
	}

	$articles = Article::orderBy('id', 'DESC')
					   ->public()
					   ->published()
					   ->visibleFor($twitter_handle)
					   ->take(15)
					   ->get();

	//$articles = Article::test(250)->get();

	foreach($articles as $article)
	{
		print_r( $article->recipientsArray() );

		echo "<b>".$article->id.") ".$article->title."</b>";

		echo " -- [ ";
		$i = 0;
		foreach($article->recipients()->get() as $recipient) {
			if($i > 0) echo ", ";
			echo $recipient->twitter;
			$i++;
		}

		echo " ]<br>";		
	}

});


