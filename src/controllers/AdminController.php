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

		if(!$item) {
			return response()->view('errors.404', [], 404);
		}

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
				if($item->template == "null") {
					$item->template = null;
				}

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

	/**
	 * A page to make sure the user wants to force delete
	 * a Folio item.
	 */
	public function ItemDestroy(Request $request, $id) {
		$item = Item::withTrashed()->find($id);
		return view('folio::admin.item-destroy', ['item' => $item]);
	}

	/**
	 * Destroys the item forever (can't be undone).
	 */
	public function ItemForceDelete(Request $request, $id) {
		$item = Item::withTrashed()->find($id);
		if($item) {
			$item->forceDelete();
		}
		return Redirect::to(Folio::adminPath().'items');
	}	

	public function getItemCreate() {
		return view('folio::admin.item-add');
	}

	public function setLocateToFirstTranslation() {
		// Initial values default to English
		// or first value of folio.translations config array (if valid)
		$default_locale = 'en';
		$translations = config('folio.translations');
		if($translations && in_array($translations[0], \ResourceBundle::getLocales(''))) {
			$default_locale = $translations[0];
		}
		app()->setLocale($default_locale);
	}

	public function postItemCreate() {

		$this->setLocateToFirstTranslation();

		$item = new Item();
		$item->title = Input::get('title');
		if($item->title == "") {
			$item->title = "Untitled";
		}
		if($item->text == "") {
			$item->text = "";
		}
		
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
		$property->name = $key;
		$property->value = $value;
		$property->label = $label;
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

	function postPropertySwap() {
		$property_id = Input::get('id');
		$property_id_swap = Input::get('id2');
		$property = Property::find($property_id);
		$property_swap = Property::find($property_id_swap);

		Property::swapOrder($property, $property_swap);

		return response()->json([
			'success' => true
		]);
	}

	function postPropertySort() {
		$ids = Input::get('ids');
		Property::setNewOrder($ids);

		return response()->json([
			'success' => true
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
		return response()->json([
				'success' => true,
				'item' => $item
		]);
	}

	function postItemRestore() {
		$item = Item::withTrashed()->find(Input::get('id'));
		$item->restore();
		$item->retag($item->tags_str);
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

	/**
	 *  Ajax Item Update
	 * TODO: Use CRUD to bind the model from Vue.js to Eloquent?
	 */
	
	public function postItemUpdateAjax(Request $request, $id) {

		$this->setLocateToFirstTranslation();

		$item = Item::withTrashed()->find($id);

		$item->text = request('text');
		$item->video = request('video');
		$item->published_at = request('published_at');
		$item->image = request('image');
		$item->image_src = request('image_src');
		$item->link = request('link');
		$item->slug_title = request('slug_title');
		$item->tags_str = request('tags_str');
		$item->recipients_str = request('recipients_str');
		$item->template = request('template');
		if(request('deleted_at')) {
			$item->deleted_at = \Date::now();
		} else {
			$item->deleted_at = null;
		}
		$item->rss = request('rss');
		$item->is_blog = request('is_blog');
		
		// title
		if(request('slug_title') == null) {
			if($item->slug_title != null) {
				// Slug has been removed, not empty before
				$item->slug_title = null;
				$item->title = request('title');
				$item->slug = Thinker::uniqueSlugWithTableAndItem(Folio::table('items'), $item);
			} else {
				// Slug is empty, and was empty before
				if($item->title != request('title')) {
					$item->title = request('title');
					$item->slug = Thinker::uniqueSlugWithTableAndItem(Folio::table('items'), $item);
				}
			}
		} else {
			$item->slug = request('slug_title');
			$item->title = request('title');
			if($item->slug_title != request('slug_title')) {
				// Slug has been edited
				$item->slug_title = request('slug_title');
			}
		}

		// ..
		if(Thinker::IsInstagramPostURL($item->image)) {
			$item->image = Thinker::InstagramImageURL($item->image);
		}
		if(Thinker::IsInstagramPostURL($item->image_src)) {
			$item->image_src = Thinker::InstagramImageURL($item->image_src);
		}
		if($item->template == "null") {
			$item->template = null;
		}
		if ($item->tags_str != '') {
			$item->retag(explode(",", $item->tags_str));
		} else {
			$item->untag();
		}

		// Recipients
		$item->recipients()->delete();
		if($item->recipients_str != NULL)
		{
			foreach($item->recipientsArray() as $recipient)
			{
			$item->recipients()->save(new Recipient(["twitter" => $recipient]));
			}
		}

		$item->save();

		return response()->json([
			'message' => 'Post updated successfully!',
			'path' => '/'.$item->path()
		], 200);

	}

}
