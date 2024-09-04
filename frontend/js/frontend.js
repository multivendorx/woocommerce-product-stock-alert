"use strict";

// On page load
jQuery(function ($) {


    /**
     * Init event listener on page loading.
     * @return {undefined}
     */
    function init() {
        $(document).on('click', '.stock-manager-button', subscribe);
        $(document).on('click', '.unsubscribe-button', unsubscribe);
        $(document).on('change', 'input.variation_id', getVariationSubscribeForm);
    }

    /**
     * Subscribe user on subscribe button click.
     * @param {object} event dom event object.
     * @return {undefined}
     */
    function subscribe(event) {
        // Prevent default form submition.
        event.preventDefault();

        const button = $(this);

        /**
         * Subscriber form dom objects
         * @var {object} dom objects
         */
        let forms = $('#stock_notifier_main_form_'+button.attr("data-product-id"));

        // Set button as processing and disable click event.
        button.text(localizeData.processing);
        button.addClass("stk_disabled");

        const recaptcha_enabled = localizeData.recaptcha_enabled;

        forms.each(function (index, element) {
            const form = $(element);

            // Product id matches then go ahead with submission
            if (form.find('.current-product-id').val() == button.attr("data-product-id")) {

                // Recaptch is enabled validate recaptch then process form
                if (recaptcha_enabled) {

                    const recaptcha_secret = form.find('#recaptchav3_secretkey').val();
                    const recaptcha_response = form.find('#recaptchav3_response').val();

                    // Prepare recaptcha request data.
                    const recaptcha_request = {
                        action: 'recaptcha_validate_ajax',
                        nonce: localizeData.nonce,
                        captcha_secret: recaptcha_secret,
                        captcha_response: recaptcha_response
                    }

                    // Request for recaptcha validation
                    $.post(localizeData.ajax_url, recaptcha_request, function (response) {

                        // If valid response process form submition.
                        if (response) {
                            processForm(form);
                        } else {

                            // Response is not a valid response alert and enable click.
                            alert('Oops, recaptcha not varified!');
                            button.removeClass("stk_disabled");
                        }
                    });
                } else {
                    processForm(form);
                }
            }

        });
    }

    /**
     * Process subscription
     * @param {undefined}
     */
    function processForm(form) {
        // Get data from form.
        let customerEmail = form.find('.stock-manager-email').val();
        let productId = form.find('.current-product-id').val();
        let variationId = form.find('.current-variation-id').val();
        let productTitle = form.find('.current-product-name').val();

        // Get data from localizer
        let buttonHtml = localizeData.button_html;
        let successMessage = localizeData.alert_success;
        let errorMessage = localizeData.error_occurs
        let tryAgainMessage = localizeData.try_again;
        let emailExist = localizeData.alert_email_exist;
        let validEmail = localizeData.valid_email;
        let banEmailDomin = localizeData.ban_email_domain_text;
        let banEmailAddress = localizeData.ban_email_address_text;
        let doubleOptInText = localizeData.double_opt_in_success;
        let unsubButtonHtml = localizeData.unsubscribe_button;

        // Prepare success message
        successMessage = successMessage.replace('%product_title%', productTitle);
        successMessage = successMessage.replace('%customer_email%', customerEmail);

        // Prepare email exist data
        emailExist = emailExist.replace('%product_title%', productTitle);
        emailExist = emailExist.replace('%customer_email%', customerEmail);

        if (isEmail(customerEmail)) {

            $(this).toggleClass('alert_loader').blur();

            // Request data for subscription
            let requestData = {
                action: 'alert_ajax',
                nonce: localizeData.nonce,
                email: customerEmail,
                product_id: productId,
                variation_id: variationId
            }

            // Add additional fields data 
            localizeData.additional_fields.forEach(element => {
                requestData[element] = $('#stock_manager_' + element).val();
            });

            // Request for subscription
            $.post(localizeData.ajax_url, requestData, function (response) {
                // Handle response
                if (response == '0') {
                    $(`#stock_notifier_main_form_`+productId).html(`<div class="registered-message"> ${errorMessage} <a href="${window.location}"> ${tryAgainMessage} </a></div>`);
                } else if (response == '/*?%already_registered%?*/') {
                    $(`#stock_notifier_main_form_`+productId).html(`<div class="registered-message">${emailExist}</div>${unsubButtonHtml}<input type="hidden" class="subscribed_email" value="${customerEmail}" /><input type="hidden" class="product_id" value="${productId}" /><input type="hidden" class="variation_id" value="${variationId}" />`);
                } else if (response == '/*?%ban_email_address%?*/') {
                    $(`.responsedata-error-message`).remove() && $(`#stock_notifier_main_form_`+productId).append($(`<p class="responsedata-error-message ban-email-address">${banEmailAddress}</p>`));
                } else if (response == '/*?%ban_email_domain%?*/') {
                    $(`.responsedata-error-message`).remove() && $(`#stock_notifier_main_form_`+productId).append($(`<p class="responsedata-error-message ban-email-address">${banEmailDomin}</p>`));
                } else if (response == '/*?%double_opt_in%?*/') {
                    $(`#stock_notifier_main_form_`+productId).html(`<div class="registered-message"> ${doubleOptInText}</div>`);
                } else {
                    $(`#stock_notifier_main_form_`+productId).html(`<div class="registered-message">${successMessage}</div>`);
                }
                $("#stock_manager_btn_"+productId).replaceWith(buttonHtml);
            });
        } else {
            $('.responsedata-error-message').remove() && $(`#stock_notifier_main_form_`+productId).append($(`<p style="color:#e2401c;" class="responsedata-error-message">${validEmail}</p>`));
            $("#stock_manager_btn_"+productId).replaceWith(buttonHtml);
        }
    }

    /**
     * Unsubscribe user on subscribe button click.
     * @param {object} event dom event object.
     * @return {undefined}
     */
    function unsubscribe(event) {

        // Prevent default from submittion.
        event.preventDefault();

        // Set button as processing and disable click event.
        $(this).text(localizeData.processing);
        $(this).addClass("stk_disabled");

        // Unsubscribe request data
        const unsubscribe_request = {
            action: 'unsubscribe_button',
            nonce: localizeData.nonce,
            customer_email: form.find('.subscribed_email').val(),
            product_id: form.find('.product_id').val(),
            var_id: form.find('.variation_id').val(),
        };

        // Prepare success message on subscribe.
        let success_message = localizeData.alert_unsubscribe_message;
        success_message = success_message.replace('%customer_email%', unsubscribe_request.customer_email);
        let error_message = localizeData.error_occurs;

        // Request for unsubscribe user.
        $.post(localizeData.ajax_url, unsubscribe_request, function (response) {
            // unsubscribe success
            if (response) {
                $('.stock-notifier-subscribe-form')
                    .html(`<div class="registered-message"> ${success_message}</div>`);
            } else {
                $('.stock-notifier-subscribe-form')
                    .html(`<div class="registered-message"> ${error_message}<a href="${window.location}"> ${localizeData.try_again}</a></div>`);
            }

            // Enable submit button.
            $(this).removeClass("stk_disabled");
        });
    }

    /**
     * Get subscription form of variation product.
     */
    function getVariationSubscribeForm() {

        const variationId = Number($(this).val());
        const productId = Number($('.stock-notifier-shortcode-subscribe-form').data('product-id'));

        // Subscription form exist and variation id exist
        if ($('.stock-notifier-shortcode-subscribe-form').length && variationId) {

            // Request body for subscription form
            const subscriptionFormRequest = {
                action: 'get_variation_box_ajax',
                nonce: localizeData.nonce,
                product_id: productId,
                variation_id: variationId
            };

            // Request for subscription form
            $.post(localizeData.ajax_url, subscriptionFormRequest, function (response) {

                // Set subscription form as inner-html
                $('.stock-notifier-shortcode-subscribe-form').html(response);
                form = $(document).find('.stock-notifier-subscribe-form');
            });
        }
        else {
            // Variation not exist.
            $('.stock-notifier-shortcode-subscribe-form').html("");
        }
    }

    /**
     * Check the email is valid email or not.
     * @param {String} email email to check
     * @returns {boolean} if the email is valid return true otherwise false
     */
    function isEmail(email) {
        if (!email) return false;

        // Regular expressing for email check
        const regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

        return regex.test(email);
    }

    // Call init function
    init();
});