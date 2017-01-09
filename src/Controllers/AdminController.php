<?php

namespace Nonoesp\Writing\Controllers;

use Illuminate\Http\Request;
use Item, User, Thinker, Recipient, Property; // Must be defined in your aliases
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

	public function getItemList($tag = null) {
		$items = Item::withTrashed()->orderBy('published_at', 'DESC')->get();
		return View::make('writing::admin.item-list')->with(['items' => $items, 'tag' => $tag]);
	}

	public function ItemEdit(Request $request, $id) {

		$item = Item::withTrashed()->find($id);

		if ($request->isMethod('post')) {

			if(Input::get('slug_title') == null) {
				if($item->slug_title != null) {
					// Slug has been removed, not empty before
					$item->slug_title = null;
					$item->slug = Thinker::uniqueSlugWithTableAndTitle('items', Input::get('title'));
				} else {
					// Slug is empty, and was empty before
					if($item->title != Input::get('title')) {
						$item->slug = Thinker::uniqueSlugWithTableAndTitle('items', Input::get('title'));
					}
				}
			} else {
				if($item->slug_title != Input::get('slug_title')) {
					// Slug has been edited
					$item->slug_title = Input::get('slug_title');
					$item->slug = Thinker::uniqueSlugWithTableAndTitle('items', $item->slug_title);
				}
			}

			$item->title = Input::get('title');
		  	$item->published_at = Input::get('published_at');
		  	$item->image = Input::get('image');
		  	$item->image_src = Input::get('image_src');
			if(Thinker::IsInstagramPostURL($item->image)) {
				$item->image = Thinker::InstagramImageURL($item->image);
			}
			if(Thinker::IsInstagramPostURL($item->image_src)) {
				$item->image_src = Thinker::InstagramImageURL($item->image_src);
			}
		  	$item->video = Input::get('video');

				// Update Properties (before tags)
				foreach(Writing::itemPropertyArray($item) as $key=>$value) {
					$property = Property::updateOrCreate(
						['item_id' => $item->id, 'name' => $key],
						['value' => Input::get($key)]
					);
				}

				// Tags
		  	$item->tags_str = Input::get('tags_str');
		  	if ($item->tags_str != '') {
		    	$item->retag(explode(",", $item->tags_str));
		    } else {
		    	$item->untag();
		    }

		    $item->recipients_str = Input::get('recipients_str');
		    $item->rss = (Input::get('rss') ? true : false);
		    $item->recipients()->delete();
		    if($item->recipients_str != NULL)
		    {
				foreach($item->recipientsArray() as $recipient)
				{
				$item->recipients()->save(new Recipient(["twitter" => $recipient]));
				}
		    }
		 	$item->text = Input::get('text');
			$item->save();
		}

		return View::make('writing::admin.item-edit')->withItem($item);
	}

	public function getItemCreate() {
		return View::make('writing::admin.item-add');
	}

	public function postItemCreate() {

		$item = new Item();
		$item->title = Input::get('title');
		$item->text = Input::get('text');
		$item->image = Input::get('image');
		$item->image_src = Input::get('image_src');
		if(Thinker::IsInstagramPostURL($item->image)) {
			$item->image = Thinker::InstagramImageURL($item->image);
		}
		if(Thinker::IsInstagramPostURL($item->image_src)) {
			$item->image_src = Thinker::InstagramImageURL($item->image_src);
		}
		$item->video = Input::get('video');
		$item->tags_str = Input::get('tags_str');
	    $item->recipients_str = Input::get('recipients_str');
	    $item->rss = (Input::get('rss') ? true : false);
	    $item->slug_title = Input::get('slug_title');
	    if($item->slug_title == "") {
	    	$item->slug = Thinker::uniqueSlugWithTableAndTitle('items', $item->title);
	    } else {
	    	$item->slug = Thinker::uniqueSlugWithTableAndTitle('items', $item->slug_title);
	    }

		// Publishing Date
		$item->published_at = Input::get('published_at');
		if(!$item->published_at) {
			$item->published_at = Date::now();
		}

	    // Save
		$item->save();

		// laravel-tagging
		if($item->tags_str != '') {
		  $tags = explode(",", $item->tags_str);
		  $item->tag($tags);
		}

	    if($item->recipients_str != NULL)
	    {
			foreach($item->recipientsArray() as $recipient)
			{
			$item->recipients()->save(new Recipient(["twitter" => $recipient]));
			}
	    }

		return Redirect::to(Writing::adminPath().'item/edit/'.$item->id);
	}

	public function getItemDelete($id) {
		$item = Item::find($id);
		$item->delete();

		return Redirect::to(Writing::adminPath().'items');
	}

	public function getItemRestore($id) {
		$item = Item::withTrashed()->find($id);
		$item->restore();

		// laravel-tagging
		if($item->tags_str != '') {
		  $tags = explode(",", $item->tags_str);
		  $item->tag($tags);
		}

		return Redirect::to(Writing::adminPath().'items');
	}

	/*
	public function getVisits() {
		return View::make('admin.visits');
	}*/


}
