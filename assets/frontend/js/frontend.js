"use strict";

var instock_notifier = {
    init: function () {
        jQuery(document).on('click', '.stock_alert_button', this.subscribe_form);
        jQuery(document).on('click', '.unsubscribe_button', this.unsubscribe_form);
        jQuery(".single_variation_wrap").on("show_variation", this.perform_upon_show_variation);
    },
    perform_upon_show_variation: function (event, variation) {
        var vid = variation.variation_id;
        jQuery('.stock_notifier-subscribe-form').hide(); //remove existing form
        jQuery('.stock_notifier-subscribe-form-' + vid).show(); //add subscribe form to show
    },
    is_email: function (email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    },
    unsubscribe_form: function (e) {
        e.preventDefault();
        jQuery(this).text(woo_stock_alert_script_data.processing);   
        jQuery(this).addClass("stk_disabled");   

        var customer_data = {
            action: 'unsubscribe_button',
            customer_email: jQuery(this).closest('.stock_notifier-subscribe-form').find('.subscribed_email').val(),
            product_id: jQuery(this).closest('.stock_notifier-subscribe-form').find('.product_id').val(),
            var_id : jQuery(this).closest('.stock_notifier-subscribe-form').find('.variation_id').val(),
        };
        
        var unsubscribe_successful_messsage = woo_stock_alert_script_data.alert_unsubscribe_message;
        unsubscribe_successful_messsage = unsubscribe_successful_messsage.replace( '%customer_email%', customer_data.customer_email );
        
        jQuery.post(woo_stock_alert_script_data.ajax_url, customer_data, function(response) {
            jQuery(this).removeClass("stk_disabled");    
            if(response == 'true') {
                jQuery('.stock_notifier-subscribe-form').html('<div class="registered_message">' + unsubscribe_successful_messsage + '</div>');
            } else {
                jQuery('.stock_notifier-subscribe-form').html('<div class="registered_message">'+woo_stock_alert_script_data.error_occurs+'<a href="'+window.location+'"> '+woo_stock_alert_script_data.try_again+'</a></div>');
            }
        });
    },
    subscribe_form: function (e) {
        e.preventDefault();
        jQuery(this).addClass("stk_disabled"); 
        var submit_button_obj = jQuery(this);
        var cus_email = jQuery(this).closest('.stock_notifier-subscribe-form').find('.stock_alert_email').val();
        var product_id = jQuery(this).closest('.stock_notifier-subscribe-form').find('.current_product_id').val();
        var pro_title = jQuery(this).closest('.stock_notifier-subscribe-form').find('.current_product_name').val();
        var var_id = jQuery(this).closest('.stock_notifier-subscribe-form').find('.current_variation_id').val();
        
        var alert_text_html = woo_stock_alert_script_data.alert_text_html;
        var button_html = woo_stock_alert_script_data.button_html;
        var alert_success = woo_stock_alert_script_data.alert_success;
        var alert_email_exist = woo_stock_alert_script_data.alert_email_exist;
        var valid_email = woo_stock_alert_script_data.valid_email;
        var unsubscribe_button_html = woo_stock_alert_script_data.unsubscribe_button;
        
        var alert_success = alert_success.replace( '%product_title%', pro_title );
        var alert_success = alert_success.replace( '%customer_email%', cus_email );
        
        var alert_email_exist = alert_email_exist.replace( '%product_title%', pro_title );
        var alert_email_exist = alert_email_exist.replace( '%customer_email%', cus_email );

        if( cus_email && instock_notifier.is_email(cus_email) ) {
            jQuery(this).toggleClass('alert_loader').blur(); 
            var stock_alert = {
                action: 'alert_ajax',
                email: cus_email,
                product_id: product_id,
                variation_id : var_id
            }

            for (var i=0; i<woo_stock_alert_script_data.additional_fields.length; i++){
                stock_alert[woo_stock_alert_script_data.additional_fields[i]] = jQuery(this).parent().find('.'+woo_stock_alert_script_data.additional_fields[i]).val();
            }

            jQuery.post(woo_stock_alert_script_data.ajax_url, stock_alert, function(response) { console.log(response);   
                if( response == '0' ) {
                    jQuery('.stock_notifier-subscribe-form').html('<div class="registered_message">'+woo_stock_alert_script_data.error_occurs+'<a href="'+window.location+'"> '+woo_stock_alert_script_data.try_again+'</a></div>');
                } else if( response == '/*?%already_registered%?*/' ) {
                    jQuery('.stock_notifier-subscribe-form').html('<div class="registered_message">'+alert_email_exist+'</div>'+unsubscribe_button_html+'<input type="hidden" class="subscribed_email" value="'+cus_email+'" /><input type="hidden" class="product_id" value="'+product_id+'" /><input type="hidden" class="variation_id" value="'+var_id+'" />');
                } else {
                    jQuery('.stock_notifier-subscribe-form').html('<div class="registered_message">'+alert_success+'</div>');
                }
            });
        } else {
            jQuery('.stock_notifier-subscribe-form').html(alert_text_html+'<input type="text" class="stock_alert_email" name="alert_email" />'+button_html+'<p style="color:#e2401c;" class="stock_alert_error_message">'+valid_email+'</p><input type="hidden" class="current_product_id" value="'+product_id+'" /><input type="hidden" class="current_product_name" value="'+pro_title+'" />');
        }
    },
};

instock_notifier.init();