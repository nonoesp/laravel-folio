<?php

$path = Config::get('writing::path');

/*----------------------------------------------------------------*/
/* BlogController
/*----------------------------------------------------------------*/

// TODO: Convert into auto-routing (or however it's called)

Route::get($path, array('as' => 'blog', 'uses' => 'Nonoesp\Writing\MyController@showHome'));
Route::get($path.'/tag/{tag}', 'Nonoesp\Writing\MyController@showArticleTag');
Route::get($path.'/{id}', 'Nonoesp\Writing\MyController@showArticleWithId')->where('id', '[0-9]+');
Route::get($path.'/{slug}', 'Nonoesp\Writing\MyController@showArticle');

	Route::post('/articles', 'Nonoesp\Writing\MyController@getArticlesWithIds');
	Route::get('/feed', array('as' => 'feed', 'uses' => 'Nonoesp\Writing\MyController@getFeed'));