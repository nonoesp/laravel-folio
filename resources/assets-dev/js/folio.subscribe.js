var timer;

$(document).on('click', '.js--subscribe-link', function(event) {
    event.preventDefault();
    $(".js--subscribe__email").focus();
});

$(document).on('click', '.js--subscribe__submit', function(event) {
    event.preventDefault();

    let subscribe = $(this).parents(".js--subscribe");
    let email = subscribe.find(".js--subscribe__email").val();
    let source = subscribe.find(".js--subscribe__source").val();
    let medium = subscribe.find(".js--subscribe__medium").val();
    let campaign = subscribe.find(".js--subscribe__campaign").val();
    let path = window.location.pathname;

    if (FormValidator.prototype._hooks.valid_email({ value: email })) {

        $.ajax({
            url: '/subscriber/create',
            type: 'POST',
            data: {
                email: email,
                source: source,
                medium: medium,
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
        let label = subscribe.find(".js--subscribe__label");
        label.html(trans.email_is_not_valid);
        clearTimeout(timer);
        timer = setTimeout(() => {
            label.html(trans.to_receive_our_updates);
        }, 3000);

    }

});

function handleSubscribeResponse(response) {
    clearTimeout(timer);
    if (response.success == true) {
        $(".js--subscribe__form").hide();
        $(".js--subscribe__label").html(trans.thanks_for_subscribing).addClass('u-text-align--center');
        timer = setTimeout(function() { restoreSubscriptionForm() }, 5000);
    }
}

function restoreSubscriptionForm() {

    $(".js--subscribe__email").val('');
    $(".js--subscribe__form").show();
    $(".js--subscribe__label").removeClass('u-text-align--center');
    clearTimeout(timer);
    timer = setTimeout(function() {
        $(".js--subscribe__label").html(trans.to_receive_our_updates);
    }, 10000);
}