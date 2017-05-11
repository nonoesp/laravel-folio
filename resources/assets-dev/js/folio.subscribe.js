
 var timer;

 $(document).on('click', '.js--subscribe-link', function(event)
 {
  event.preventDefault();
  console.log("js--subscribe-link");
  $(".js--subscribe__email").focus();
 });

 $(document).on('click', '.js--subscribe__submit', function(event)
 {
  event.preventDefault();

  var email = $(".js--subscribe__email").val();
  var source = $(".js--subscribe__source").val();
  var campaign = $(".js--subscribe__campaign").val();
  var path = window.location.pathname;

  if (FormValidator.prototype._hooks.valid_email({value: email})) {

    $.ajax({
          url: '/subscriber/create',
          type: 'POST',
          data: {
            email: email,
            source: source,
            campaign: campaign,
            path: path
          },
          dataType: "json",
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(data) {
               handleSubscribeResponse(data);
          },
          traditional: true
    });

  } else {

    $(".js--subscribe__label").html(trans.email_is_not_valid);
    clearTimeout(timer);
    timer = setTimeout("$('.js--subscribe__label').html(trans.to_receive_our_updates)", 3000);

  }

 });

 function handleSubscribeResponse(response) {
  clearTimeout(timer);
 	if(response.success == true) {
 		$(".js--subscribe__form").hide();
 		$(".js--subscribe__label").html(trans.thanks_for_subscribing).addClass('u-text-align--center');
    timer = setTimeout(function() { restoreSubscriptionForm() }, 5000);
 	}
 }

 function restoreSubscriptionForm() {
  console.log('restore subscription form');
  $(".js--subscribe__email").val('');
  $(".js--subscribe__form").show();
  $(".js--subscribe__label").removeClass('u-text-align--center');
  clearTimeout(timer);
  timer = setTimeout(function() {
      $(".js--subscribe__label").html(trans.to_receive_our_updates);
  }, 10000);
 }
