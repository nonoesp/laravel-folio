<?php
  // Class
  $class_specified = '';
  if(isset($classes) && $classes != '') $class_specified = '[ '.$classes.' ]';
?>

<header class="[ c-header c-header--js ] {{ $class_specified }}">
	<div class="[ o-wrap o-wrap--full ]">

		@if(!$is_navigation_hidden)
		<nav class="[ navigation ] [ u-mar-t-0x ]">
			<ul>
				<li><a href="{!! route('space') !!}" class="navigation-link work">home</a></li>
        @if(Auth::check())
				<li><a href="/{!! Config::get('space.admin-path-prefix') !!}" class="navigation-link approach">admin</a></li>
        @endif
			</ul>
		</nav>
		@endif

	</div>
</header>
