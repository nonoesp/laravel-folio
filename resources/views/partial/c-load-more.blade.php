<div class="[ c-load-more ]  [ js-c-load-more ]">
	@if ($ids)
		<span class="[ c-load-more__item  c-load-more__item--load-more ]  [ js-c-load-more__load-more ]  [ u-cursor-pointer  u-case-upper ]">
		  {{ trans('writing::base.load-more') }}
		</span>
		<span class="[ c-load-more__item  c-load-more__item--loading-wheel ]">
		  <img src="/img/loader.gif">
		</span>
	@endif
</div>