jQuery( 'input.variation_id' ).change( function(){
    var alert_text_html = stock_alert_sc_data.alert_text_html;
    var button_html = stock_alert_sc_data.button_html;
    var pro_title = stock_alert_sc_data.product_title;
    if( '' != jQuery(this).val() ) {
        var var_id = jQuery(this).val();
        var stock_alert_box = {
            action: 'get_variation_box_ajax',
            product_id: stock_alert_sc_data.product_id,
            variation_id : var_id
        };
        for (var i=0; i<stock_alert_sc_data.additional_fields.length; i++){
            stock_alert_box[stock_alert_sc_data.additional_fields[i]] = jQuery(this).parent().find('.'+stock_alert_sc_data.additional_fields[i]).val();
        }

        jQuery.post( stock_alert_sc_data.ajax_url, stock_alert_box, function(response) {
            jQuery('.stock_notifier-shortcode-subscribe-form').html(response); 
        });
    } else{
        jQuery('.stock_notifier-shortcode-subscribe-form').html('');
    }
});