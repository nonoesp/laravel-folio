
@extends('writing::admin.layout')

<?php
	$site_title = 'Articles — '. Config::get('settings.title');
?>

@section('title', 'Articles')

	@section('content')
	
	<div class="admin-list">

	<?php

	foreach($articles as $article) {
		$css = 'is-active';
		if ($article->trashed()) {
			$css = 'is-trashed';
		}

		if(!$article->title) {
			$article->title = '(Untitled)';
		}

		echo '<p class="admin-list-item '.$css.'">';
			
		echo HTML::link('/admin/article/edit/'.$article->id, $article->title, array('class' => 'admin-list-itemLink'));
		
		if($article->trashed()) {
			echo ' '.HTML::link('/admin/article/restore/'.$article->id, 'O', array('class' => 'admin-list-optionLink is-invisible'));
		} else {
			echo ' '.HTML::link('/admin/article/delete/'.$article->id, 'X', array('class' => 'admin-list-optionLink is-invisible'));
		}

		echo "</p>";

		if ($article->published_at > Date::now()) {

			$date = new Date($article->published_at);
			echo '<p class="admin-list-itemDetails">'			
				." "
				.ucWords($date->format('F j (l)'))
				."</p>";					
		}

	}

	?>

	</div>

@endsection