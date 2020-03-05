@extends('folio::admin.layout')

<?php
	$settings_title = config('settings.title');
	if($settings_title == '') {
		$settings_title = "Folio";
	}
    $site_title = 'Versions of Item '.$item->id.' | '. $settings_title;
?>

@section('title', 'Versions of Item '.$item->id)

@section('floating.menu')
  	{!! view('folio::partial.c-floating-menu', ['buttons' => [
		  '<i class="fa fa-chevron-left"></i>' => $item->editPath(),
		  ]]) !!}
@stop

@section('scripts')
<script type="text/javascript" src="{{ mix('/nonoesp/folio/js/manifest.js') }}"></script>
<script type="text/javascript" src="{{ mix('/nonoesp/folio/js/vendor.js') }}"></script>
<script type="text/javascript" src="{{ mix('/nonoesp/folio/js/folio.js') }}"></script>
<script type="text/javascript">
    
    VueResource.Http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
    
    const admin = new Vue({
        el: '.c-admin',
        name: 'Admin',
        data: {
	        item_id: {{ $item->id }}
        },
        methods: {
	        revert: function(version_id) {
                if(confirm("Want to revert?")) {
                    VueResource.Http.post('/api/item/revert', {id: this.item_id, version_id }).then((response) => {
                        // success
                    }, (response) => {
                        // error
                    });                    
                }
	        }
        }
    });
</script>

@endsection

@section('content')

	<div class="[ c-admin ] [ u-pad-b-12x ]">
        
        <p>
            <a href="/admin/item/edit/{{ $item->id }}">Return to Item</a>
        </p>

        @foreach($item->versions->reverse() as $key=>$version)
            <?php
                $date = Item::formatDate($version->updated_at, 'l, F j, Y H:i:s');
                $text_languages = json_decode($version->getModel()->text);
            ?>

            <div class="[ u-pad-t-2x ]">
                <strong>Version {{ $version->version_id }}</strong>
                ·
                <span @click="revert({{ $version->version_id }})" style="cursor:pointer">
                    Revert
                </span>
			</div>

            <div class="[ u-pad-b-1x ] [ c-admin--font-light ] ">
                {{ $date }} · {{ config('app.timezone' )}}
			</div>
            
            <div class="grid">
            @foreach ($text_languages as $lang => $text)
                
                <div class="grid__item one-half">
                    <div class="u-mar-b-1x u-mar-t-1x"
                    style="color:#666;text-transform:uppercase;font-size:0.7rem;
                    font-weight:600;
                    letter-spacing:0.03em">
                        {{ \Symfony\Component\Intl\Locales::getNames()[$lang] }}
                    </div>
                    <textarea>{{ $text }}</textarea>
                </div>

            @endforeach
            </div>

            <br/>
        @endforeach

	</div>

@endsection
