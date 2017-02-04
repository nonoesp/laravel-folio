<?php
  // Class
  $class_specified = '';
  if(isset($classes) && $classes != '') $class_specified = '[ '.$classes.' ]';
  // Data
  if(isset($data['is_navigation_hidden'])) { $is_navigation_hidden = $data['is_navigation_hidden']; }
  if(isset($data['is_media_hidden'])) { $is_media_hidden = $data['is_media_hidden']; }
  if(isset($data['image'])) { $image = $data['image']; }
  if(isset($data['description'])) { $description = $data['description']; }
?>

<!-- c-header-simple 1.0: styling based on frankchimero.com -->

<header class="[ c-header-simple ] {{ $class_specified }}">
	<div class="[ o-wrap  o-wrap--full ]">
		<a href="/" class="[ c-header-simple__name ]">
			nono.ma
		</a>
		@if(!isset($is_navigation_hidden))
		<nav role="navigation" class="[ c-header-simple__navigation ]">
			<ul>
				<li>
					<a href="{{ Config::get('space.path-prefix') }}" class="[ navigation-link js--navigation-link-says ]">
						{{ trans('folio.writing') }}
					</a>
				</li>
				<li>
					<a href="/about" class="[ navigation-link js--navigation-link-about ]">
						{{ trans('folio.about-me') }}
					</a>
				</li>
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
		  {!! view('space::partial.c-media')->with(['media' => Config::get('space.media_links')]) !!}
		@endif
	</div>
</header>
