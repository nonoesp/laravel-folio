<?php
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
  // Data
  if(isset($data['is_navigation_hidden'])) { $is_navigation_hidden = $data['is_navigation_hidden']; }
  if(isset($data['is_media_hidden'])) { $is_media_hidden = $data['is_media_hidden']; }
  if(isset($data['image'])) { $image = $data['image']; }
	if(isset($data['title'])) { $title = $data['title']; }
	if(isset($data['title_svg'])) { $title_svg = $data['title_svg']; }
  if(isset($data['description'])) { $description = $data['description']; }
	if(isset($data['color'])) { $color = $data['color']; }
  if(isset($data['navigation'])) { $navigation = $data['navigation']; } else {
    $navigation = [
			trans('folio::base.writing') => ['/'.Config::get('folio.path-prefix'), Config::get('folio.path-prefix')],
			trans('folio::base.about-me') => ['/about', 'about']
		];
  }
	if(isset($data['media_links'])) { $media_links = $data['media_links']; } else { $media_links = config('folio.media_links'); }
	$header_domain = '';
	if(config('folio.domain')) {
		$header_domain = 'http://'.config('folio.domain');
	}
?>

<!-- c-header-simple · styling based on frankchimero.com -->

<header class="[ c-header-simple ] {{ $class_specified }}">
	<div class="[ o-wrap o-wrap--size-full ]">
		<a href="{{ $header_domain }}/" class="[ c-header-simple__name ]">

			@if(isset($title_svg))

				<div class="[ -u-hidden ] [ c-header__icon-desktop ]
					[ o-icon-prototype
					o-icon-prototype--display-inline
					o-icon-prototype--size-logo-line-desktop-static
					@if(isset($color))o-icon-prototype--color-{{$color}}@endif ]">
					{!! config('svg.'.$title_svg) !!}
				</div>

			@else
			
				{{ $title or 'Folio' }}
			
			@endif

		</a>

		@if(!isset($is_navigation_hidden))

			<nav role="navigation" class="[ c-header-simple__navigation ]">
				<ul>

					@foreach($navigation as $title=>$href)

						<?php
						// Insert {path-prefix}
						$href[0] = str_replace('{path-prefix}', config('folio.path-prefix'), $href[0]);
						$href[1] = str_replace('{path-prefix}', config('folio.path-prefix'), $href[1]);
						?>
						<li>
							<a href="{{ $href[0] }}" class="[ navigation-link js--navigation-link-{{$href[1]}} ]">
								{{ trans('folio.'.$title) }}
							</a>
						</li>
						
					@endforeach

				</ul>
			</nav>

    @endif

		@if(isset($image))
		<div class="[ c-header-simple__image ]">
			<img class="[ u-round ]" src="{{ $image }}">
		</div>
		@endif
		
		@if(isset($description))
		<div class="[ c-header-simple__description ]">
			<div class="[ o-wrap  o-wrap--bio  o-wrap--bleed ]">
				{!! $description !!}
			</div>
		</div>
		@endif

		@if(!isset($is_media_hidden))
		  {!! view('folio::partial.c-media')->with(['media' => $media_links]) !!}
		@endif

	</div>
</header>
