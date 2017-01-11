Vue.http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

var app = new Vue({
  el: '#app',
  data: {
    message: 'Hello Vue!',
    variable: 'test',
    item: '',
    properties: '',
    styleObject: {
      color: 'red'
    }
  },
  methods: {
    update: function(event) {
      //var id = event.target.getAttribute("data-id");
      //var value = event.target.value;
      //this.variable = event.target.getAttribute("data-id") + " " + event.target.value;
    },
    sync_properties: function(property) {
      var data = property;
      this.$http.post('/api/property/update', data).then((response) => {
          // success callback
          // console.log('ajax - success');
          console.log(response);
        }, (response) => {
          // error callback
          // console.log('ajax - error');
        });
    },
    delete_property: function(property) {

      this.$http.post('/api/property/delete', {id: property.id}).then((response) => {

        this.properties.splice(this.properties.indexOf(property), 1);
          // success callback
          // delete from Vue array
        }, (response) => {
          // error callback
          // console.log('ajax - error');
        });
    },
    add_property: function(event) {

      var data = {
        item_id: this.item.id
      }

      this.$http.post('/api/property/create', data).then((response) => {
          // success callback
            this.properties.push({id: response.body.property_id});
          //console.log(response.body.property_id);
          //console.log('ajax - success');
          //console.log(response);
        }, (response) => {
          // error callback
          //console.log('ajax - error');
        });

    }
  }
});
