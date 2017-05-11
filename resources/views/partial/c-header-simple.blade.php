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
  if(isset($data['description'])) { $description = $data['description']; }
  if(isset($data['navigation'])) { $navigation = $data['navigation']; } else {
    $navigation = [
			trans('folio::base.writing') => ['/'.Config::get('folio.path-prefix'), Config::get('folio.path-prefix')],
			trans('folio::base.about-me') => ['/about', 'about']
		];
  }
?>

<!-- c-header-simple 1.0: styling based on frankchimero.com -->

<header class="[ c-header-simple ] {{ $class_specified }}">
	<div class="[ o-wrap o-wrap--size-full ]">
		<a href="/" class="[ c-header-simple__name ]">
			nono.ma
		</a>
		@if(!isset($is_navigation_hidden))
		<nav role="navigation" class="[ c-header-simple__navigation ]">
			<ul>

        @foreach($navigation as $title=>$href)
          <li>
  					<a href="{{ $href[0] }}" class="[ navigation-link js--navigation-link-{{$href[1]}} ]">
  						{{ $title }}
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
		  {!! view('folio::partial.c-media')->with(['media' => Config::get('folio.media_links')]) !!}
		@endif
	</div>
</header>
