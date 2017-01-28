
<div class="admin-menu u-case-upper u-text-align--center">

	{{-- Html::link('/admin/', 'Dashboard', array('class' => 'admin-menu-item')) --}}

	{{-- Html::link('/admin/items', 'Articles', array('class' => 'admin-menu-item'))--}}

	{{-- Html::link('/admin/item/add', 'Add Article', array('class' => 'admin-menu-item')) --}}

	{{-- Html::link(URL::route('blog'), 'Space', array('class' => 'admin-menu-item')) --}}

	{{-- Html::link('/logout', 'Exit', array('class' => 'admin-menu-item')) --}}

	<div class="[ m-fa  m-fa--black-static ]">

    	<a href="/admin/items">
      		<i class="[ fa fa-align-justify fa--social ]"></i>
    	</a>

      <a href="/admin/item/add">
          <i class="[ fa fa-file-o fa--social ]"></i>
      </a>

      <a href="/subscribers">
          <i class="[ fa fa-envelope-o fa--social ]"></i>
      </a>

    	<a href="/logout">
      		<i class="[ fa fa-close fa--social ]"></i>
    	</a>

  </div>

</div>
