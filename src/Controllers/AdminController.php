<?php

namespace Nonoesp\Writing\Controllers;

use Illuminate\Http\Request;
use Article, User, Thinker, Recipient; // Must be defined in your aliases
use Nonoesp\Writing\Writing;
use View;
use Config;
use Authenticate; // Must be installed (nonoesp/authenticate) and defined in your aliases
use Input;
use Redirect;
use Date;

class AdminController extends Controller
{
	public function getDashboard() {
		return View::make('admin.dashboard');
	}

	public function getArticleList() {
		$articles = Article::withTrashed()->orderBy('published_at', 'DESC')->get();

		return View::make('writing::admin.article-list')->withArticles($articles);
	}

	public function ArticleEdit(Request $request, $id) {

		$article = Article::withTrashed()->find($id);

		if ($request->isMethod('post')) {
		  	if($article->title != Input::get('title')) {
				$article->title = Input::get('title');
		    	$article->slug = Thinker::uniqueSlugWithTableAndTitle('articles', $article->title);
		  	}
		  	$article->published_at = Input::get('published_at');
		  	$article->image = Input::get('image');
		  	$article->video = Input::get('video');
		  	$article->tags_str = Input::get('tags_str');
		  	if ($article->tags_str != '') {
		    	$article->retag(explode(",", $article->tags_str));		  	
		    } else {
		    	$article->untag();
		    }
		    $article->recipients_str = Input::get('recipients_str');
		    $article->recipients()->delete();
		    if($article->recipients_str != NULL)
		    {
				foreach($article->recipientsArray() as $recipient)
				{
				$article->recipients()->save(new Recipient(["twitter" => $recipient]));
				}		    	
		    }
		 	$article->text = Input::get('text');
			$article->save();
		}

		return View::make('writing::admin.article-edit')->withArticle($article);
	}

	public function getArticleCreate() {
		return View::make('writing::admin.article-add');
	}

	public function postArticleCreate() {

		$article = new Article();
		$article->title = Input::get('title');
		$article->text = Input::get('text');
		$article->image = Input::get('image');
		$article->video = Input::get('video');
		$article->tags_str = Input::get('tags_str');	
	    $article->recipients_str = Input::get('recipients_str');		
		$article->slug = Thinker::uniqueSlugWithTableAndTitle('articles', $article->title);

		// Publishing Date
		$article->published_at = Input::get('published_at');
		if(!$article->published_at) {		
			$article->published_at = Date::now();
		}
		
	    // Save
		$article->save();

		// laravel-tagging
		if($article->tags_str != '') {
		  $tags = explode(",", $article->tags_str);
		  $article->tag($tags);
		}

	    if($article->recipients_str != NULL)
	    {
			foreach($article->recipientsArray() as $recipient)
			{
			$article->recipients()->save(new Recipient(["twitter" => $recipient]));
			}		    	
	    }		

		return Redirect::to('/admin/article/edit/'.$article->id);
	}

	public function getArticleDelete($id) {
		$article = Article::find($id);
		$article->delete();

		return Redirect::to('/admin/articles');
	}

	public function getArticleRestore($id) {
		$article = Article::withTrashed()->find($id);
		$article->restore();

		return Redirect::to('/admin/articles');
	}	

	/*
	public function getVisits() {
		return View::make('admin.visits');
	}*/


}
