window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.$ = window.jQuery = require('jquery');
window.jQuery.Lazy = require('jquery-lazy');
window.unveil = require('jquery-unveil');

$(function() {
  // lazy-loading
  $("img.lazy").Lazy({
    afterLoad: function(element) {
      $(element).addClass('lazy--visible').hide();
      const url = $(element).attr('src');
      $(element).siblings('div.lazy').css(
        'background-image',
        `url('${url}')`).addClass('lazy--visible');
    },
    visibleOnly: true    
  });
  // data-src > src
  $("img:not(.lazy)").unveil(500);
});

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = require('vue');
window.VueResource = require('vue-resource');
window.Vue.use(window.VueResource);
window.Focus = require('vue-focus');
import draggable from "vuedraggable";
window.draggable = draggable;
window.count = require('@wordpress/wordcount');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// window.axios = require('axios');
//
// window.axios.defaults.headers.common = {
//     'X-Requested-With': 'XMLHttpRequest'
// };

window.validatejs = require('validate-js');

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });