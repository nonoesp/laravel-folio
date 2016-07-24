
<div class="admin-menu u-case-upper">

	{{-- HTML::link(Writing::adminPath(), 'Dashboard', array('class' => 'admin-menu-item')) --}}

	{{ HTML::link(Writing::adminPath().'articles', 'Articles', array('class' => 'admin-menu-item'))}}

	{{ HTML::link(Writing::adminPath().'article/add', 'Add Article', array('class' => 'admin-menu-item')) }}

	{{ HTML::link('/'.Writing::path(), 'Writing', array('class' => 'admin-menu-item')) }}

	{{ HTML::link('/logout', 'Exit', array('class' => 'admin-menu-item')) }}

</div>