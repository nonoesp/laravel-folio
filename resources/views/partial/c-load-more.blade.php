<div class="[ c-load-more ] [ js-c-load-more ]">
	@if ($ids)

		<span class="[ c-load-more__item ] [ c-load-more__text-wrapper ]{{--
						 --}}[ js-c-load-more__load-more ]{{--
						 --}}[ u-cursor-pointer u-case-upper ]">
		  {{ trans('space::base.load-more') }}
		</span>

		<span class="[ c-load-more__item ] [ c-load-more__image-wrapper ]">
		  <img class="[ c-load-more__image ]" src="/img/loader.gif">
		</span>

	@endif
</div>
