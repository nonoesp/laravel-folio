
<div class="admin-menu u-case-upper">

	{{-- HTML::link('/admin/', 'Dashboard', array('class' => 'admin-menu-item')) --}}

	{{ HTML::link('/admin/articles', 'Articles', array('class' => 'admin-menu-item'))}}

	{{ HTML::link('/admin/article/add', 'Add Article', array('class' => 'admin-menu-item')) }}

	{{ HTML::link('/'.Writing::path(), 'Writing', array('class' => 'admin-menu-item')) }}

	{{ HTML::link('/logout', 'Exit', array('class' => 'admin-menu-item')) }}

</div>