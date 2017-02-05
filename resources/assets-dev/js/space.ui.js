

/* Writing.js */

/* Events */

$(document).on('click', '.js-video-thumb', function(){
  $(this).html(videoInsert($(this).attr('data-url'), $(this).attr('data-service')));
});

$(document).on('click', '.js-c-load-more__load-more', function(){
  $(".js-c-load-more").addClass('is-loading');
  loadMore();
});

/* Header.js */

$(document).ready(function()
{
	// Activate navigation-link for current page
	var page = location.pathname.split('/')[1];
	if (!page) {
		page = 'me';
	}
	$(".navigation-link." + "js--navigation-link-"+page).addClass('navigation-link--active');
});

/* Helpers */

function loadMore() {
  if (ids.length > 0) {

    var ids_load = [];
    var step = 3;

    for(i=0;i<step;i++) {
      if (ids[i] == undefined) break;
      ids_load.push(ids[i]);
    }

    // Load artiles and remove ids
    articlesWithIds(ids_load);
    ids.splice(0, step);

    if(ids.length == 0) {
      $(".js-c-load-more__load-more").remove();
    }
  }
}

function articlesWithIds(ids) {

  // To properly set the CSRF-TOKEN with Laravel,
  // make sure you have the following HTML meta tag in your view
  // <meta name="csrf-token" content="{{ csrf_token() }}" />

  $.ajax({
        url: 'http://' + document.domain + '/articles',
        type: 'POST',
        data: {ids: ids, article_type: 'SUMMARY_ARTICLE_TYPE'},
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
             $(".js-c-load-more").before(data);
             $(".js-c-load-more").removeClass('is-loading')
        }
      });
}

function videoInsert(code, service) {

console.log('the service is ' + service);

  if (service == 'youtube') {
    return '<iframe width="100%" height="268" style="max-width:100%"'+
           'src="http://www.youtube.com/embed/'+code+
         '?autoplay=1" frameborder="0" allowfullscreen></iframe>';
  }

  if (service == 'vimeo') {
    return '<iframe src="//player.vimeo.com/video/'+ code +
         '?&portrait=0&title=0&byline=0&color=db4a38&badge=0&autoplay=1"'+
         'width="100%" height="268" frameborder="0" webkitallowfullscreen'+
         ' mozallowfullscreen allowfullscreen></iframe>';
  }
}

function getVimeoThumb(code) {
  $.ajax({
        url: 'http://vimeo.com/api/v2/video/' + code + '.json',
        dataType: 'jsonp',
        success: function(data) {
             console.log(data[0].thumbnail_large);
             $("[data-code="+code+"]").attr('src', data[0].thumbnail_large);
        }
      });
}
