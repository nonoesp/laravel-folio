<?php
	use Illuminate\Support\Arr;

	$header_class = 'c-header-simple';
	// Class
	$class_specified = '';
	if(isset($classes) && $classes != '') {
		$class_specified = '[ ';
		foreach($classes as $class) {
			$class_specified .= $header_class.'--'.$class.' ';
		}
		$class_specified .= ']';
	}
  
	$defaults = [
		'is_navigation_hidden' => false,
		'is_media_hidden' => false,
		'image' => null,
  		'title' => null,
  		'title_svg' => null,
  		'description' => null,
  		'description_classes' => null,
		  'color' => null,
		  'media_links' => config('folio.media_links'),
		  'navigation' => ['Home' => '/']
	];

	foreach($defaults as $key => $default) {
		$$key = Arr::get($data, $key, $default);
	}

    $protocol = 'http://';
    if (\Request::secure()) {
      $protocol = 'https://';
	}

	$header_domain = config('folio.domain') ? $protocol.config('folio.domain') : '';

?>

<!-- c-header-simple · styling based on frankchimero.com -->

<header class="[ c-header-simple ] {{ $class_specified }}">
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

					@foreach($navigation as $title=>$href)

						<?php
						// Insert {path-prefix}
						$href[0] = str_replace('{path-prefix}', config('folio.path-prefix'), $href[0]);
						$href[1] = str_replace('{path-prefix}', config('folio.path-prefix'), $href[1]);
						$isExternal = false;
						if(count($href) > 2) { if($href[2] == 'external') { $isExternal = true; } }
						?>
						<li>
							<a href="{{ $href[0] }}" class="[ navigation-link js--navigation-link-{{$href[1]}} 
							@if($isExternal) u-is-external-v2 u-is-external--top-right ]" target="_blank" @else ]" @endif>
								{!! trans('folio.'.$title) !!}
							</a>
						</li>
						
					@endforeach

				</ul>
			</nav>

    @endunless

		@isset($image)
		<div class="[ c-header-simple__image ]">
			<img class="[ u-round ]" src="{{ $image }}">
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
