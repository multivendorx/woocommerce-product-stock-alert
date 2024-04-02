"use strict";

jQuery( function ($) {

    var instock_notifier = {
        init: function () {
            $(document).on('click', '.stock_manager_button', this.subscribe_form);
            $(document).on('click', '.unsubscribe_button', this.unsubscribe_form);
            $(document).on('change', 'input.variation_id', this.getVariationSubscribeForm);
        },

        getVariationSubscribeForm: function () {
            const variationId = Number($(this).val());
            const productId = Number($('.stock_notifier-shortcode-subscribe-form').data('product-id'));
            if ($('.stock_notifier-shortcode-subscribe-form').length && variationId) {
                const responseData = {
                    action: 'get_variation_box_ajax',
                    nonce: woo_stock_manager_script_data.nonce,
                    product_id: productId,
                    variation_id: variationId
                };
                $.post( woo_stock_manager_script_data.ajax_url, responseData, function(response) {
                    $('.stock_notifier-shortcode-subscribe-form').html(response); 
                });
            }
            else {
                $('.stock_notifier-shortcode-subscribe-form').html("");
            }
        },

        is_email: function (email) {
            const regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if (!regex.test(email)) {
                return false;
            } else {
                return true;
            }
        },

        unsubscribe_form: function (e) {
            e.preventDefault();
            $(this).text(woo_stock_manager_script_data.processing);   
            $(this).addClass("stk_disabled");   
            const form = $(this).closest('.stock_notifier-subscribe-form');
            const customer_data = {
                action: 'unsubscribe_button',
                nonce: woo_stock_manager_script_data.nonce,
                customer_email: form.find('.subscribed_email').val(),
                product_id: form.find('.product_id').val(),
                var_id : form.find('.variation_id').val(),
            };
            
            let unsubscribe_successful_messsage = woo_stock_manager_script_data.alert_unsubscribe_message;
            unsubscribe_successful_messsage = unsubscribe_successful_messsage.replace( '%customer_email%', customer_data.customer_email );
            
            $.post(woo_stock_manager_script_data.ajax_url, customer_data, function(response) {
                $(this).removeClass("stk_disabled");    
                if(response == true) {
                    $('.stock_notifier-subscribe-form').html('<div class="registered_message">' + unsubscribe_successful_messsage + '</div>');
                } else {
                    $('.stock_notifier-subscribe-form').html('<div class="registered_message">'+woo_stock_manager_script_data.error_occurs+'<a href="'+window.location+'"> '+woo_stock_manager_script_data.try_again+'</a></div>');
                }
            });
        },

        subscribe_form: function (e) {
            e.preventDefault();
            $(this).text(woo_stock_manager_script_data.processing);
            $(this).addClass("stk_disabled");
            const recaptcha_enabled = woo_stock_manager_script_data.recaptcha_enabled;
            const form = $(this).closest('.stock_notifier-subscribe-form');
            
            if (recaptcha_enabled) {
                const recaptcha_secret = form.find('#recaptchav3_secretkey').val();
                const recaptcha_response = form.find('#recaptchav3_response').val();
                const recaptcha = {
                    action: 'recaptcha_validate_ajax',
                    nonce: woo_stock_manager_script_data.nonce,
                    captcha_secret : recaptcha_secret,
                    captcha_response : recaptcha_response
                }

                $.post(woo_stock_manager_script_data.ajax_url, recaptcha, function(response) {
                    if (response == 1) {
                        instock_notifier.process_form(form.find('.stock_manager_email').val(), form.find('.current_product_id').val(), form.find('.current_variation_id').val(), form.find('.current_product_name').val());
                    } else {
                        alert('Oops, recaptcha not varified!');
                        $(this).removeClass("stk_disabled");
                    }
                });
            } else {
                instock_notifier.process_form(form.find('.stock_manager_email').val(), form.find('.current_product_id').val(), form.find('.current_variation_id').val(), form.find('.current_product_name').val());
            }
        },

        process_form: function (cus_email, product_id, var_id, pro_title) {
            var button_html = woo_stock_manager_script_data.button_html;
            var alert_success = woo_stock_manager_script_data.alert_success;
            var alert_email_exist = woo_stock_manager_script_data.alert_email_exist;
            var valid_email = woo_stock_manager_script_data.valid_email;
            var ban_email_domin = woo_stock_manager_script_data.ban_email_domain_text;
            var ban_email_address = woo_stock_manager_script_data.ban_email_address_text;
            var double_opt_in_text = woo_stock_manager_script_data.double_opt_in_success;
            var unsubscribe_button_html = woo_stock_manager_script_data.unsubscribe_button;
            
            var alert_success = alert_success.replace( '%product_title%', pro_title );
            var alert_success = alert_success.replace( '%customer_email%', cus_email );
            
            var alert_email_exist = alert_email_exist.replace( '%product_title%', pro_title );
            var alert_email_exist = alert_email_exist.replace( '%customer_email%', cus_email );

            if (cus_email && instock_notifier.is_email(cus_email)) {
                $(this).toggleClass('alert_loader').blur(); 
                var responseData = {
                    action: 'alert_ajax',
                    nonce: woo_stock_manager_script_data.nonce,
                    email: cus_email,
                    product_id: product_id,
                    variation_id : var_id
                }

                for (var i=0; i<woo_stock_manager_script_data.additional_fields.length; i++) {
                    responseData[woo_stock_manager_script_data.additional_fields[i]] = $('#woo_stock_manager_' + woo_stock_manager_script_data.additional_fields[i]).val();
                }

                $.post(woo_stock_manager_script_data.ajax_url, responseData, function(response) {   
                    
                    if (response == '0') {
                        $('.stock_notifier-subscribe-form').html('<div class="registered_message">'+woo_stock_manager_script_data.error_occurs+'<a href="'+window.location+'"> '+woo_stock_manager_script_data.try_again+'</a></div>');
                    } else if (response == '/*?%already_registered%?*/') {
                        $('.stock_notifier-subscribe-form').html('<div class="registered_message">'+alert_email_exist+'</div>'+unsubscribe_button_html+'<input type="hidden" class="subscribed_email" value="'+cus_email+'" /><input type="hidden" class="product_id" value="'+product_id+'" /><input type="hidden" class="variation_id" value="'+var_id+'" />');
                    } else if (response == '/*?%ban_email_address%?*/') {
                        $('.responseData_error_message').remove() && $('.stock_notifier-subscribe-form').append($(`<p class="responseData_error_message ban_email_address">${ban_email_address}</p>`));
                    } else if (response == '/*?%ban_email_domain%?*/') {
                        $('.responseData_error_message').remove() && $('.stock_notifier-subscribe-form').append($(`<p class="responseData_error_message ban_email_address">${ban_email_domin}</p>`));
                    } else if (response == '/*?%double_opt_in%?*/') {
                        $('.stock_notifier-subscribe-form').html('<div class="registered_message">'+double_opt_in_text+'</div>');
                    } else {
                        $('.stock_notifier-subscribe-form').html('<div class="registered_message">'+alert_success+'</div>');
                    }
                    $(".stock_manager_button").replaceWith(button_html);
                });
            } else {
                $('.responseData_error_message').remove() && $('.stock_notifier-subscribe-form').append($(`<p style="color:#e2401c;" class="responseData_error_message">${valid_email}</p>`));
                $(".stock_manager_button").replaceWith(button_html);
            }
        }
    };
    instock_notifier.init();
});