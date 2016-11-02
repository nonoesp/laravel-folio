
<div class="admin-menu u-case-upper">
{{-- --}}
	{{-- Html::link(Writing::adminPath(), 'Dashboard', array('class' => 'admin-menu-item')) --}}

	{!! Html::link(Writing::adminPath().'articles', 'Articles', array('class' => 'admin-menu-item')) !!}

	{!! Html::link(Writing::adminPath().'article/add', 'Add Article', array('class' => 'admin-menu-item')) !!}

	{!! Html::link(URL::route('blog'), 'Writing', array('class' => 'admin-menu-item')) !!}
	
	{!! Html::link('/logout', 'Exit', array('class' => 'admin-menu-item')) !!}

</div>
