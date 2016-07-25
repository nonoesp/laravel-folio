

<?php
	/**
	*	c-user-picture
	*
	*	param: size (defines width and height)
	*   param: shouldLink (defines if the thumbnail should link to user profile)
	*	@return profile picture
	*
	**/

	if(!isset($shouldLink)) {
		$shouldLink = true;	
	}
?>

@if($shouldLink)
  <a href="http://google.com" class="u-link--block">
@endif

	<div class="c-user-picture" style="
		 @if($user->image) background-image: url('{{ $user->image }}'); @endif
		 @if(isset($size)) width:{{ $size }}px;height:{{ $size }}px; @endif
		 @if(isset($margin_top)) margin-top:{{ $margin_top }}px; @endif
		 @if(isset($margin_bottom)) margin-bottom:{{ $margin_bottom }}px; @endif
		 ">

	</div>

@if($shouldLink)
  </a>
@endif