(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
        $(document).ready(function(){
		var adurl = 'admin.php?page=casheer_payment_gateway';
		$("#woocommerce_casheer_ogpaymentmethod").closest('tr').hide();
		$("#woocommerce_casheer_paymentmethodmode").after('<span id="payment-help-text">Save current configuration and <a href="'+adurl+'" target="blank">Add new Payment Methods</a></span>');	
		var methodmode = $("#woocommerce_casheer_paymentmethodmode").val();
		if(methodmode=='ogpayment'){
			$("#woocommerce_casheer_ogpaymentmethod").closest('tr').show();
			$("#woocommerce_casheer_ogpaymentcurrency").closest('tr').show();
			$("#payment-help-text").hide();			
		}else{
			$("#woocommerce_casheer_ogpaymentmethod").closest('tr').hide();
			$("#woocommerce_casheer_ogpaymentcurrency").closest('tr').hide();
			$("#payment-help-text").show();
		}
			
		$("#woocommerce_casheer_paymentmethodmode").change(function(){
		var methodmode = $(this).val();
			
			if(methodmode=='ogpayment'){
				$("#woocommerce_casheer_ogpaymentmethod").closest('tr').show();
				$("#woocommerce_casheer_ogpaymentcurrency").closest('tr').show();
				$("#payment-help-text").hide();
			}else{
				$("#woocommerce_casheer_ogpaymentmethod").closest('tr').hide();
				$("#woocommerce_casheer_ogpaymentcurrency").closest('tr').hide();
				$("#payment-help-text").show();
			}			
		});
			
        
		var i=$('#dynamic_field thead').attr('data-key');	
        $('#add').click(function(){
        i++;
        $('#dynamic_field').append('<tr id="row'+i+'"><td><input type="text" name="customize_payment['+i+'][name]" placeholder="" class="form-control name_list" /></td><td><input type="text" name="customize_payment['+i+'][code]" placeholder="" class="form-control name_list" /></td><td><input type="text" name="customize_payment['+i+'][currency]" placeholder="" class="form-control name_list" /></td><td><input type="text" name="customize_payment['+i+'][tunnel]" placeholder="" class="form-control name_list tunnelid"/></td><td><button type="button" name="remove" id="'+i+'" class="wp-core-ui button button-secondary btn_remove">Remove</button></td></tr>');
        });
        	
			$(document).on('click', '.btn_remove', function(){
			var button_id = $(this).attr("id"); 
			$('#row'+button_id+'').remove();
			});
          $('#form-casheer input[type="text"]').bind('blur', function(){
            $(this).val(function(_, v){
             return $.trim(v);
            });
          });
			$('#form-casheer').on('submit', function(event) {
				var err = 0;
				 $('#form-casheer input[type="text"]').each(function() {
					$(this).val(function(_, v){
					 return $.trim(v);
					});					 
					 $(this).removeClass('haserror');
					 $("#alert_message").html("");
					console.log($(this).val().length);
					if($(this).val().length === 0){
						console.log("fkffef");
						if(!$(this).hasClass('tunnelid')) {
							$(this).addClass('haserror');
							err++;
						}
						$("#alert_message").html("All fields are required!!");
					}else{
						$(this).removeClass('haserror');
					}
				 });
				
				if(err>0){
					event.preventDefault();
				}else{
					$("#alert_message").html("");
				}
				
			});	
			
			
        });
})( jQuery );
