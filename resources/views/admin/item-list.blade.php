
@extends('writing::admin.layout')

<?php
	$settings_title = Config::get('settings.title');
	if($settings_title == '') {
		$settings_title = "Space";
	}
	$site_title = 'Items — '. $settings_title;
?>

@section('title', 'Items')

	@section('content')

	<div class="admin-list">

	<?php

	foreach($items as $item) {
		$css = 'is-active';
		if ($item->trashed()) {
			$css = 'is-trashed';
		}

		if(!$item->title) {
			$item->title = '(Untitled)';
		}

		echo '<p class="admin-list-item '.$css.'">';

		echo Html::link(Writing::adminPath().'item/edit/'.$item->id, $item->title, array('class' => 'admin-list-itemLink'));

		if($item->trashed()) {
			echo ' '.Html::link(Writing::adminPath().'item/restore/'.$item->id, 'O', array('class' => 'admin-list-optionLink is-invisible'));
		} else {
			echo ' '.Html::link(Writing::adminPath().'item/delete/'.$item->id, 'X', array('class' => 'admin-list-optionLink is-invisible'));
		}

		if(count($item->tagNames())) {
			echo "— (";
		$i = 0;
		foreach($item->tagNames() as $tag) {
			if($i > 0) { echo ", "; }
			echo strtolower($tag);
			$i++;
		}
		echo ")";

		}


		echo "</p>";

		if ($item->published_at > Date::now()) {

			$date = new Date($item->published_at);
			echo '<p class="admin-list-itemDetails">'
				." "
				.ucWords($date->format('F j (l)'))
				."</p>";
		}

	}

	?>

	</div>

@endsection
