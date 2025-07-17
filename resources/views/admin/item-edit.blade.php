@extends('folio::admin.layout')

<?php
$settings_title = config('folio.title');
if($settings_title == '') {
	$settings_title = "Folio";
}
$site_title = 'Editing Item '.$item->id.' · '.$item->title.' | '. $settings_title;
$remove_wrap = true;

$translations = config('folio.translations');
if(!$translations) $translations = ['en'];

foreach($translations as $translation) {
	if (!\Symfony\Component\Intl\Locales::exists($translation)) {
		$language_errors = [
'**'.$translation.'** is not a valid locale.
Provide valid `translations` in `config/folio.php`.

For instance, you can specify English and Spanish.

```php
\'translations\' => [\'en\', \'es\'],
```

Or you can just leave it empty (defaults to `en`).

```php
\'translations\' => [],
```
'
		];
	}
}

?>

@section('title', 'Editing Item '.$item->id)

@section('scripts')

	<script type="text/javascript" src="{{ mix('/folio/js/manifest.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/folio/js/vendor.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/folio/js/folio.js') }}"></script>
	<!-- Mousetrap for handling keyboard shortcuts -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.6.1/mousetrap.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.6.1/plugins/global-bind/mousetrap-global-bind.min.js"></script>
	<!-- Clipboard -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.0/clipboard.min.js"></script>
	<!-- Axios -->
	<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
	<!-- Slider CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@vueform/slider/themes/default.css">

<script type="text/javascript">

/*
 * Prevent the user from walking away without saving changes.
 */
window.onbeforeunload = function() {
	if(isSaving) {
		return null;
	}
    return admin.isDirty() ? "If you leave this page you will lose your unsaved changes." : null;
}

/*
 * Wether the user requested to save.
 * (User to not warn the user the file has unsaved changes.)
 */
var isSaving = false;

/*
 * CTRL+S & COMMAND+S
 * Keyboard shortcut to save edits by submitting the form.
 */
Mousetrap.bindGlobal(['ctrl+s', 'command+s'], function(e) {
	admin.save();
	e.preventDefault();
	return false;
});

/*
 * esc (escape key)
 * Keyboard shortcut to escape text editing mode.
 */
Mousetrap.bindGlobal('esc', function(e) {
    e.preventDefault();
	admin.exitFullscreen();
	
	// Reset height of property textareas
	$('.c-admin__property-textarea').css('height', '36px');
	$('textarea:focus').blur();
    
	return false;
});

/*
 * command+i
 * Keyboard shortcut to log if the Item is "dirty."
 */
Mousetrap.bindGlobal('command+i', function(e) {
	console.log('admin.isDirty(): ' + admin.isDirty());
	e.preventDefault();
	return false;
});

/*
 * alt+f
 * Keyboard shortcut to edit full screen.
 */
Mousetrap.bindGlobal('alt+f', function(e) {
    e.preventDefault();
    enterFullscreen();
	return false;
});

var save = function(e) {
	if(e) {
		e.preventDefault();
	}
	if(admin.isDirty()) {
		admin.saveItemChanges();
	}
	return;
}

$(document).on('submit', 'form', function() {
	isSaving = true;
});

// Set CSRF token for axios
axios.defaults.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

var debounced_property_sync = _.debounce(
	function(property, model) {
		if(property.value != property.old_value ||
		property.label != property.old_label ||
		property.name != property.old_name) {
			// update
		} else {
			// no changes, abort update
			return;
		}

		var data = property;
		property.is_updating = true;

		property.old_value = property.value;
		property.old_label = property.label;
		property.old_name = property.name;

		// model.$forceUpdate();
		axios.post('/api/property/update', data).then((response) => {
				// success
				property.is_updating = false;
				// model.$forceUpdate();
			}, (response) => {
				// error
			});
	}, 500);

var editing_property = -1;

const { createApp, ref, computed, watch, onMounted } = Vue;
const draggable = window.draggable;

const admin = createApp({
    components: {
        draggable
    },
    setup() {
        const item = ref({!! $item !!});
        const originalItem = ref({!! $item !!});
        const properties = ref({!! $item->properties->sortBy('order_column')->values() !!});
        const message = ref({!! json_encode($item->title) !!});
        const isTextareaFocused = ref(false);
        const isTextareaExpanded = ref(false);
        const isFullscreen = ref(false);
        const properties_changed = ref(false);
        const timers = ref({});

        // Function to refresh CSRF token
        const refreshCsrfToken = () => {
            axios.get('/csrf-token')
                .then(response => {
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = response.data.token;
                })
                .catch(error => {
                    console.error('Error refreshing CSRF token:', error);
                });
        };

        // Initialize properties
        const initProperties = () => {
            for(var i in properties.value) {
                var p = properties.value[i];
                p.old_name = p.name;
                p.old_label = p.label;
                p.old_value = p.value;
            }
        };

        // Watch properties for changes
        watch(properties, (value, old) => {
            if (old != "") {
                properties_changed.value = true;
            }
        }, { deep: true });

        // Methods
        const save = (e) => {
            if(e) {
                e.preventDefault();
            }
            if(isDirty()) {
                saveItemChanges();
            }
            return;
        };

        const exitFullscreen = () => {
            isFullscreen.value = false;
            const textareas = $('.o-textarea__text');
            $('.o-textarea').removeClass('o-textarea--fullscreen');
            $(textareas).each((index, element) => {
                element.blur();
                adjustTextareaHeight(element, 150);
            });
        };

        const enterFullscreen = (e) => {
            isFullscreen.value = true;
            $('.o-textarea').removeClass('o-textarea--fullscreen');
            
            if (e != undefined) {
                const oTextarea = $(e.target).closest('.js--o-textarea');
                $(oTextarea).addClass('o-textarea--fullscreen');
                const textarea = $('.o-textarea--fullscreen textarea').get();
                setTimeout(() => {
                    $(textarea).css('height', '-webkit-fill-available');
                }, 1);
            }
        };

        const wordCountFromText = (t) => {
            t = t.replace(/(^\s*)|(\s*$)/gi,"");
            t = t.replace(/[ ]{2,}/gi," ");
            t = t.replace(/\n /,"\n");
            return t.split(' ').length;
        };

        const wordCount = () => {
            const textarea = $("textarea:focus");
            if (typeof textarea !== 'undefined') {
                const text = textarea.val();
                if (typeof text !== 'undefined') {
                    if (text === '') {
                        return 0;
                    }
                    return count.count(text, 'words', {});
                }
            }
            return 0;
        };

        const sync_properties = (property) => {
            if(editing_property != -1 && property.id != editing_property) {
                debounced_property_sync.flush();
            }
            editing_property = property.id;
            
            refreshCsrfToken()
            debounced_property_sync(property, this);
            properties_changed.value = false;
        };

        const movePropertyUp = (property) => {
            const index = properties.value.indexOf(property);
            if (index > 0) {
                const newOrder = [...properties.value];
                [newOrder[index], newOrder[index - 1]] = [newOrder[index - 1], newOrder[index]];
                properties.value = newOrder;
            }
        };

        const movePropertyDown = (property) => {
            const index = properties.value.indexOf(property);
            if (index < properties.value.length - 1) {
                const newOrder = [...properties.value];
                [newOrder[index], newOrder[index + 1]] = [newOrder[index + 1], newOrder[index]];
                properties.value = newOrder;
            }
        };

        const delete_property = (property) => {
            if(confirm("Are you sure you want to delete this property? This cannot be undone.")) {
                axios.post('/api/property/delete', {id: property.id}).then((response) => {
                    // success
                    const index = properties.value.indexOf(property);
                    properties.value.splice(index, 1);
                }, (response) => {
                    // error
                });
            }
        };

        const add_property_at = (property, index, delta) => {
            const data = { item_id: item.value.id }
            axios.post('/api/property/create', data).then((response) => {
                // success
                const newProperty = {id: response.data.property_id, order_column: response.data.order_column};
                properties.value.splice(index + delta, 0, newProperty);
            }, (response) => {
                // error
            });
        };

        const add_property = () => {
            const data = { item_id: item.value.id }
            axios.post('/api/property/create', data).then((response) => {
                // success
                const property = {id: response.data.property_id};
                properties.value.push(property);
            }, (response) => {
                // error
            });
        };

        const textareaKeyupHandler = (event) => {
            let target = targetElement(event);
            adjustTextareaHeight(target, 0);
        };

        const adjustTextareaHeight = (textarea, height) => {
            if ($('.o-textarea--fullscreen').length) {
                textarea.style.height = '-webkit-fill-available';
            } else if(height == 0) {
                isTextareaExpanded.value = true;
                var scrollTop = document.documentElement.scrollTop;
                textarea.style.height = "1px";
                textarea.style.height = (25+textarea.scrollHeight)+"px";
                document.documentElement.scrollTop = scrollTop;
            } else {
                isTextareaExpanded.value = false;
                textarea.style.height = height+'px';
            }
        };

        const updateTextareaFocus = (event) => {
            let target = targetElement(event);

            switch(event.type) {
                case 'focus':
                    isTextareaFocused.value = true;
                    adjustTextareaHeight(target, 0);
                break;
                case 'blur':
                    isTextareaFocused.value = false;
                break;
            }
        };

        const targetElement = (event) => {
            if(event.srcElement == undefined) {
                return event.target;
            }
            return event.srcElement;
        };

        const saveItemChanges = () => {
            let itemId = item.value.id;
            let itemData = {
                title: item.value.title,
                text: item.value.text,
                video: item.value.video,
                published_at: item.value.published_at,
                image: item.value.image,
                image_src: item.value.image_src,
                link: item.value.link,
                slug_title: item.value.slug_title,
                tags_str: item.value.tags_str,
                recipients_str: item.value.recipients_str,
                template: item.value.template,
                deleted_at: item.value.deleted_at,
                rss: item.value.rss,
                is_blog: item.value.is_blog,
            };

            $(".js--save").html('Saving..');
            setFloatingMenuState('Saving..');

            axios.post('/item/update/' + itemId, itemData)
                .then((response) => {
                    // Deep clone the current item state to originalItem
                    originalItem.value = JSON.parse(JSON.stringify(item.value));
                    
                    $(".js--item-path").attr('href', response.data.path);
                    $(".js--save").html('Save');
                    setFloatingMenuState();
                })
                .catch((error) => {
                    console.error('Error saving item:', error);
                    $(".js--save").html('Save');
                    setFloatingMenuState('Error saving changes');
                });
        };

        const setFloatingMenuState = (state) => {
            if (state) {
                $(".js--floating-menu__buttons").hide();
                $(".js--floating-menu__status").show().html(state);
            } else {
                $(".js--floating-menu__buttons").show();
                $(".js--floating-menu__status").hide();
            }
        };

        const isDirty = () => {
            var trackedFields = [
                'title',
                'text',
                'video',
                'published_at',
                'image',
                'image_src',
                'link',
                'slug_title',
                'tags_str',
                'recipients_str',
                'template',
                'deleted_at',
                'rss',
                'is_blog',
            ];

            for(var i in item.value) {
                if(trackedFields.includes(i)) {
                    let itemValue = item.value[i];
                    let originalValue = originalItem.value[i];

                    if((itemValue == "" || itemValue == "null") && originalValue == null) {
                        // ignore this as is not really "dirty"
                    } else if(
                        itemValue != originalValue &&
                        JSON.stringify(itemValue) != JSON.stringify(originalValue)) {
                        return true;
                    }
                }
            }
            return false;
        };

        const sortProperties = () => {
            let property_ids = JSON.parse(JSON.stringify(properties.value));
            property_ids = property_ids.map((element, index) => { return element.id; });

            axios.post('/api/property/sort', {ids: property_ids})
            .then((response) => {
                // success
            }, (response) => {
                // error
            });
        };

        const textareaAutoresize = (e) => {
            e.target.style.cssText = 'height:36px;';
            e.target.style.cssText = `height: ${e.target.scrollHeight}px`;
        };

        const textareaCollapse = (e) => {
            e.target.style.cssText = 'height:36px;';
        };

        onMounted(() => {
            initProperties();
            // parse deleted_at date to true if existing for isDirty to detect property
            if(item.value.deleted_at != null) item.value.deleted_at = true;
            if(originalItem.value.deleted_at != null) originalItem.value.deleted_at = true;
            new ClipboardJS('.js--encoded-path');
            $(document).on('click', '.js--encoded-path', function(e) {
                e.preventDefault();
                return false;
            });

            // Set up periodic CSRF token refresh
            // Refresh every 30 minutes (1800000 ms)
            setInterval(refreshCsrfToken, 1800000);
            // Also refresh immediately on mount
            // refreshCsrfToken();
        });

        return {
            item,
            originalItem,
            properties,
            message,
            isTextareaFocused,
            isTextareaExpanded,
            isFullscreen,
            properties_changed,
            timers,
            save,
            exitFullscreen,
            enterFullscreen,
            wordCountFromText,
            wordCount,
            sync_properties,
            movePropertyUp,
            movePropertyDown,
            delete_property,
            add_property_at,
            add_property,
            textareaKeyupHandler,
            adjustTextareaHeight,
            updateTextareaFocus,
            targetElement,
            saveItemChanges,
            setFloatingMenuState,
            isDirty,
            sortProperties,
            textareaAutoresize,
            textareaCollapse
        };
    }
}).mount('.c-admin');

</script>

@stop

<?php
	$inputs = [
		['name' => 'published_at', 'placeholder' => 'Publication Date (yyyy-mm-dd hh:mm:ss)', 'label' => 'Publication Date'],
		['name' => 'image', 'placeholder' => 'Image', 'label' => 'Image'],
		['name' => 'image_src', 'placeholder' => 'Social Preview Image', 'label' => 'Social Preview Image'],
		['name' => 'video', 'placeholder' => 'Video', 'label' => 'Video URL'],
		['name' => 'link', 'placeholder' => 'External Link', 'label' => 'External Link'],
		['name' => 'slug_title', 'placeholder' => 'Explicit URL slug (prefix with / to make absolute, i.e. \'/terms\')', 'label' => 'URL slug'],
		['name' => 'tags_str', 'placeholder' => 'Tags (e.g. writing, project)', 'label' => 'Tags'],
	];
?>

@section('floating.menu')
  	{!! view('folio::partial.c-floating-menu', ['buttons' => [
		  '<i class="fa fa-home"></i>' => '/'.Folio::path(),
		  '<i class="fa fa-times"></i>' => $item->destroyPath(),
		  '<i class="fa fa-history"></i>' => $item->versionsPath(),
		  '<i class="fa fa-link"></i>' => [
			  '//'.$item->encodedPath(true),
			  'js--encoded-path',
			  'data-clipboard-text="'.$item->encodedPath(true).'"'],
		  '<i class="fa fa-eye"></i>' => [
			  $item->path(),
			  'js--item-path',
			  '']
		  ]]) !!}
@stop

@section('content')

@isset($language_errors)

	<div class="[ c-admin-form-v2 ] [ grid ]">
	<div class="[ o-wrap o-wrap--size-small ]">
		<div class="[ grid__item ] [ one-whole ]">
			@foreach($language_errors as $error)
			<p><strong>Configuration error</strong></p>
			<p>{!! Item::convertToHtml($error) !!}</p>
			@endforeach
		</div>
	</div>
	</div>

@else

@push('metadata')
<link href="https://fonts.googleapis.com/css?family=Cousine:400,700" rel="stylesheet">
@endpush

{{-- Vue Component --}}

<div v-cloak class="[ c-admin ] [ u-pad-b-12x ]">

	<div class="[ c-admin-form-v2 ] [ grid ]">

		@if( Request::isMethod('post') )
		<div class="[ o-wrap o-wrap--size-small ]">
			<div class="[ grid__item ] [ one-whole ]">
				<p>Changes saved.</p>
			</div>
		</div>
		@endif

		{{ Form::model($item, ['route' => ['item.edit', $item->id]]) }}

        @foreach($translations as $translation)
            <div class="o-textarea js--o-textarea">

			@php
				$language_label = "";
				if(count($translations) > 1) {
					$language_label = " · $translation";
				}
			@endphp
			<div class="[ o-wrap o-wrap--size-750 ] ">

				<div v-if="!isFullscreen" class="o-fullscreen-button__wrap" @click="enterFullscreen">
					<i class="fa fa-expand o-fullscreen-button__icon"></i>
				</div>

				<div v-if="isFullscreen" class="o-fullscreen-button__wrap" @click="exitFullscreen">
					<i class="fa fa-compress o-fullscreen-button__icon"></i>
				</div>

				<div class="[ grid__item ] [ one-whole ] o-textarea__title">
					<p>{{ Form::text('title', null, [
							'placeholder' => 'Title'.$language_label,
							'v-model' => 'item.title.'.$translation,
							'class' => 'o-textarea__title-input js--o-textarea__title-input center',
                        ]) }}
                    </p>
				</div>
			</div>

			<div class="[ grid__item ] [ one-whole ] o-textarea__textarea">
			<p>{{ Form::textarea('text', null, [
				'placeholder' => 'Text'.$language_label,
				'ref' => 'editor',
				'v-on:keyup' => 'textareaKeyupHandler',
				'v-model' => 'item.text.'.$translation,
				'@focus' => 'updateTextareaFocus',
				'@blur' => 'updateTextareaFocus',
				'class' => 'o-textarea__text',
				'v-bind:class' => '{ "u-opacity--high" : !isTextareaFocused }'
                ]) }}
            </p>
        </div>
     </div>
        
		@endforeach

		<div v-if="!isFullscreen" class="[ o-wrap o-wrap--size-600 ]" style="position:relative">

			@foreach($inputs as $input)
				<div v-if="item.{{ $input['name'] }}" class="[ grid__item ]
				[ c-admin-form__label u-text-align--right ]
				[ one-half portable--one-whole ]">
					<span>{{ $input['label'] }}</span>
				</div><!--
		 --><div class="[ grid__item ] [ one-whole ]">
		 			<p>{{ Form::text($input['name'], null, ['v-model' => 'item.'.$input['name'], 'placeholder' => $input['placeholder']]) }}</p>
			</div>
			@endforeach

			{{-- Template Drop-down --}}

			<div class="[ grid__item ]
				[ c-admin-form__label u-text-align--right ]
				[ one-half portable--one-whole ]">
					<span>Template</span>
			</div>
			<div class="[ grid__item ] [ one-whole ]">
				<p>
					{{ Form::select('template', $templates, $item->template, ['v-model' => 'item.template']) }}
				</p>
				@if($item->templateView() != null && !view()->exists($item->templateView()))
				<p class="u-pad-b-1x">View <i>{{$item->templateView()}}</i> is missing!</p>
				@endif
			</div>

			<div class="[ grid__item ] [ one-whole ]">
				<p>
					{{ Form::checkbox('is_hidden', null, $item->trashed(), ['id' => 'is_hidden', 'v-model' => 'item.deleted_at']) }}
					<label for="is_hidden">Hidden</label>
				</p>
			</div>

			{{-- Blog Feed --}}

			<div class="[ grid__item ] [ one-whole ]">
				<p>
					{{ Form::checkbox('is_blog', null, null, ['id' => 'is_blog', 'v-model' => 'item.is_blog']) }}
					<label for="is_blog">Blog Feed</label></p>
			</div>

			{{-- RSS --}}

			<div class="[ grid__item ] [ one-whole ]">
				<p>
					{{ Form::checkbox('rss', null, null, ['id' => 'rss', 'v-model' => 'item.rss']) }}
					<label for="rss">RSS Feed</label>
				</p>
			</div>

			{{-- Properties --}}

				<br/>
				<br/>
				<div v-if="properties.length" class="[ grid__item ] [ u-pad-b-1x ]">
					<strong>Properties</strong>
				</div>
				<div v-if="!!!properties.length" class="[ grid__item ] [ u-pad-b-1x ]">
					<p @click="add_property" class="c-admin__property-add">Add Properties</p>
				</div>

				<draggable 
					v-model="properties"
					:animation="150"
					handle=".js--dragger"
					@end="sortProperties"
					item-key="id"
					class="[ grid__item ] [ one-whole ]"
				>
					<template #item="{ element: property, index }">
						<div class="[ grid__item one-whole ] [ c-admin__property ]"
						v-bind:class="{'is-title': !!property.value && !property.name && !property.label}">

							<div class="[ grid grid--narrow ]">
								<div class="[ grid__item ]
								[ c-admin-form__label u-text-align--right c-admin--font-light ]
								[ one-half portable--one-whole ]
								[ u-hidden-portable ]">
								</div>
								<!--
							--><div class="[ grid__item two-twelfths  ] [ u-text-align--right ]" style="position:relative;">
								
									<span v-bind:data-id="property.id"
									class="[ c-admin__property-icon c-admin__property-icon--drag ]
									[ js--dragger ]">
										<i class="fa fa-bars"></i>
									</span>	
									<span v-bind:data-id="property.id"
									@click="add_property_at(property,index,0)"
									class="[ c-admin__property-icon c-admin__property-icon--add-before ]">
										+
									</span>	
									
									<input type="text"
									placeholder="Label"
									v-model="property.label"
									@keyup="sync_properties(property)"
									v-bind:data-id="property.id" data-field="label"
									v-bind:class="{
										'is-updating': property.is_updating, 
										'is-empty': !property.label || !!property.name && property.name[0] === '-',
									}"
									class="u-text-align--right">
								</div><!--
								--><div class="[ grid__item three-twelfths ]">
									<input type="text"
									placeholder="Name"
									v-model="property.name"
									@keyup="sync_properties(property)"
									v-bind:data-id="property.id" data-field="name"
									v-bind:class="{
										'is-updating': property.is_updating,
										'is-empty': !property.name || !!property.name && property.name[0] === '-',
									}"
									class="u-text-align--right">
								</div><!--
								--><div class="[ grid__item six-twelfths ]">
										<textarea
											v-model="property.value"
											class="c-admin__property-textarea"
											v-bind:class="{
												'is-updating': property.is_updating,
												'is-empty': (!property.value && !property.name) || !!property.name && property.name[0] === '-',
												'is-title': !property.name && !property.label && !!property.value
											}"
											placeholder="Value"
											@keyup="sync_properties(property)"
											@input="textareaAutoresize"
											@focus="textareaAutoresize"
										></textarea>
								</div><!--
								--><div
								class="[ grid__item one-twelfth ]">
									<div @click="delete_property(property)" v-bind:data-id="property.id"
									class="[ c-admin__property-icon ]">
										<i class="fa fa-trash-o"></i>
									</div>							
									<span @click="add_property_at(property,index,1)" v-bind:data-id="property.id"
									class="[ c-admin__property-icon ]">
										+
									</span>
								</div><!--
					--></div>

						</div>
					</template>
				</draggable>

		{{ Form::close() }}

	</div>

	<div style="background-color:transparent;position:fixed;bottom:0;right:0;left:0;height:55px;z-index:200">

		{{-- Save --}}
		
		<div v-if="isDirty()"
		style="background-color:transparent;position:absolute;bottom:0;right:0;width:10rem;padding-top:10px;padding-left:40px;padding-right:10px">
			<p>{{ Form::button('Save', [
				'v-on:click' => 'save()',
				'v-bind:disabled'=>"!isDirty()",
				'class' => 'js--save',
				'style' => 'margin:0;background-color:white;'
				]) }}</p>
		</div>

		{{-- Unsaved changes --}}
		
		<div class="o-label-saved">
			<p>
				<span v-if="!!wordCount()">@{{ wordCount() }} words</br></span>
				<span v-if="!!isDirty()" v-bind:class="{'u-opacity--half': isFullscreen}">Unsaved changes.</span>
				<span v-if="!isDirty()" v-bind:class="{'u-opacity--half': isFullscreen}">Saved.</span>
			</p>
		</div>     

	</div>

</div>

@endisset

@endsection
