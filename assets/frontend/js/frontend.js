/* global woo_stock_alert_script_data */
jQuery(document).ready(function($) {
	var register_html;
	$( ".variations_form" ).on( "woocommerce_variation_select_change", function () {
	    $('.alert_container').css('display', 'none');
	} );
	$(window).bind('found_variation', function(event, variation) {
	  	var pro_id = $('.current_product_id').val();
	  	if (variation == null) { 
	  	    if($('#product-'+pro_id).is('.outofstock')){
	  		    $('.alert_container').css('display', 'block');
	  	    }else{
	  	        $('.alert_container').css('display', 'none');
	  	    }
		}else{
			var var_id = '';
			if(variation.hasOwnProperty('variation_id')){  
				var_id = variation.variation_id;
			}else if(variation.hasOwnProperty('id')){
				var_id = variation.id; 
			}else{
				var_id = variation.id; 
			}
			
			$('.alert_container').css('display', 'none');
		  	var child_data = {
		  		action: 'alert_box_ajax',
		  		child_id: var_id
		  	};
		  	$.post(woo_stock_alert_script_data.ajax_url, child_data, function(response) {
		  		if( response == 'true' ) {
		  			$('.alert_container').css('display', 'block');
		  		} else if( response == 'false' ) {
		  			$('.alert_container').css('display', 'none');
		  		}
		  	});
		  	initStockAlertVariation(var_id);
		}
	}).trigger( 'found_variation' );
	//$('.variations_form').trigger( 'found_variation' );
	initStockAlert();

	$(document).on( 'click', '.alert_container .unsubscribe_button', function() { 
		$(this).text(woo_stock_alert_script_data.processing);	
		$(this).addClass("stk_disabled");	

		var customer_data = {
			action: 'unsubscribe_button',
			customer_email: $(this).parent().find('.subscribed_email').val(),
			product_id: $(this).parent().find('.product_id').val()
		};
		
		unsubscribe_successful_messsage = woo_stock_alert_script_data.alert_unsubscribe_message;
		unsubscribe_successful_messsage = unsubscribe_successful_messsage.replace( '%customer_email%', customer_data.customer_email );
		
		$.post(woo_stock_alert_script_data.ajax_url, customer_data, function(response) {
			$(this).removeClass("stk_disabled");	
			if(response == 'true') {
				$('.alert_container').html('<div class="registered_message">' + unsubscribe_successful_messsage + '</div>');
			} else {
				$('.alert_container').html('<div class="registered_message">'+woo_stock_alert_script_data.error_occurs+'<a href="'+window.location+'"> '+woo_stock_alert_script_data.try_again+'</a></div>');
			}
		});
	});
	
	function initStockAlert() {
		$('.alert_container').off('click').on('click','.stock_alert_button', function() {
			var $this = $(this);
			cus_email = $(this).parent().find('.stock_alert_email').val();
			pro_id = $(this).parent().find('.current_product_id').val();
			pro_title = $(this).parent().find('.current_product_name').val();
			register_box = $('.alert_container').html();
			
			alert_text_html = woo_stock_alert_script_data.alert_text_html;
			button_html = woo_stock_alert_script_data.button_html;
			alert_success = woo_stock_alert_script_data.alert_success;
			alert_email_exist = woo_stock_alert_script_data.alert_email_exist;
			valid_email = woo_stock_alert_script_data.valid_email;
			unsubscribe_button_html = woo_stock_alert_script_data.unsubscribe_button;
			
			alert_success = alert_success.replace( '%product_title%', pro_title );
			alert_success = alert_success.replace( '%customer_email%', cus_email );
			
			alert_email_exist = alert_email_exist.replace( '%product_title%', pro_title );
			alert_email_exist = alert_email_exist.replace( '%customer_email%', cus_email );
			
			if( cus_email && validateEmail(cus_email) ) {
				//$(this).attr("value","Processing...");
				//$(this).addClass("stk_disabled");
				$(this).toggleClass('alert_loader').blur();	
				var stock_alert = {
					action: 'alert_ajax',
					email: cus_email,
					product_id: pro_id
				}
				$.post(woo_stock_alert_script_data.ajax_url, stock_alert, function(response) { console.log(response);
					//$(this).removeClass("stk_disabled");
					$this.removeClass('alert_loader').blur();	
					if( response == '0' ) {
						$('.alert_container').html('<div class="registered_message">'+woo_stock_alert_script_data.error_occurs+'<a href="'+window.location+'"> '+woo_stock_alert_script_data.try_again+'</a></div>');
					} else if( response == '/*?%already_registered%?*/' ) {
						$('.alert_container').html('<div class="registered_message">'+alert_email_exist+'</div>'+unsubscribe_button_html+'<input type="hidden" class="subscribed_email" value="'+cus_email+'" /><input type="hidden" class="product_id" value="'+pro_id+'" />');
					} else {
						$('.alert_container').html('<div class="registered_message">'+alert_success+'</div>');
					}
				});
			} else {
				$('.alert_container').html(alert_text_html+'<input type="text" class="stock_alert_email" name="alert_email" />'+button_html+'<p style="color:#e2401c;">'+valid_email+'</p><input type="hidden" class="current_product_id" value="'+pro_id+'" /><input type="hidden" class="current_product_name" value="'+pro_title+'" />');
			}
		});
	}
	
	function initStockAlertVariation(variation_id) { 
		$('.alert_container').off('click').on('click','.stock_alert_button', function() {
			
			cus_email = $(this).parent().find('.stock_alert_email').val();
			//variation_id = $(this).parent().parent().parent().find('.variation_id').val();
			variation_id = variation_id;
			pro_id = $(this).parent().find('.current_product_id').val();
			pro_title = $(this).parent().find('.current_product_name').val();
			register_html = $('.alert_container').html();
			
			alert_text_html = woo_stock_alert_script_data.alert_text_html;
			button_html = woo_stock_alert_script_data.button_html;
			alert_success = woo_stock_alert_script_data.alert_success;
			alert_email_exist = woo_stock_alert_script_data.alert_email_exist;
			valid_email = woo_stock_alert_script_data.valid_email;
			unsubscribe_button_html = woo_stock_alert_script_data.unsubscribe_button;
			
			alert_success = alert_success.replace( '%product_title%', pro_title );
			alert_success = alert_success.replace( '%customer_email%', cus_email );
			
			alert_email_exist = alert_email_exist.replace( '%product_title%', pro_title );
			alert_email_exist = alert_email_exist.replace( '%customer_email%', cus_email );
			
			if( cus_email && validateEmail(cus_email) ) {
				$(this).attr("value",woo_stock_alert_script_data.processing);
				$(this).addClass("stk_disabled");	
				var stock_alert = {
					action: 'alert_ajax',
					email: cus_email,
					product_id: variation_id
				}
				$.post(woo_stock_alert_script_data.ajax_url, stock_alert, function(response) {
					$(this).removeClass("stk_disabled");	
					if( response == '0' ) {
						$('.alert_container').html('<div class="registered_message">'+woo_stock_alert_script_data.error_occurs+'<a href="'+window.location+'"> '+woo_stock_alert_script_data.try_again+'</a></div>');
					} else if( response == '/*?%already_registered%?*/' ) {
						$('.alert_container').html('<div class="registered_message">'+alert_email_exist+'</div>'+unsubscribe_button_html+'<input type="hidden" class="subscribed_email" value="'+cus_email+'" /><input type="hidden" class="product_id" value="'+variation_id+'" />');
					} else {
						$('.alert_container').html('<div class="registered_message">'+alert_success+'</div>');
					}
				});
			} else {
				$('.alert_container').html(alert_text_html+'<input type="text" class="stock_alert_email" name="alert_email" />'+button_html+'<p style="color:#e2401c;">'+valid_email+'</p><input type="hidden" class="current_product_id" value="'+pro_id+'" /><input type="hidden" class="current_product_name" value="'+pro_title+'" />');
			}
		});
	}
	
	function validateEmail(sEmail) {
		var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
		if (filter.test(sEmail)) {
			return true;
		} else {
			return false;
		}
	}

});