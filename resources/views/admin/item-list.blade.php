@extends('folio::admin.layout')

@php
	$settings_title = config('folio.title');
	if($settings_title == '') {
		$settings_title = "Folio";
	}
	$site_title = 'Items | '. $settings_title;
	$remove_wrap = true;

@endphp

@section('scripts')

    <script type="text/javascript" src="{{ mix('/folio/js/manifest.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/folio/js/vendor.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/folio/js/folio.js') }}"></script>

		<?php
		foreach($items as $item) {
			$item->hidden = false;
			$item->path = $item->path();
			$item->editPath = $item->editPath();
			$item->titleString = $item->title ?? 'Untitled';
		}

		foreach($existing_tags as $tag) {
			$tag->selected = false;
		}
		?>

<script type="text/javascript">
// Get CSRF token for API requests.
const csrfToken = $('meta[name="csrf-token"]').attr('content');

const months = [
	'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december',
	'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'
];

const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

// Helper function for API requests.
async function apiRequest(url, data) {
	try {
		const response = await fetch(url, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': csrfToken
			},
			body: JSON.stringify(data)
		});
		return await response.json();
	} catch (error) {
		console.error('API request failed:', error);
		throw error;
	}
}

// Helper function for sorting objects by count.
function orderBy(obj, key, direction) {
	// Convert object to array if it's not already an array.
	const array = Array.isArray(obj) ? obj : Object.values(obj);
	return array.sort((a, b) => {
		if (direction === 'desc') {
			return b[key] - a[key];
		}
		return a[key] - b[key];
	});
}

const admin = Vue.createApp({
	data() {
		return {
			items: {!! $items !!},
			tags: {!! $existing_tags !!},
			date: '{{Date::now()}}',
			unfiltered: true,
			initialLimit: 0,
			limit: 0,
		};
	},
	watch: {
		tags: {
			handler(newValue) {
				let unfiltered = true;
				for (let i in this.tags) {
					if (this.tags[i].selected) {
						unfiltered = false;
					}
				}
				this.unfiltered = unfiltered;
			},
			deep: true
		},
		items: {
			handler() {
				this.initializeItems();
			},
			deep: true,
			immediate: true
		}
	},
	computed: {
		orderedTags() {
			return orderBy(this.tags, 'count', 'desc');
		},
		filteredItems() {
			// Filter out null/undefined items and ensure all items have hidden property.
			return Object.values(this.items)
				.filter(item => item && typeof item === 'object')
				.map(item => {
					// Ensure hidden property is set.
					if (typeof item.hidden === 'undefined') {
						item.hidden = false;
					}
					return item;
				});
		}
	},
	methods: {
		updateItem(item) {
			console.log('test');
			console.log(item);
			console.log(item.deleted_at);
			// property.is_updating = true;
		},
		async toggleItem(item) {
			const url = item.deleted_at ? '/api/item/restore' : '/api/item/delete';
			
			try {
				const response = await apiRequest(url, { id: item.id });
				item.deleted_at = response.item.deleted_at;
			} catch (error) {
				console.error('Toggle item failed:', error);
			}
		},
		async updateItemWithData(item, update) {
			try {
				const response = await apiRequest('/api/item/update', { id: item.id, update: update });
				console.log('updated item');
				console.log(response.item);
			} catch (error) {
				console.error('Update item failed:', error);
			}
		},
		trashItem(item) {
			console.log('trash');
		},
		humanDate(item) {
			const d = new Date(item.published_at);
			return months[d.getMonth()] + ' ' + (d.getDate()) + ', ' + (d.getFullYear());
		},
		humanDateWithDay(item) {
			const d = new Date(item.published_at);
			return months[d.getMonth()] + ' ' + (d.getDate()) + ', ' + (d.getFullYear()) + ' (' + days[d.getDay()] + ')';
		},
		editHref(item) {
			return '/{{ Folio::adminPath() }}item/edit/' + item.id;
		},
		tagWithSlug(slug) {
			for (let i in this.tags) {
				const tag = this.tags[i];
				if (tag.slug == slug) {
					return tag;
				}
			}
		},
		displayAllTags() {
			for (let i in this.tags) {
				this.tags[i].selected = false;
			}
			for (let i in this.items) {
				if (this.items[i] && typeof this.items[i] === 'object') {
					this.items[i].hidden = false;
				}
			}
		},
		sortTags() {
			const orderedTags = [];
			for (let i in this.tags) {
				orderedTags.push(this.tags[i]);
			}
			orderedTags.sort((a, b) => parseInt(a.count) < parseInt(b.count));
			const orderedTagsObject = {};
			for (let i in orderedTags) {
				orderedTagsObject[i] = orderedTags[i];
			}
			this.tags = orderedTagsObject;
		},
		filterByTag(tag) {
			for (let i in this.tags) {
				this.tags[i].selected = false;
			}
			tag.selected = true;
			for (let i in this.items) {
				const item = this.items[i];
				if (item && typeof item === 'object') {
					const tagsStr = item.tags_str;
					if (tagsStr != null && tagsStr !== '') {
						const tags = item.tags_str.split(',');
						const tagsClean = [];
						for (let j in tags) {
							let tagItem = tags[j];
							let loop = true;
							while (loop) {
								const original = tagItem;
								tagItem = tagItem.trim().replace(" ", "-");
								if (original == tagItem) loop = false;
							}
							tagsClean.push(tagItem);
						}
						item.hidden = !tagsClean.includes(tag.slug);
					} else {
						item.hidden = true;
					}
				}
			}
		},
		initializeItems() {
			// Ensure all items have the hidden property initialized.
			for (let i in this.items) {
				if (this.items[i] && typeof this.items[i] === 'object') {
					if (typeof this.items[i].hidden === 'undefined') {
						this.items[i].hidden = false;
					}
				} else if (this.items[i] === null || this.items[i] === undefined) {
					// Remove null/undefined items from the array.
					delete this.items[i];
				}
			}
		}
	},
	mounted() {
		this.initializeItems();
		this.sortTags();
	}
}).mount('.js--admin');


</script>

@stop

@section('title', 'Items')

	@section('content')

	<style>
	[v-cloak] {
  		display: none;
	}
	</style>

	<div class="[ js--admin c-admin-v2 ] [ admin-list ]">

		{{-- Loading.. --}}

		{{--  <div v-if="false">...</div>		  --}}

		{{-- Tag Cloud --}}

		<div v-cloak class="[ u-visible-vue ] [ c-admin__existing-tags ] [ u-pad-b-2x ]">
			<div v-if="orderedTags.length" class="o-wrap o-wrap--size-650 u-text-align--center">
				<ul>
				<li @click="displayAllTags()"
							v-bind:class="{ 'u-opacity--low': !unfiltered }"
							class="u-cursor-pointer">
						All
				</li>
				<li v-if="limit < 9999"
					class="u-cursor-pointer u-opacity--low"
					@click="limit = 9999">
					<span v-if="limit > 0">Show All Tags</span>
					<span v-if="limit == 0">Show Tags</span>
				</li>					
				<li v-if="limit == 9999"
					class="u-cursor-pointer u-opacity--low"
					@click="limit = initialLimit">
						<span v-if="initialLimit == 0">Hide Tags</span>
						<span v-if="initialLimit > 0">See Less Tags</span>
				</li>
				</ul>
			</div>
			{{-- <div v-if="limit > 0" class="o-wrap o-wrap--size-650 -o-wrap--full u-text-align--left u-mar-t-2x"> --}}
			<div v-if="limit > 0" class="o-wrap o-wrap--full u-text-align--center u-mar-t-2x">
			<ul>
				<li v-for="(tag, index) in orderedTags" v-if="index < limit"
				class="u-cursor-pointer"
				@click="filterByTag(tag)"
				v-bind:class="{ 'u-opacity--low': !tag.selected }">
					@{{ tag.slug }} · @{{ tag.count }}
				</li>
			<ul>
			</div>
		</div>

		{{-- Item List --}}

		<div style="border-top: 1px solid #eaeaea;">
		<div v-cloak v-for="item in filteredItems" class="[ u-visible-vue ] [ admin-list-item ]"
		style="padding:0.6rem 0;margin-bottom:0;border-bottom: 1px solid #eaeaea;" ref="items">
			<div class="o-wrap o-wrap--size-900" style="padding:0;margin-left:auto;margin-right:auto;">
			<div :class="{ 'u-opacity--half': item.deleted_at }">
				<div class="grid">
					<div class="grid__item one-eighth c-admin__item-list-item-tools">
						<div class="m-fa grid">

							{{-- Preview --}}
							<div class="fa-wrap u-cursor-pointer is-invisible">
								<a :href="item.path" target="_blank">
									<i class="[ fa fa-eye fa--social ]"></i>
								</a>
							</div>

							{{-- Edit --}}
							<div class="fa-wrap u-cursor-pointer is-invisible u-hidden-portable">
								<a :href="item.editPath">
									<i class="[ fa fa-pencil fa--social ]"></i>
								</a>
							</div>

							{{-- Hide/Show --}}
							<div class="fa-wrap u-cursor-pointer is-invisible" >
								<i v-if="item.deleted_at" @click="toggleItem(item)"
								class="[ fa fa-toggle-off fa--social ]"></i>
								<i v-if="!item.deleted_at" @click="toggleItem(item)"
								class="[ fa fa-toggle-on fa--social ]"></i>
							</div>							

						</div>
					</div>

					<div class="grid__item six-eighths c-admin__item-list-item-title" style="height:25px">
						<a :href="editHref(item)">
							@{{ item.titleString || 'Untitled' }}
						</a>
					</div>

				</div>
			</div>
			<div class="admin-list-itemDetails" style="margin:0;user-select:none">
				<div class="grid c-admin__item-list-item-date">
					<div class="grid__item one-eighth">

					</div>
					<div class="grid__item six-eighths">
						<p v-if="item.published_at > date" style="font-size:0.65rem;margin:0">
							Scheduled for <span style="text-transform:capitalize">@{{ humanDateWithDay(item) }}</span>
						</p>
						<p v-if="item.published_at <= date" style="font-size:0.65rem;margin:0">
							<span style="text-transform:capitalize">@{{ humanDate(item) }}</span>
						</p>
					</div>
				</div>				
			</div>
			</div>
		</div>
		</div>

	</div>

@endsection
