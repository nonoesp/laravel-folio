<?php
  // Class
  $class_specified = '';
  if(isset($classes) && $classes != '') $class_specified = '[ '.$classes.' ]';
  // Data
  if(isset($data['is_navigation_hidden'])) { $is_navigation_hidden = $data['is_navigation_hidden']; }
  if(isset($data['image'])) { $image = $data['image']; }
?>

<!-- c-header-simple 1.0: styling based on frankchimero.com -->

<header class="[ c-header-simple ] {{ $class_specified }}">
	<div class="[ o-wrap  o-wrap--full ]">
		<a href="/" class="[ c-header-simple__name ]">
			nono.ma
		</a>
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
		@if(isset($image))
		<div class="[ c-header-simple__image ]">
			<img class="[ u-round ]" src="{{ $image }}">
		</div>
		@endif
		@if(isset($description))
		<div class="[ c-header-simple__description ]">
			<div class="[ o-wrap  o-wrap--bio  o-wrap--bleed ]">
				{{ $description }}
			</div>
		</div>
		@endif

		@if(!isset($is_media_hidden))
		<div class="[ c-header-simple__media ]">

	        <span class="[ c-social -m-social--small ]">
	            <a href="{{ Config::get('data.feeds.main') }}" target="_blank" class="fa--social">
	              <i class="fa fa-rss"></i>
	            </a>
		        <a href="http://facebook.com/nonoesp" target="_blank" class="fa--social">
		          <i class="fa fa-facebook"></i>
		        </a>
		        <a href="http://twitter.com/nonoesp" target="_blank" class="fa--social">
		          <i class="fa fa-twitter"></i>
		        </a>
		        <a href="http://instagram.com/nonoesp" target="_blank" class="fa--social">
		          <i class="fa fa-instagram"></i>
		        </a>
		        <a href="http://dribbble.com/nonoesp" target="_blank" class="fa--social">
		          <i style="font-weight:600" class="fa fa-dribbble"></i>
		        </a>
		        <a href="http://github.com/nonoesp" target="_blank" class="fa--social">
		          <i class="fa fa-github"></i>
		        </a>
		        {{--<a href="http://gettingsimple.com" target="_blank" class="fa--social">
		          <i class="fa fa-star"></i>
		        </a>--}}
	        </span>

		</div>

		<div class="[ c-header-simple__media ]">

			<div class="[ c-social ] [ m-fa {{-- $class or '' --}} ]">

			<a href="{{ Config::get('services.social.facebook') }}" target="_blank" class="m-fa__link {{ $class_link or '' }}">
					<i class="[ fa fa-facebook fa--social ]"></i>
			</a>

			 <a href="{{ Config::get('services.social.twitter') }}" target="_blank" class="m-fa__link {{ $class_link or '' }}">
					 <i class="[ fa fa-twitter fa--social ]"></i>
			 </a>

			 <a href="{{ Config::get('services.social.instagram') }}" target="_blank" class="m-fa__link {{ $class_link or '' }}">
					 <i class="[ fa fa-instagram fa--social ]"></i>
			 </a>

			 <a href="{{ Config::get('services.social.linkedin') }}" target="_blank" class="m-fa__link {{ $class_link or '' }}">
					 <i class="[ fa fa-linkedin fa--social ]"></i>
			 </a>

			</div>


		</div>
		@endif
	</div>
</header>
