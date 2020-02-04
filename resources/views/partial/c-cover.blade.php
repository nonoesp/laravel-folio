
<section class="[ c-cover {!! $class ?? 'c-cover--header' !!} {{--
--}} @if($show_arrow) js--scroll-over @endif{{--
--}} ]">

	<div class="[ c-cover__image {{--
--}} @if(isset($isLazy)) lazy @endif{{--
--}} ]"
--}} @if(isset($image) && isset($isLazy)) data-src="{{ $image }}" {{--
--}} @elseif(isset($image)) style="background-image:url('{!! $image !!}')" {{--
--}} @elseif(isset($background_color)) style="background-color:{{$background_color}}" @endif></div>

	<div class="c-cover__fade"></div>

	<div class="c-cover__title">
		<span class="[ c-cover__title-a ]">{!! $title !!}</span>
		<br>
		<span class="[ c-cover__title-b  {!! $classes_title_b ?? '' !!} ]">{!! $subtitle ?? '' !!}</span>
	</div>

	<div class="c-cover__description">
		{!! $description ?? '' !!}
	</div>

	@if($show_arrow)
	<div class="[ c-cover__arrow-down ] [ js--arrow-down u-cursor-pointer ]">
		<div class="[ u-text-align--center ]">
			<svg class="[ o-icon o-icon--arrow-down ] [ js-arrow-down ]" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 54.59 29.43"><path d="M89.9,29.2"/><polygon points="27.3 28.3 0.76 2.19 2.16 0.76 27.3 25.5 52.43 0.76 53.84 2.19 27.3 28.3"/></svg>
		</div>
	</div>
	@endif

	@if(isset($slideshow))
		<div class="[ c-cover__slide c-cover__slide-back c-cover__slide-back--js ]">
		</div>

		<div class="[ c-cover__slide c-cover__slide-front c-cover__slide-front--js ]">
		</div>
	@endif


	@if(isset($video))

    <video class="c-cover__video" src="{!! $video !!}" preload="auto" autoplay loop muted>
			<source src="{!! $video !!}" type="video/mp4"/>
			Your browser does not support HTML5 video.
    </video>

	@endif

	<div class="[ c-cover__slide c-cover__veil c-cover__veil--js ]" @if(isset($veil_opacity))style="opacity:{!! $veil_opacity !!}"@endif>
	</div>

</section>
