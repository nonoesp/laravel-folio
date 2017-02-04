
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

  if (FormValidator.prototype._hooks.valid_email({value: email})) {

    $.ajax({
          url: '/subscriber/create',
          type: 'POST',
          data: {email: $('.js--subscribe__email').val()},
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
 		$(".js--subscribe__form").remove();
 		$(".js--subscribe__label").html(trans.thanks_for_subscribing);
 	}
 }
