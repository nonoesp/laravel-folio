<?php namespace Nonoesp\Writing;

use Nonoesp\Writing\Article;

class MyController extends \BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showHome() {

		// Get Articles + Articles ids
		$show = 5;
		$left = Article::published()->count();
  		$articles = Article::published()->orderBy('published_at', 'DESC')->skip(0)->take($show)->get();
		$ids = Article::published()->select('id','published_at')->orderBy('published_at', 'DESC')->skip($show)->take($left)->get();
		$ids_array = array();

		foreach($ids as $id) {
			array_push($ids_array, $id->id);
		}

		// Get Expected Articles
		$show_expected = 3;
		$articles_expected = Article::expected()->orderBy('published_at', 'ASC')->take($show_expected)->get()->reverse();
		return \View::make('writing::base')->with(array('articles' => $articles,
													  'ids' => $ids_array,
													  'articles_expected' => $articles_expected));
	}

	// Simplify making showHome a generic function, then call it directly from route or from Controller function
	public function showArticleTag($tag) {

		$show = 5;
		$left = Article::withAnyTag($tag)->published()->count() - $show;

  		$articles = Article::withAnyTag($tag)->published()->orderBy('published_at', 'DESC')->skip(0)->take($show)->get();
		$ids_array = array();  		
  		if ($left > 0) {
			$ids = Article::withAnyTag($tag)->published()->select('id','published_at')->orderBy('published_at', 'DESC')->skip($show)->take($left)->get();

			foreach($ids as $id) {
				array_push($ids_array, $id->id);
			}
		}

		return \View::make('writing::base')->
		             with(array(
		             	  'articles' => $articles,
		             	  'ids' => $ids_array, 
		             	  'tag' => $tag));
	}	

	public function showArticle($slug) {

		$article = Article::whereSlug($slug)->first();
		$article->visits++;
		$article->save();
		return \View::make('writing::base')->with('article', $article);
	}

	public function showArticleWithId($id) {
		$article = Article::withTrashed()->find($id);
		return \Redirect::to(\Config::get('writing::path').'/'.$article->slug);
	}

	public function getArticlesWithIds() {

		// Set Article Type
		\Input::get('article_type') ? $article_type = \Input::get('article_type') : $article_type = 'DEFAULT_ARTICLE_TYPE';

		// Echo Articles
		foreach(\Input::get('ids') as $id) {
			echo \View::make('partial.blog.c-article')->
			           with(array('article' => Article::find($id),
			           	  		  'article_type' => $article_type,
			           	  		  'isTitleLinked' => true));
		}
	}

	public function getFeed() {
		
		// create new feed
	    $feed = \Feed::make();

	    // cache the feed for 60 minutes (second parameter is optional)
	    $feed->setCache(60, 'laravelFeedKey');

	    // check if there is cached feed and build new only if is not
	    if (!$feed->isCached())
	    {
	    	$default_author = 'AR-MA';

	       // creating rss feed with our most recent articles
		   $show = 30;
	  	   $articles = Article::published()->orderBy('published_at', 'DESC')->take($show)->get();

	       // set your feed's title, description, link, pubdate and language
	       $feed->title = \Config::get('settings.title');
	       $feed->description = \Config::get('settings.description');
	       $feed->logo = 'http://ar-ma.net/img/image_src.jpg';
	       $feed->link = \URL::to('/'.\Config::get('writing::path'));
	       $feed->setDateFormat('datetime'); // 'datetime', 'timestamp' or 'carbon'
	       $feed->pubdate = $articles[0]->created_at;
	       $feed->lang = 'en';
	       $feed->setShortening(false); // true or false
	       $feed->setTextLimit(159); // maximum length of description text

	       foreach ($articles as $article)
	       {
	           // set item's title, author, url, pubdate, description and content
	       	   $imageURL = '';
	       	   $image = '';
	       	   if ($article->image) {
	       	     $image = '<p><img src="'.$article->image.'" alt="'.$article->title.'"></p>';
	       	   }

	       	   if ($article->video) {
	       	   	 $image = '<p><a href="http://ar-ma.net/'.\Config::get('writing::path').'/'.$article->slug.'"><img src="'.\Thinker::getVideoThumb($article->video).'" alt="'.$article->title.'"></a></p>';
	       	   }

	       	   if ($imageURL != '') {
	       	   	 $image = '<p><img src="'.$imageURL.'" alt="'.$article->title.'"></p>';
	       	   }

	           $feed->add($article->title, $default_author, \URL::to(\Config::get('writing::path').'/'.$article->slug), $article->published_at, '', str_replace('<img', '<img width="100%"', $image.\Markdown::string($article->text)));
	       }

	    }

	    // first param is the feed format
	    // optional: second param is cache duration (value of 0 turns off caching)
	    // optional: you can set custom cache key with 3rd param as string
	    //return $feed->render('atom');
	    return \Response::make($feed->render('rss', -1), 200, array('Content-Type' => 'text/xml', 'Cache-Control' => 'no-cache'));

	    // to return your feed as a string set second param to -1
	    // $xml = $feed->render('atom', -1);
	}
}
