<?php

namespace Nonoesp\Writing\Controllers;

use Illuminate\Http\Request;
use Article, User; // Must be defined in your aliases
use Nonoesp\Writing\Writing;
use View;
use Config;
use Authenticate; // Must be installed (nonoesp/authenticate) and defined in your aliases
use Input;

class AdminController extends Controller
{
	public function getDashboard() {
		return View::make('admin.dashboard');
	}

	public function getArticleList() {
		$articles = Article::withTrashed()->orderBy('published_at', 'DESC')->get();

		foreach($articles as $article)
		{
			echo '<a href="/'.Config::get('writing.admin_path').'/article/edit/'.$article->id.'">'.$article->title.'<br>';
		}

		//return View::make('admin.article-list')->withArticles($articles);
	}

	public function ArticleEdit(Request $request, $id) {

		$article = Article::withTrashed()->find($id);

		if ($request->isMethod('post')) {
		  	if($article->title != Input::get('title')) {
				$article->title = Input::get('title');
		    	$article->slug = Helper::uniqueSlugWithTableAndTitle('articles', $article->title);
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
		 	$article->text = Input::get('text');
			$article->save();
		}

		return View::make('writing::admin.article-edit')->withArticle($article);
	}

	public function getArticleCreate() {
		return View::make('admin.article-add');
	}

	public function postArticleCreate() {

		$article = new Article();
		$article->title = Input::get('title');
		$article->text = Input::get('text');
		$article->image = Input::get('image');
		$article->video = Input::get('video');
		$article->tags = Input::get('tags');				
		$article->slug = Helper::uniqueSlugWithTableAndTitle('articles', $article->title);
		$article->published_at = Input::get('published_at');
		if(!$article->published_at) {		
			$article->published_at = Date::now();
		}
		$article->save();

		// laravel-tagging
		if($article->tags != '') {
		  $tags = explode(",", $article->tags);
		  $article->tag($tags);
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
