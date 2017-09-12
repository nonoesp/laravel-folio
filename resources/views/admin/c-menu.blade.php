<?php
	$admin_path = Folio::adminPath();
	$exit = config('authenticate.exit');
?>

<div class="admin-menu u-case-upper u-text-align--center">

	<div class="[ m-fa  m-fa--black-static ]">

    	<a href="/{{ $admin_path }}items">
      		<i class="[ fa fa-align-justify fa--social ]"></i>
    	</a>

      <a href="/{{ $admin_path }}item/add">
          <i class="[ fa fa-file-o fa--social ]"></i>
      </a>

      <a href="/{{ $admin_path }}upload">
          <i class="[ fa fa-file-image-o fa--social ]"></i>
      </a>	  

      <a href="/{{ $admin_path }}subscribers">
          <i class="[ fa fa-envelope-o fa--social ]"></i>
      </a>

    	<a href="/{{ $exit }}">
      		<i class="[ fa fa-close fa--social ]"></i>
    	</a>

  </div>

</div>
