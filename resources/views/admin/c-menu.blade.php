
<div class="admin-menu u-case-upper">
{{-- --}}
	{{-- Html::link('/admin/', 'Dashboard', array('class' => 'admin-menu-item')) --}}

	{{ Html::link('/admin/articles', 'Articles', array('class' => 'admin-menu-item')) }}

	{{ Html::link('/admin/article/add', 'Add Article', array('class' => 'admin-menu-item')) }}

	{{ Html::link(URL::route('blog'), 'Writing', array('class' => 'admin-menu-item')) }}

	{{ Html::link('/logout', 'Exit', array('class' => 'admin-menu-item')) }}

</div>