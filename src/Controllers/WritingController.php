<?php

namespace Nonoesp\Writing\Controllers;

use Illuminate\Http\Request;
use Article, User; // Must be defined in your aliases
use Nonoesp\Writing\Writing;
use View;
use Config;
use Authenticate; // Must be installed (nonoesp/authenticate) and defined in your aliases
use App;

class WritingController extends Controller
{
	public function showHome() {

		// Get user's Twitter handle (or visitor)
		$twitter_handle = Authenticate::isUserLoggedInTwitter();

		// Get Articles + Articles ids
		$show = Config::get("writing.archive-show");

		//$left = Article::published()->count();
  		//$articles = Article::published()->orderBy('published_at', 'DESC')->skip(0)->take($show)->get();
		//$ids = Article::published()->select('id','published_at')->orderBy('published_at', 'DESC')->skip($show)->take($left)->get();
		
		$left = Article::published()
		               ->public()
		               ->visibleFor($twitter_handle)
		               ->count();

  		$articles = Article::published()
  		                   ->public()
  		                   ->visibleFor($twitter_handle)
  		                   ->orderBy('published_at', 'DESC')
  		                   ->skip(0)
  		                   ->take($show)
  		                   ->get();

		$ids = Article::published()
		              ->public()
		              ->visibleFor($twitter_handle)
		              ->select('id','published_at')
		              ->orderBy('published_at', 'DESC')
		              ->skip($show)
		              ->take($left)
		              ->get();	

		$ids_array = array();

		foreach($ids as $id)
		{
			array_push($ids_array, $id->id);
		}

		// Get Expected Articles
		$show_expected = 3;

		$articles_expected = Article::expected()
								    ->public()
									->orderBy('published_at', 'ASC')
									->take($show_expected)
									->get()
									->reverse();

		return view('writing::base')->with(array('articles' => $articles,
													  'ids' => $ids_array,
													  'articles_expected' => $articles_expected));
	}

	// Simplify making showHome a generic function, then call it directly from route or from Controller function
	public function showArticleTag($tag) {

		// Get user's Twitter handle (or visitor)
		$twitter_handle = Authenticate::isUserLoggedInTwitter();

		$show = Config::get("writing.archive-show");
		$left = Article::withAnyTag($tag)
		               ->published()
		               ->public()
		               ->visibleFor($twitter_handle)
		               ->count() - $show;

  		$articles = Article::withAnyTag($tag)  		
  		                   ->published()
  		                   ->public()
  		                   ->visibleFor($twitter_handle)  		                   
  		                   ->orderBy('published_at', 'DESC')
  		                   ->skip(0)
  		                   ->take($show)
  		                   ->get();

		$ids_array = array();

  		if ($left > 0)
  		{
			$ids = Article::withAnyTag($tag)
						  ->published()
  		                  ->public()
  		                  ->visibleFor($twitter_handle)						  
						  ->select('id','published_at')
						  ->orderBy('published_at', 'DESC')
						  ->skip($show)
						  ->take($left)
						  ->get();

			foreach($ids as $id)
			{
				array_push($ids_array, $id->id);
			}
		}

		return view('writing::base')->
		             with(array(
		             	  'articles' => $articles,
		             	  'ids' => $ids_array, 
		             	  'tag' => $tag));
	}	

	public function showArticle($slug) {

		if($article = Article::whereSlug($slug)->first()) {
			$article->visits++;
			$article->save();
			return view('writing::base')->with('article', $article);			
		}

	}

	public function showArticleWithId($id) {
		$article = Article::withTrashed()->find($id);
		return \Redirect::to(Writing::path().$article->slug);
	}

	public function getArticlesWithIds() {

		// Set Article Type
		\Input::get('article_type') ? $article_type = \Input::get('article_type') : $article_type = 'DEFAULT_ARTICLE_TYPE';

		// Echo Articles
		foreach(\Input::get('ids') as $id) {
			echo view('writing::partial.c-article')->
			           with(array('article' => Article::find($id),
			           	  		  'article_type' => $article_type,
			           	  		  'isTitleLinked' => true));
		}
	}

	public function getFeed(Request $request) {
		
		// create new feed
	    $feed = App::make("feed");

	    // cache the feed for 60 minutes (second parameter is optional)
	    $feed->setCache(60, 'laravelFeedKey');

	    // check if there is cached feed and build new only if is not
	    if (!$feed->isCached())
	    {
	    	$default_author = 'Nono Martínez Alonso';

	       // creating rss feed with our most recent articles
		   $show = Config::get('writing.feed.show');
	  	   
			$articles = Article::published()
			                   ->public()
			                   ->rss()
			                   ->orderBy('published_at', 'DESC')
			                   ->take($show)
			                   ->get();

	       // set your feed's title, description, link, pubdate and language
	       $feed->title = Config::get('writing.feed.title');
	       $feed->description = Config::get('writing.feed.description');
	       $feed->logo = Config::get('writing.feed.logo');
	       $feed->link = \URL::to('/'.Writing::path());
	       $feed->setDateFormat('datetime'); // 'datetime', 'timestamp' or 'carbon'
	       $feed->pubdate = $articles[0]->published_at;
	       $feed->lang = 'en';
	       $feed->setShortening(false); // true or false
	       $feed->setTextLimit(159); // maximum length of description text
	       $feed->setView("writing::feed.rss");

	       foreach ($articles as $article)
	       {
	           // set item's title, author, url, pubdate, description and content
	       	   $imageURL = '';
	       	   $image = '';
	       	   if ($article->image) {
	       	     $image = '<p><img src="'.$article->image.'" alt="'.$article->title.'"></p>';
	       	   }

	       	   if ($article->video) {
	       	   	 $image = '<p><a href="'.$request->root().'/'.Writing::path().$article->slug.'">'
	       	   	         .'<img src="'.\Thinker::getVideoThumb($article->video)
	       	   	         .'" alt="'.$article->title.'"></a></p>';
	       	   }

	       	   if ($imageURL != '') {
	       	   	 $image = '<p><img src="'.$imageURL.'" alt="'.$article->title.'"></p>';
	       	   }

	           $feed->add($article->title, $default_author, \URL::to(Writing::path().$article->slug), $article->published_at, '', str_replace('<img', '<img width="100%"', $image.\Markdown::string($article->text)));
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
