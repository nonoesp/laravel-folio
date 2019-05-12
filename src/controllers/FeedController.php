<?php

namespace Nonoesp\Folio\Controllers;

use Illuminate\Http\Request;
use Item, User;
use Nonoesp\Folio\Folio;
use View;
use Config;
use Authenticate; // nonoesp/authenticate
use App;
use Markdown;
use Auth;
use Response, URL;

class FeedController extends Controller
{
	public static function makeFeed(Request $request, $domain, $item = null) {

		// Create new feed
		$feed = App::make("feed");

		// Defaults
		$cacheDuration = 0;
		$cacheKey = 'folio-feed';

		// Overrides (before caching)
		if ($item) {
			$cacheDuration = $item->intProperty('feed-cache-duration', $cacheDuration);
			$cacheKey = $item->stringProperty('feed-key', $cacheKey);
		}
		// Cache the feed for X minutes (second parameter is optional)
		$feed->setCache($cacheDuration, $cacheKey);

		// Check if there is cached feed and build new only if is not
		if (!$feed->isCached()) {

			$default_author = config('folio.feed.default-author');
			$feed_show = config('folio.feed.show');
			$feedTitle = config('folio.feed.title');
			$feedDescription = config('folio.feed.description');
			$feedLogo = config('folio.feed.logo');
			$feedLink = URL::to('/'.Folio::path());
			$feedLang = 'en';
			$feedCustomView = "folio::template.rss";

			// Collection
			if ($item) {
				$items = $item->collection();
			} else {
				$items = Item::published()
				->public()
				->orderBy('published_at', 'DESC')
				->rss()
				->take($feed_show)
				->get();
			}

			// Overrides (after caching)
			if ($item) {
				$cacheDuration = $item->intProperty('feed-cache-duration', $cacheDuration);
				$cacheKey = $item->stringProperty('feed-key', $cacheKey);
				$feedTitle = $item->stringProperty('feed-title', $feedTitle);
				$feedDescription = $item->stringProperty('feed-description', $feedDescription);
				$feedLogo = $item->stringProperty('feed-logo', $feedLogo);
				$feedLink = $item->stringProperty('feed-link', $feedLink);
				$feedLang = $item->stringProperty('feed-lang', $feedLang);

				// Try to use Item's template
				$itemTemplateView = $item->templateView(); // Template view for this item
				if(!$itemTemplateView || !view()->exists($itemTemplateView)) {
					// Fallback to default Folio item view if empty or non-existing
					$itemTemplateView = config('folio.view.item');
				} else {
					// Template view $itemTemplateView is good to go!
					$feedCustomView = $itemTemplateView;
				}

				// Allow to override with 'feed-view'
				$feedCustomView = $item->stringProperty('feed-view', $feedCustomView);
			}

			// set your feed's title, description, link, pubdate and language
			$feed->title = $feedTitle;
			$feed->description = $feedDescription;
			$feed->logo = $feedLogo;
			$feed->link = $feedLink;
			$feed->setDateFormat('datetime'); // 'datetime', 'timestamp' or 'carbon'
			if(count($items)) {
				$feed->pubdate = $items[0]->published_at;
			}
			$feed->lang = $feedLang;
			$feed->setShortening(false); // true or false
			$feed->setTextLimit(159); // maximum length of description text
			$feed->setCustomView($feedCustomView);

			foreach ($items as $item) {
				// Link
				$URL = URL::to($request->root().'/'.$item->path());
				$itemURL = $URL;
				if($item->link) {
					$URL = $item->link;
				}

				// Image
				$image = '';
				$item_image = $item->image;
				$item_image_src = $item->image_src;

				if ($item->stringProperty('feed-image')) {
					$item_image_src = $item->stringProperty('feed-image');
				} else if ($item->stringProperty('rss-image')) {
					$item_image_src = $item->stringProperty('rss-image');
				}

				// Make sure $item->image is global (not local like /img/u/image.jpg)
				if ($item_image && substr($item_image, 0, 1) == '/') {
					$item_image = $request->root().$item_image;
				}

				// And image_src is global or falls back to default
				if ($item_image_src == '' && $item->image) {
					$item_image_src = $item_image;
				} else if ($item_image_src && substr($item_image_src, 0, 1) == '/') {
					$item_image_src = $request->root().$item_image_src;
				} else if($item_image_src == '') {
					// Disabled to avoid displaying placeholder image in all posts
					// $item_image_src = config('folio.feed.default-image-src');
				}

				if ($item->video) {
					$image = '<p><a href="'.$URL.'">'
						.'<img src="'.$item->videoThumbnail()
						.'" alt="'.$item->title.'"></a></p>';
				} else if ($item->image) {
					// Disable to avoid displaying image twice and control location in Mailchimp template
					// $image = '<p><img src="'.$item_image.'" alt="'.$item->title.'"></p>';
				}

				// Text
				$html = str_replace([
					'<img',
					'src="/',
					'href="/',
					'<hr />',
				], [
					'<img width="100%"',
					'src="'.$request->root().'/',
					'href="'.$request->root().'/',
					'<br />',
				],
				$image.
				$item->htmlText(false, $request->root()));

				$html = str_replace(
					["<p><img", "/></p>"],
					["<p class=\"rss__img-wrapper\"><img", "/></p>"],
					$html);		

				// add item as array with custom tags
				$feedItem = [
					'title' => $item->title,
					'author' => $default_author,
					'url' => $URL,
					'pubdate' => $item->published_at,
					'description' => \Thinker::limitMarkdownText($item->htmlText(), 159, ['sup']),
					'content' => $html,
				];

				if ($item_image) {
					$feedItem['media:content'] = [
						'url' => $item_image,
						'medium' => 'image',
						// 'height' => '768',
						// 'width' => '1024'
					];
					$feedItem['enclosure'] = [
						'url' => $item_image,
						'type' => 'image/jpeg',
						// 'height' => '768',
						// 'width' => '1024'
					];
				}

				$feed->addItem($feedItem);

				}
		}

		/*
		$feed->render($format = null, $cache = null, $key = null))
			- $format (atom or rss)
			- $cache (duration, value of 0 turns off caching)
			- $key (custom cache key as string)
		*/
		return $feed->render('rss')->withHeaders([
			'Content-Type' => 'text/xml'
		]);
	}

}