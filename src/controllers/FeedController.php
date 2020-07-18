<?php

namespace Nonoesp\Folio\Controllers;

use Illuminate\Http\Request;
use Item, User;
use Nonoesp\Folio\Folio;
use View;
use Config;
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
			if ($locale = $item->stringProperty('locale')) {
				app()->setLocale($locale);
			}
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
			$feedCustomView = config('folio.feed.view', "folio::template.rss");

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

			$imgix = config('folio.imgix');

			config(['folio.imgix' => false]);
			foreach ($items as $item) {
				// Link for this RSS item
				$URL = $item->link ?? $item->URL();

				// Image
				$imageUrl = $item->feedImage();

				// if ($imageUrl) {
					// Disable to avoid displaying image twice and control location in Mailchimp template
					// $image = '<p><img src="'.$imageUrl.'" alt="'.$item->title.'"></p>';
				// }

				// Item->htmlText()
				$itemHTMLText = $item->htmlTextExcerpt([
					'veilImages' => false,
					'parseExternalLinks' => $request->root(),
					'stripTags' => ['norss', 'nofeed']
				]);

				// Item description (can be set as Mailchimp's preview text)
				$itemDescription = $item->stringProperty('feed-description') ?? \Thinker::limitMarkdownText($itemHTMLText, 159, ['sup']);

				// Text
				$html = str_replace(
					[
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
					//$image.
					$itemHTMLText
				);

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
					'description' => $itemDescription,
					'content' => $html,
				];

				$feedItem['item'] = $item;

				if ($imageUrl) {
					$feedItem['media:content'] = [
						'url' => $imageUrl,
						'medium' => 'image',
						// 'height' => '768',
						// 'width' => '1024'
					];
					$feedItem['enclosure'] = [
						'url' => $imageUrl,
						'type' => 'image/jpeg',
						// 'height' => '768',
						// 'width' => '1024'
					];

					// Square image for Instagram with imgix
					// RSS feed template needs to look for 'media:square'
					if ($imgix) {

						$squareImageSize = 2048;
						$squareImageLink = imgix($item->image, [
							'ar' => '1:1',
							'w' => $squareImageSize,
							'h' => $squareImageSize,
							'fit' => 'clamp',
						]);
						$feedItem['media:square'] = [
							'url' => htmlspecialchars($squareImageLink),
							'type' => 'image/jpeg',
							'height' => $squareImageSize,
							'width' => $squareImageSize,
						];

					}

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