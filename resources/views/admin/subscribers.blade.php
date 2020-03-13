
@extends('folio::admin.layout')

<?php
$settings_title = config('folio.title');
if($settings_title == '') {
	$settings_title = "Folio";
}
	$site_title = 'Subscribers | '. $settings_title;
?>

@section('title', 'Subscribers')

@section('scripts')

	<script type="text/javascript" src="{{ mix('/nonoesp/folio/js/manifest.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/nonoesp/folio/js/vendor.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/nonoesp/folio/js/folio.js') }}"></script>

	<script type="text/javascript">
		
		VueResource.Http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');		
	
		var admin = new Vue({
			el: '.c-admin',
			data: {
				name: 'subscribers'
			},
			methods: {
				hide: function(id) {
					VueResource.Http.post('/subscriber/delete', {id: id}).then((response) => {
							// success
							location.href = location.href;
					}, (response) => {
							// error
					});

				}
			}
		});

	</script>

@stop

@section('content')

<style media="screen">
	.grid {
		letter-spacing: inherit;
	}
	.o-hide-button {
		cursor:pointer;
	}
</style>

<div class="[ c-admin ]">

  @if(count($subscribers))

    @if(count($subscribers) == 1)
      <p>There is a subscriber.</p>
    @else
      <p>There are {{ count($subscribers) }} subscribers.</p>
    @endif

		<ul class="c-archive__list">
			@foreach($subscribers as $subscriber)

					<?php
					$date = new Date($subscriber->created_at);
					$date = ucWords(substr($date->format('l'), 0, 3)
							 .'&nbsp;'
							 .$date->format('j')
							 .',&nbsp;'
							 .substr($date->format('F'), 0, 3)
							 .'&nbsp;'
							 .$date->format('Y'));
					?>

					<li>
						<a href="mailto:{{ $subscriber->email }}" target="_blank">

							<b class="c-archive__list__title">{{ strtolower($subscriber->email) }}</b>

							<em class="c-archive__list__date u-font-size--a">

								{!! $date !!}

							</em>

						</a>

						<p class="u-font-size--a u-opacity--low -u-hidden-palm -u-text-align--right" style="margin-top:-0.8em">
						
									<?php
										$data = [];				
										if($path = $subscriber->path) {
											array_push($data, $path);
										}
										if($host = $subscriber->host) {
											array_push($data, $host);
										}																
										if($source = $subscriber->source) {
											array_push($data, $source);
										}
										if($medium = $subscriber->medium) {
											array_push($data, $medium);
										}
										if($campaign = $subscriber->campaign) {
											array_push($data, $campaign);
										}
										if($newsletter_list = $subscriber->newsletter_list) {
											$lists = join(" · ", explode(",", str_replace(' ', '', $newsletter_list)));
											array_push($data, "<strong>$lists</strong>");
										}
										if($ip = $subscriber->ip) {
											array_push($data, $ip);
										}
									?>
									
									@if(count($data))
										{!! join(" · ", $data) !!}
										·
									@endif

									<span class="o-hide-button" onclick="admin.hide({{ $subscriber->id }})">hide</span>
						</p>

					</li>


			@endforeach
		</ul>

  @else
    <p>
      There are no subscribers yet.
    </p>
  @endif

</div>

@endsection
