<?php

namespace Nonoesp\Space\Controllers;

use Illuminate\Http\Request;
use Item, User, Thinker, Recipient, Property, Subscriber; // Must be defined in your aliases
use Nonoesp\Space\Space;
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

		$existing_tags = Item::existingTags()->sortBy(function($item) {
			return -$item->count;
		});
		
		if($tag) {
			$items = Item::withTrashed()->withAnyTag([$tag])->orderBy('published_at', 'DESC')->get();
		} else {
			$items = Item::withTrashed()->orderBy('published_at', 'DESC')->get();
		}

		return View::make('space::admin.item-list')->with([
			'items' => $items,
			'tag' => $tag,
			'existing_tags' => $existing_tags
		]);
	}

	public function ItemEdit(Request $request, $id) {

		$item = Item::withTrashed()->find($id);

		if ($request->isMethod('post')) {

			if(Input::get('slug_title') == null) {
				if($item->slug_title != null) {
					// Slug has been removed, not empty before
					$item->slug_title = null;
					$item->slug = Thinker::uniqueSlugWithTableAndTitle('space_items', Input::get('title'));
				} else {
					// Slug is empty, and was empty before
					if($item->title != Input::get('title')) {
						$item->slug = Thinker::uniqueSlugWithTableAndTitle('space_items', Input::get('title'));
					}
				}
			} else {
				if($item->slug_title != Input::get('slug_title')) {
					// Slug has been edited
					$item->slug_title = Input::get('slug_title');
					$item->slug = Thinker::uniqueSlugWithTableAndTitle('space_items', $item->slug_title);
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
				$item->link = Input::get('link');

				// Update Properties (before tags)
				// foreach(Space::itemPropertyArray($item) as $key=>$value) {
				// 	$property = Property::updateOrCreate(
				// 		['item_id' => $item->id, 'name' => $key],
				// 		['value' => Input::get($key)]
				// 	);
				// }

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

		return View::make('space::admin.item-edit')->withItem($item);
	}

	public function getItemCreate() {
		return View::make('space::admin.item-add');
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
		$item->link = Input::get('link');
		$item->tags_str = Input::get('tags_str');
	    $item->recipients_str = Input::get('recipients_str');
	    $item->rss = (Input::get('rss') ? true : false);
	    $item->slug_title = Input::get('slug_title');
	    if($item->slug_title == "") {
	    	$item->slug = Thinker::uniqueSlugWithTableAndTitle('space_items', $item->title);
	    } else {
	    	$item->slug = Thinker::uniqueSlugWithTableAndTitle('space_items', $item->slug_title);
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

		return Redirect::to(Space::adminPath().'item/edit/'.$item->id);
	}

	public function getItemDelete($id) {
		$item = Item::find($id);
		$item->delete();

		return Redirect::to(Space::adminPath().'items');
	}

	public function getItemRestore($id) {
		$item = Item::withTrashed()->find($id);
		$item->restore();

		// laravel-tagging
		if($item->tags_str != '') {
		  $tags = explode(",", $item->tags_str);
		  $item->tag($tags);
		}

		return Redirect::to(Space::adminPath().'items');
	}

	// Properties (API)

	function postPropertyUpdate() {
		$property = Property::find(Input::get('id'));
		$key = Input::get('name');
		$value = Input::get('value');
		$label = Input::get('label');
		if($key) $property->name = $key;
		if($value) $property->value = $value;
		if($label) $property->label = $label;
		$property->save();
		return response()->json([
				'success' => true,
				'property' => $property
		]);
	}

	function postPropertyDelete() {
		$property_id = Input::get('id');
		$property = Property::find($property_id);
		$property->delete();

		return response()->json([
				'success' => true,
				'property_id' => $property->id,
		]);
	}

	function postPropertyCreate() {
		$key = Input::get('name');
		$value = Input::get('value');
		$label = Input::get('label');
		$item_id = Input::get('item_id');
		$property = new Property();
		$property->item_id = $item_id;
		$property->save();

		return response()->json([
				'success' => true,
				'property_id' => $property->id,
				'item_id' => $item_id
		]);
	}

	// Subscribers

	public function getSubscribers() {
		$subscribers = Subscriber::orderBy('id', 'DESC')->get();
		return view('space::admin.subscribers')->withSubscribers($subscribers);
	}




}
