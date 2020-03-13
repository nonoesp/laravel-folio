var timer;

$(document).on('click', '.js--subscribe-link', function(event) {
    event.preventDefault();
    $(".js--subscribe__email").focus();
});

$(document).on('click', '.js--subscribe__submit', function(event) {

    event.preventDefault();

    let subscribeContainer = $(this).parents(".js--subscribe");
    let submitButton = $(this);
    let emailField = subscribeContainer.find(".js--subscribe__email");

    let email = emailField.val();
    let source = subscribeContainer.find(".js--subscribe__source").val();
    let medium = subscribeContainer.find(".js--subscribe__medium").val();
    let campaign = subscribeContainer.find(".js--subscribe__campaign").val();
    let newsletter_list = subscribeContainer.find(".js--subscribe__newsletter-list").val();
    let path = window.location.pathname;

    if (FormValidator.prototype._hooks.valid_email({ value: email })) {

        // Show form is working
        emailField.prop('disabled', true);
        submitButton.prop('disabled', true);
        submitButton.val("...");

        $.ajax({
            url: '/subscriber/create',
            type: 'POST',
            data: {
                email,
                source,
                medium,
                campaign,
                newsletter_list,
                path
            },
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                handleSubscribeResponse(response, subscribeContainer);

                // Reset form state
                emailField.prop('disabled', false);
                submitButton.prop('disabled', false);
                submitButton.val(trans.subscribe_button_text);
            },
            traditional: true
        });

    } else {

        handleInvalidEmail(subscribeContainer);

    }

});

function handleInvalidEmail(container) {

    let label = container.find(".js--subscribe__label");

    label.html(trans.email_is_not_valid);

    clearTimeout(timer);
    timer = setTimeout(() => {
        label.html(trans.to_receive_our_updates);
    }, 3000);

}

function handleSubscribeResponse(response, container) {
    clearTimeout(timer);

    const form = container.find(".js--subscribe__form");
    const label = container.find(".js--subscribe__label");
    form.hide();

    if (response.success == true) {

        label.html(trans.thanks_for_subscribing).addClass('u-text-align--center');

    } else {
        label.html(trans.something_is_not_working_well).addClass('u-text-align--center');
    }

    timer = setTimeout(() => {
        restoreSubscriptionForm(container);
    }, 5000);    
}

function restoreSubscriptionForm(container) {

    let email = container.find(".js--subscribe__email");
    let form = container.find(".js--subscribe__form");
    let label = container.find(".js--subscribe__label");

    email.val('');
    form.show();
    label.removeClass('u-text-align--center');

    clearTimeout(timer);
    timer = setTimeout(() => {
        label.html(trans.to_receive_our_updates);
    }, 10000);
}