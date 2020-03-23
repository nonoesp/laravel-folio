<?php
	$admin_path = Folio::adminPath();
?>

<div class="admin-menu u-case-upper u-text-align--center">

	<div class="[ m-fa  m-fa--black-static ]">

    	<a href="/{{ Folio::adminPath('items') }}">
      		<i class="[ fa fa-align-justify fa--social ]"></i>
    	</a>

      <a href="/{{ Folio::adminPath('item/add') }}">
          <i class="[ fa fa-file-o fa--social ]"></i>
      </a>

      <a href="/{{ Folio::adminPath('upload') }}">
          <i class="[ fa fa-file-image-o fa--social ]"></i>
      </a>	  

      <a href="/{{ Folio::adminPath('subscribers') }}">
          <i class="[ fa fa-envelope-o fa--social ]"></i>
      </a>

      <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
      		<i class="[ fa fa-close fa--social ]"></i>
    	</a>

      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>      

  </div>

</div>
