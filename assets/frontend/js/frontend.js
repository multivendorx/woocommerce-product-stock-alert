"use strict";
jQuery(function ($) {
    var instock_notifier = {
        init: function () {
            $(document).on('click', '.stock_alert_button', this.subscribe_form);
            $(document).on('click', '.unsubscribe_button', this.unsubscribe_form);
            $(".single_variation_wrap").on("show_variation", this.perform_upon_show_variation);
        },
        perform_upon_show_variation: function (event, variation) {
            var vid = variation.variation_id;
            $('.stock_notifier-subscribe-form').hide(); //remove existing form
            $('.stock_notifier-subscribe-form-' + vid).show(); //add subscribe form to show
        },
        is_email: function (email) {
            var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if (!regex.test(email)) {
                return false;
            } else {
                return true;
            }
        },

        unsubscribe_form: function (e) {
            e.preventDefault();
            $(this).text(woo_stock_alert_script_data.processing);   
            $(this).addClass("stk_disabled");   
            var form = $(this).closest('.stock_notifier-subscribe-form');
            var customer_data = {
                action: 'unsubscribe_button',
                customer_email: form.find('.subscribed_email').val(),
                product_id: form.find('.product_id').val(),
                var_id : form.find('.variation_id').val(),
            };
            
            var unsubscribe_successful_messsage = woo_stock_alert_script_data.alert_unsubscribe_message;
            unsubscribe_successful_messsage = unsubscribe_successful_messsage.replace( '%customer_email%', customer_data.customer_email );
            
            $.post(woo_stock_alert_script_data.ajax_url, customer_data, function(response) {
                $(this).removeClass("stk_disabled");    
                if(response == true) {
                    $('.stock_notifier-subscribe-form').html('<div class="registered_message">' + unsubscribe_successful_messsage + '</div>');
                } else {
                    $('.stock_notifier-subscribe-form').html('<div class="registered_message">'+woo_stock_alert_script_data.error_occurs+'<a href="'+window.location+'"> '+woo_stock_alert_script_data.try_again+'</a></div>');
                }
            });
        },
        subscribe_form: function (e) {
            e.preventDefault();
            $(this).text(woo_stock_alert_script_data.processing);
            $(this).addClass("stk_disabled");
            var recaptcha_enabled = woo_stock_alert_script_data.recaptcha_enabled;
            var form = $(this).closest('.stock_notifier-subscribe-form');

            if (recaptcha_enabled) {
                var recaptcha_secret = form.find('#recaptchav3_secretkey').val();
                var recaptcha_response = form.find('#recaptchav3_response').val();
                var recaptcha = {
                    action: 'recaptcha_validate_ajax',
                    captcha_secret : recaptcha_secret,
                    captcha_response : recaptcha_response
                }

                $.post(woo_stock_alert_script_data.ajax_url, recaptcha, function(response) {
                    if (response == 1) {
                        instock_notifier.process_form(form.find('.stock_alert_email').val(), form.find('.current_product_id').val(), form.find('.current_variation_id').val(), form.find('.current_product_name').val());
                    } else {
                        alert('Oops, recaptcha not varified!');
                        $(this).removeClass("stk_disabled");
                    }
                });
            } else {
                instock_notifier.process_form(form.find('.stock_alert_email').val(), form.find('.current_product_id').val(), form.find('.current_variation_id').val(), form.find('.current_product_name').val());
            }
        },

        process_form: function(cus_email, product_id, var_id, pro_title) {      
            var alert_text_html = woo_stock_alert_script_data.alert_text_html;
            var button_html = woo_stock_alert_script_data.button_html;
            var alert_success = woo_stock_alert_script_data.alert_success;
            var alert_email_exist = woo_stock_alert_script_data.alert_email_exist;
            var valid_email = woo_stock_alert_script_data.valid_email;
            var ban_email_domin = woo_stock_alert_script_data.ban_email_domin;
            var ban_email_address = woo_stock_alert_script_data.ban_email_address;
            var double_opt_in_text = woo_stock_alert_script_data.double_opt_in_success;
            var unsubscribe_button_html = woo_stock_alert_script_data.unsubscribe_button;
            var alert_fields = woo_stock_alert_script_data.alert_fields;
            
            var alert_success = alert_success.replace( '%product_title%', pro_title );
            var alert_success = alert_success.replace( '%customer_email%', cus_email );
            
            var alert_email_exist = alert_email_exist.replace( '%product_title%', pro_title );
            var alert_email_exist = alert_email_exist.replace( '%customer_email%', cus_email );

            if( cus_email && instock_notifier.is_email(cus_email) ) {
                $(this).toggleClass('alert_loader').blur(); 
                var stock_alert = {
                    action: 'alert_ajax',
                    email: cus_email,
                    product_id: product_id,
                    variation_id : var_id
                }

                for (var i=0; i<woo_stock_alert_script_data.additional_fields.length; i++){
                    stock_alert[woo_stock_alert_script_data.additional_fields[i]] = $(this).parent().find('.'+woo_stock_alert_script_data.additional_fields[i]).val();
                }

                $.post(woo_stock_alert_script_data.ajax_url, stock_alert, function(response) {   
                    
                    if (response == '0') {
                        $('.stock_notifier-subscribe-form').html('<div class="registered_message">'+woo_stock_alert_script_data.error_occurs+'<a href="'+window.location+'"> '+woo_stock_alert_script_data.try_again+'</a></div>');
                    } else if (response == '/*?%already_registered%?*/') {
                        $('.stock_notifier-subscribe-form').html('<div class="registered_message">'+alert_email_exist+'</div>'+unsubscribe_button_html+'<input type="hidden" class="subscribed_email" value="'+cus_email+'" /><input type="hidden" class="product_id" value="'+product_id+'" /><input type="hidden" class="variation_id" value="'+var_id+'" />');
                    } else if (response == '/*?%ban_email_address%?*/') {
                        $('.stock_notifier-subscribe-form').html(alert_text_html+'<div class="woo_fields_wrap">'+alert_fields+''+button_html+'</div><p class="stock_alert_error_message ban_email_address">'+ban_email_address+'</p><input type="hidden" class="current_product_id" value="'+product_id+'" /> <input type="hidden" class="current_variation_id" value="'+var_id+'" /><input type="hidden" class="current_product_name" value="'+pro_title+'" />');
                    } else if (response == '/*?%ban_email_domain%?*/') {
                        $('.stock_notifier-subscribe-form').html(alert_text_html+'<div class="woo_fields_wrap">'+alert_fields+''+button_html+'</div><p class="stock_alert_error_message ban_email_domin">'+ban_email_domin+'</p><input type="hidden" class="current_product_id" value="'+product_id+'" /> <input type="hidden" class="current_variation_id" value="'+var_id+'" /><input type="hidden" class="current_product_name" value="'+pro_title+'" />');
                    } else if (response == '/*?%double_opt_in%?*/') {
                        $('.stock_notifier-subscribe-form').html('<div class="registered_message">'+double_opt_in_text+'</div>');
                    } else {
                        $('.stock_notifier-subscribe-form').html('<div class="registered_message">'+alert_success+'</div>');
                    }
                });
            } else {
                $('.stock_notifier-subscribe-form').html(alert_text_html+'<div class="woo_fields_wrap">'+alert_fields+''+button_html+'</div><p style="color:#e2401c;" class="stock_alert_error_message">'+valid_email+'</p><input type="hidden" class="current_product_id" value="'+product_id+'" /> <input type="hidden" class="current_variation_id" value="'+var_id+'" /><input type="hidden" class="current_product_name" value="'+pro_title+'" />');
            }
            $(this).removeClass("stk_disabled");
        }
    };
    instock_notifier.init();
});