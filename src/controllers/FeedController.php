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
		$cacheDuration = 5;
		$cacheKey = 'folio-feed';

		// Overrides (before caching)
		if ($item) {
			$cacheDuration = $item->intProperty('feed-cache-duration', $cacheDuration);
			$cacheKey = $item->stringProperty('feed-key', $cacheKey);
		}
		// Cache the feed for 5 minutes (second parameter is optional)
		$feed->setCache($cacheDuration, $cacheKey);
		$feed->clearCache(); // Temporarily because laravelium/feed is buggy

		return $cacheDuration;

		// Check if there is cached feed and build new only if is not
		if (!$feed->isCached()) {

			$default_author = config('folio.feed.default-author');
			$feed_show = config('folio.feed.show');
			$feedTitle = config('folio.feed.title');

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
			}

			// set your feed's title, description, link, pubdate and language
			$feed->title = $feedTitle;
			$feed->description = config('folio.feed.description');
			$feed->logo = config('folio.feed.logo');
			$feed->link = URL::to('/'.Folio::path());
			$feed->setDateFormat('datetime'); // 'datetime', 'timestamp' or 'carbon'
			if(count($items)) {
				$feed->pubdate = $items[0]->published_at;
			}
			$feed->lang = 'en';
			$feed->setShortening(false); // true or false
			$feed->setTextLimit(159); // maximum length of description text
			$feed->setCustomView("folio::feed.rss");

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

				if ($item->stringProperty('rss-image')) {
					$item_image = $item->stringProperty('rss-image');
				}

				// Make sure $item->image is global (not local like /img/u/image.jpg)
				if ($item_image && substr($item_image, 0, 1) == '/') {
					$item_image = $request->root().$item_image;
				}

				// And image_src is global or falls back to default
				if ($item_image_src == '' && $item->image) {
					$item_image_src = $item_image;
				} else if ($item->image_src && substr($item->image_src, 0, 1) == '/') {
					$item_image_src = $request->root().$item->image_src;
				} else {
					$item_image_src = config('folio.feed.default-image-src');
				}

				if ($item->video) {
					$image = '<p><a href="'.$URL.'">'
						.'<img src="'.$item->videoThumbnail()
						.'" alt="'.$item->title.'"></a></p>';
				} else if ($item->image) {
					$image = '<p><img src="'.$item_image.'" alt="'.$item->title.'"></p>';
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
				$image.$item->htmlText(false, $request->root()));

				$html = str_replace(
					["<p><img", "/></p>"],
					["<p class=\"rss__img-wrapper\"><img", "/></p>"],
					$html);		

				$feed->add(
					$item->title,
					$default_author,
					$URL,
					$item->published_at,
					\Thinker::limitMarkdownText($item->htmlText(), 159, ['sup']),
					$html,
					['url'=>$item_image_src,'type'=>'image/jpeg']);
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