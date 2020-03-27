<div class="[ c-load-more ] [ js-c-load-more ]">
	@isset($ids)

		<span class="
		[ c-load-more__item ] [ c-load-more__text-wrapper ]
		[ js-c-load-more__load-more ]
		[ u-cursor-pointer u-case-upper ]">
		  {{ trans('folio::base.load-more') }}
		</span>

		<span class="[ c-load-more__item ] [ c-load-more__image-wrapper ]">
			<img class="[ c-load-more__image ]" src="{{ Folio::asset('images/loader.gif') }}">
		</span>

	@endisset
</div>
