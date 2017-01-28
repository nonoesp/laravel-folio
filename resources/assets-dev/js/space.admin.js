Vue.http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

var app = new Vue({
  el: '#app',
  data: {
    message: 'nono.ma',
    item: '',
    properties: '',
    timers: {},
    styleObject: {
      color: 'red'
    }
  },
  computed: {
    reversedMessage: function () {
      return this.message.split('').reverse().join('');
    }
  },
  methods: {
    set_updating: function(property) {
      property.is_updating = true;
    },
    sync_properties: _.debounce(
      function(property) {
        console.log('sync_properties');
        var data = property;
        property.is_updating = true;
        this.$forceUpdate();
        this.$http.post('/api/property/update', data).then((response) => {
            // success
            property.is_updating = false;
            this.$forceUpdate();
          }, (response) => {
            // error
          });
    }, 500),
    delete_property: function(property) {
      this.$http.post('/api/property/delete', {id: property.id}).then((response) => {
          // success
          this.properties.splice(this.properties.indexOf(property), 1);
        }, (response) => {
          // error
        });
    },
    add_property: function(event) {
      var data = { item_id: this.item.id }
      this.$http.post('/api/property/create', data).then((response) => {
          // success
          this.properties.push({id: response.body.property_id});
        }, (response) => {
          // error
        });
    }
  }
});
