@php
	use Illuminate\Support\Str;
	
	$header_active_link_classes = $header_active_link_classes ?? 'c-header-getting-simple__link--active';
	$class = $class ?? 'c-header-simple-v2';
  	$classes = Folio::expandClassesAsString($classes ?? config('folio.header.classes'), $class);
	$item = $item ?? null;
	$title = $title ?? null;
	$is_navigation_hidden = $is_navigation_hidden ?? null;
    $is_media_hidden = $is_media_hidden ?? null;
    $image = $image ?? null;
	$title_svg = $title_svg ?? null;
	$description = $description ?? null;
	$color = $color ?? null;
    $navigation = $navigation ?? [];
	$media_links = $media_links ?? config('folio.media_links');
	$header_domain = '';
	$folio_domain = config('folio.domain') ? Folio::protocol().config('folio.domain') : null;
	$header_domain = $header_domain ?? $folio_domain;

	// Set link type to highlight header link
	$hasPodcastTag = $item && $item->hasTag('podcast');
	$hasBlogTag = $item && $item->hasTag('blog');
	$path = request()->path();
	$isHome = $path === '/';
	$isPodcast = Str::of($path)->startsWith('podcast') || $hasPodcastTag;
	$isNewsletter = Str::of($path)->startsWith('newsletter');
	$isSisyphus = Str::of($path)->startsWith('sisyphus');
    $isWriting = $hasBlogTag || (!$isHome && !$isPodcast && !$isNewsletter && !$isSisyphus);

@endphp

<!-- c-header-simple · styling based on frankchimero.com -->

<header class="{{ $classes }}">
	<div class="[ o-wrap o-wrap--size-full ]">
		<a href="{{ $header_domain }}/" class="[ c-header-simple__name ]">

			@isset($title_svg)

				<div class="[ c-header__icon-desktop ]
					[ o-icon-prototype
					o-icon-prototype--display-inline
					o-icon-prototype--size-logo-line-desktop-static
					@if(isset($color))o-icon-prototype--color-{{$color}}@endif ]">
					{!! config('svg.'.$title_svg) !!}
				</div>

			@else
			
				{{ $title ?? 'Folio' }}
			
			@endisset

		</a>

		@unless($is_navigation_hidden)

			<nav role="navigation" class="[ c-header-simple__navigation ]">
				<ul>
					@foreach($navigation as $title => $link)
					@php
						$link_classes = [];
		
						if (is_array($link)) {
							$href = $link['href'] ?? 'empty';
							$link_classes = Folio::expandClasses($link['classes'] ?? [], 'link');
						} else {
							$href = $link;
						}
		
						$isExternal = Str::of($href)->startsWith('http');
						if ($isExternal) {
							array_push($link_classes, 'u-is-external-v2');
							array_push($link_classes, 'u-is-external--top-right');
						}
					@endphp

					<li>
						<a href="{{ $href }}" class="link {{ join(" ", $link_classes) }}" {{ $isExternal ? 'target="_blank"' : null}}>
							{!! trans('folio.'.$title) ?? $title !!}
						</a>
					</li>
					@endforeach

				</ul>
			</nav>

    	@endunless

		@isset($image)
		<div class="[ c-header-simple__image ]">
			<img src="{{ $image }}" style="border-radius: 50%">
		</div>
		@endisset
		
		@isset($description)
		<div class="[ c-header-simple__description 	@isset($description_classes)[ {{ $description_classes }} ]@endisset ]">
			<div class="[ o-wrap  o-wrap--bio  o-wrap--bleed ]">
				{!! $description !!}
			</div>
		</div>
		@endisset

		@unless($is_media_hidden)
		  {!! view('folio::partial.c-media')->with(['media' => $media_links]) !!}
		@endunless

	</div>
</header>
