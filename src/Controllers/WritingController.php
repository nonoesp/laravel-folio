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

		// Config variables
		$published_show = Config::get("writing.published-show");
		$expected_show = Config::get("writing.expected-show");

		$published_existing = Article::published()
    				   		->visibleFor($twitter_handle)
    				   		->count();

		$expected_existing = Article::expected()
    				   	   ->visibleFor($twitter_handle)
    				       ->count();

		if($published_show > $published_existing) {
			$published_show = $published_existing;
		}
		if($expected_show > $expected_existing) {
			$expected_show = $expected_existing;
		}

    	$published_left = $published_existing - $published_show;
    	$expected_left = $expected_existing - $expected_show;

		// Get Articles + Articles ids

    	$left = Article::published()
    				   ->visibleFor($twitter_handle)
    				   ->count() - $published_show;

    	$articles = Article::published()
    					   ->visibleFor($twitter_handle)
    					   ->orderBy('published_at', 'DESC')
    					   ->skip(0)
    					   ->take($published_show)
    					   ->get();

		$ids_array = array();

  		  if ($left > 0)
  		  {
			$ids = Article::published()
	    				  ->visibleFor($twitter_handle)
			              ->select('id','published_at')    				  
	    				  ->orderBy('published_at', 'DESC')
	    				  ->skip($published_show)
	    				  ->take($left)
	    				  ->get();

			foreach($ids as $id)
			{
				array_push($ids_array, $id->id);
			}	    				  
	    }

		// Get Expected Articles
		$articles_expected = Article::expected()
									->visibleFor($twitter_handle)
									->orderBy('published_at', 'DESC')
									->take($expected_show)
									->get();

		return view('writing::base')->with(array('articles' => $articles,
													  'ids' => $ids_array,
													  'articles_expected' => $articles_expected));
	}

	// Simplify making showHome a generic function, then call it directly from route or from Controller function
	public function showArticleTag($tag) {

		// Get user's Twitter handle (or visitor)
		$twitter_handle = Authenticate::isUserLoggedInTwitter();

		// Config variables
		$published_show = Config::get("writing.published-show");
		$expected_show = Config::get("writing.expected-show");

		$published_existing = Article::withAnyTag($tag)
    					    ->published()
    				   		->visibleFor($twitter_handle)
    				   		->count();

		$expected_existing = Article::withAnyTag($tag)
						   ->expected()
    				   	   ->visibleFor($twitter_handle)
    				       ->count();

		if($published_show > $published_existing) {
			$published_show = $published_existing;
		}
		if($expected_show > $expected_existing) {
			$expected_show = $expected_existing;
		}

    	$published_left = $published_existing - $published_show;
    	$expected_left = $expected_existing - $expected_show;

    	// Get content

    	$articles = Article::withAnyTag($tag) 
    					   ->published()
    					   ->visibleFor($twitter_handle)
    					   ->orderBy('published_at', 'DESC')
    					   ->take($published_show)
    					   ->get();

		$ids_array = array();

  		if ($published_left > 0)
  		{

			$ids = Article::withAnyTag($tag)
						  ->published()
	    				  ->visibleFor($twitter_handle)
			              ->select('id','published_at')    				  
	    				  ->orderBy('published_at', 'DESC')
	    				  ->skip($published_show)
	    				  ->take($published_left)
	    				  ->get();

			foreach($ids as $id)
			{
				array_push($ids_array, $id->id);
			}
		}

		// Get Expected Articles
		$articles_expected = Article::withAnyTag($tag)
								    ->expected()
									->visibleFor($twitter_handle)
									->orderBy('published_at', 'DESC')
									->take($expected_show)
									->get();

		return view('writing::base')->with(
								[
		             	  			'articles' => $articles,
		             	  			'ids' => $ids_array, 
		             	  			'tag' => $tag,
		             	  			'articles_expected' => $articles_expected
		             	  		]);
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
		   $feed_show = Config::get('writing.feed.show');
	  	   
	       $articles = Article::published()
					          ->public()
					          ->orderBy('published_at', 'DESC')
					          ->rss()
					          ->take($feed_show)
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
	       	   $image_src = Config::get('writing.feed.default-image-src');
	       	   $image = '';

			   if ($article->video) {
	       	     $image = '<p><a href="'.$request->root().'/'.Writing::path().$article->slug.'">'
	       	   	         .'<img src="'.\Thinker::getVideoThumb($article->video)
	       	   	         .'" alt="'.$article->title.'"></a></p>';
	       	   } else if ($article->image) {
	       	     $image = '<p><img src="'.$article->image.'" alt="'.$article->title.'"></p>';
	       	   }

	       	   if ($article->image_src != '') {
	       	   	$image_src = $article->image_src;
	       	   } else if ($article->image != '') {
	       	   	$image_src = $article->image;
	       	   }

	           $feed->add(
	           	$article->title,
	           	$default_author,
	           	\URL::to(Writing::path().$article->slug),
	           	$article->published_at,
	           	\Thinker::limitMarkdownText($article->text, 159),
	           	str_replace('<img', '<img width="100%"', $image.\Markdown::string($article->text)),
	           	['url'=>$image_src,'type'=>'image/jpeg']);
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
