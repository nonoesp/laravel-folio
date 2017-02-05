<?php
  $header_class = 'c-header';
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
			trans('space::base.writing') => ['/'.Config::get('space.path-prefix'), Config::get('space.path-prefix')],
			trans('space::base.about-me') => ['/about', 'about']
		];
  }
?>

<header class="[ c-header c-header--js ] {{ $class_specified }}">
	<div class="[ o-wrap o-wrap--full ]">

		<nav class="[ navigation ] [ u-mar-t-0x ]">
			<ul>
				<li><a href="/{{ Config::get('space.path-prefix') }}" class="navigation-link js--navigation-link-space">home</a></li>
        @if(Auth::check())
				<li><a href="/{!! Config::get('space.admin-path-prefix') !!}" class="navigation-link js--navigation-link-admin">admin</a></li>
        @endif
			</ul>
		</nav>

	</div>
</header>
