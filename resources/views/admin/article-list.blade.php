
@extends('writing::admin.layout')

<?php
	$site_title = 'Articles — '. Config::get('settings.title');
?>

@section('title', 'Articles')

	@section('content')

	<div class="admin-list">

	<?php

	foreach($items as $article) {
		$css = 'is-active';
		if ($article->trashed()) {
			$css = 'is-trashed';
		}

		if(!$article->title) {
			$article->title = '(Untitled)';
		}

		echo '<p class="admin-list-item '.$css.'">';

		echo Html::link(Writing::adminPath().'article/edit/'.$article->id, $article->title, array('class' => 'admin-list-itemLink'));

		if($article->trashed()) {
			echo ' '.Html::link(Writing::adminPath().'article/restore/'.$article->id, 'O', array('class' => 'admin-list-optionLink is-invisible'));
		} else {
			echo ' '.Html::link(Writing::adminPath().'article/delete/'.$article->id, 'X', array('class' => 'admin-list-optionLink is-invisible'));
		}

		if(count($article->tagNames())) {
			echo "— (";
		$i = 0;
		foreach($article->tagNames() as $tag) {
			if($i > 0) { echo ", "; }
			echo strtolower($tag);
			$i++;
		}
		echo ")";

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
