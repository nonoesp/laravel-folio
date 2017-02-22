
@extends('space::admin.layout')

<?php
	$settings_title = config('space.title');
	if($settings_title == '') {
		$settings_title = "Space";
	}
	$site_title = 'Items | '. $settings_title;
?>

@section('title', 'Items')

	@section('content')

	<div class="admin-list">

	<div class="[ c-admin__existing-tags ] [ u-pad-b-2x ]">
		<ul>
		@foreach($existing_tags as $tag)
			<a href="/{{ config('space.admin-path-prefix')."/items/$tag->slug" }}">
				<li>{{$tag->slug}} · {{$tag->count}}</li>
			</a>
		@endforeach
		<ul>
	</div>

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

		echo Html::link(Space::adminPath().'item/edit/'.$item->id, $item->title, array('class' => 'admin-list-itemLink'));

		if($item->trashed()) {
			echo '<a href="/admin/item/restore/'.$item->id.'"><i class="[ fa fa-toggle-off fa--social ] [ admin-list-optionLink is-invisible ]"></i></a>';
		} else {
			echo '<a href="/admin/item/delete/'.$item->id.'"><i class="[ fa fa-toggle-on fa--social ] [ admin-list-optionLink is-invisible ]"></i></a>';
		}

		if(false) {

					if(count($item->tagNames())) {
						echo " — (";
					$i = 0;
					foreach($item->tagNames() as $tag) {
						if($i > 0) { echo ", "; }
						echo strtolower($tag);
						$i++;
					}
					echo ")";

					}

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
