
<section class="[ c-cover {!! $class or 'c-cover--header' !!} ]"{{--
--}}@if(isset($image)) style="background-image:url('{!! $image !!}')" @endif{{--
--}}@if(isset($background_color)) style="background-color:{{$background_color}}" @endif>

	<div class="c-cover__title">
		<span class="[ c-cover__title-a ]">{!! $title !!}</span>
		<br>
		<span class="[ c-cover__title-b  {!! $classes_title_b or '' !!} ]">{!! $subtitle or '' !!}</span>
	</div>

	<div class="c-cover__description">
		{!! $description !!}
	</div>

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
