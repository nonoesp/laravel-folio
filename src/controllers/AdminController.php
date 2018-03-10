<?php

namespace Nonoesp\Folio\Controllers;

use Illuminate\Http\Request;
use Item, User, Thinker, Recipient, Property, Subscriber;
use Nonoesp\Folio\Folio;
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

		$select_fields = [
			'id',
			'title',
			'tags_str',
			'published_at',
			'deleted_at'];

		if($tag) {
			$items = Item::
			withTrashed()->
			withAnyTag([$tag])->
			orderBy('published_at', 'DESC')->
			get($select_fields);
		} else {
			$items = Item::
			withTrashed()->
			orderBy('published_at', 'DESC')->
			get($select_fields);
		}

		return View::make('folio::admin.item-list')->with([
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
					$item->title = Input::get('title');
					$item->slug = Thinker::uniqueSlugWithTableAndItem(Folio::table('items'), $item);
				} else {
					// Slug is empty, and was empty before
					if($item->title != Input::get('title')) {
						$item->title = Input::get('title');
						$item->slug = Thinker::uniqueSlugWithTableAndItem(Folio::table('items'), $item);
					}
				}
			} else {
				$item->slug = Input::get('slug_title');
				$item->title = Input::get('title');
				if($item->slug_title != Input::get('slug_title')) {
					// Slug has been edited
					$item->slug_title = Input::get('slug_title');
				// 	$item->slug = Thinker::uniqueSlugWithTableAndTitle(Folio::table('items'), $item->slug_title);
				}
			}

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
				// foreach(Folio::itemPropertyArray($item) as $key=>$value) {
				// 	$property = Property::updateOrCreate(
				// 		['item_id' => $item->id, 'name' => $key],
				// 		['value' => Input::get($key)]
				// 	);
				// }

				// Template
				$item->template = Input::get('template');

				// Tags
		  	$item->tags_str = Input::get('tags_str');
		  	if ($item->tags_str != '') {
		    	$item->retag(explode(",", $item->tags_str));
		    } else {
		    	$item->untag();
			}
			
			$is_hidden = Input::get('is_hidden');
			if($is_hidden && !$item->trashed()) {
			 	$item->delete();
			} else if(!$is_hidden && $item->trashed()) {
				$item->restore();
			}

		    $item->recipients_str = Input::get('recipients_str');
		    $item->rss = (Input::get('rss') ? true : false);
			$item->is_blog = (Input::get('is_blog') ? true : false);
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
		
		return view('folio::admin.item-edit', [
			'item' => $item,
			'templates' => Folio::templates()
		]);
	}

	public function ItemVersions(Request $request, $id) {
		$item = Item::withTrashed()->find($id);
		return view('folio::admin.item-versions', [
			'item' => $item
		]);		
	}

	public function getItemCreate() {
		return View::make('folio::admin.item-add');
	}

	public function postItemCreate() {

		$item = new Item();
		$item->title = Input::get('title');
		if($item->title == "") $item->title = "Untitled";

		// $item->text = Input::get('text');
		// $item->image = Input::get('image');
		// $item->image_src = Input::get('image_src');
		// if(Thinker::IsInstagramPostURL($item->image)) {
			// $item->image = Thinker::InstagramImageURL($item->image);
		// }
		// if(Thinker::IsInstagramPostURL($item->image_src)) {
			// $item->image_src = Thinker::InstagramImageURL($item->image_src);
		// }
		// $item->video = Input::get('video');
		// $item->link = Input::get('link');
		// $item->tags_str = Input::get('tags_str');
	    // $item->recipients_str = Input::get('recipients_str');
		// $item->rss = (Input::get('rss') ? true : false);
		
		$item->is_blog = false;

	    $item->slug_title = Input::get('slug_title');
	    if($item->slug_title == "") {
	    	$item->slug = Thinker::uniqueSlugWithTableAndTitle(Folio::table('items'), $item->title);
	    } else {
	    	$item->slug = Thinker::uniqueSlugWithTableAndTitle(Folio::table('items'), $item->slug_title);
	    }

		// Publishing Date
		$item->published_at = Input::get('published_at');
		if(!$item->published_at) {
			$item->published_at = Date::now();
		}

	    // Save
		$item->save();

		// laravel-tagging
		// if($item->tags_str != '') {
		//   $tags = explode(",", $item->tags_str);
		//   $item->tag($tags);
		// }

	    // if($item->recipients_str != NULL)
	    // {
			// foreach($item->recipientsArray() as $recipient)
			// {
			// $item->recipients()->save(new Recipient(["twitter" => $recipient]));
			// }
	    // }

		return Redirect::to(Folio::adminPath().'item/edit/'.$item->id);
	}

	public function getItemDelete($id) {
		$item = Item::find($id);
		$item->delete();

		return Redirect::to(Folio::adminPath().'items');
	}

	public function getItemRestore($id) {
		$item = Item::withTrashed()->find($id);
		$item->restore();

		// laravel-tagging
		if($item->tags_str != '') {
		  $tags = explode(",", $item->tags_str);
		  $item->tag($tags);
		}

		return Redirect::to(Folio::adminPath().'items');
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

	// Items API

	function postItemUpdate() {
		$item = Item::withTrashed()->find(Input::get('id'));
		$update = Input::get('update');
		foreach($update as $key=>$value) {
			$item[$key] = $value;
		}
		$item->save();
		return response()->json([
				'success' => true,
				'item' => $item,
				'update'=>$update
		]);
	}

	function postItemDelete() {
		$item = Item::withTrashed()->find(Input::get('id'));
		$item->delete();
		// $item->save();
		return response()->json([
				'success' => true,
				'item' => $item
		]);
	}

	function postItemRestore() {
		$item = Item::withTrashed()->find(Input::get('id'));
		$item->restore();
		return response()->json([
				'success' => true,
				'item' => $item
		]);
	}

	// Subscribers

	public function getSubscribers() {
		$subscribers = Subscriber::orderBy('id', 'DESC')->get();
		return view('folio::admin.subscribers')->withSubscribers($subscribers);
	}

}
