/**
 * Framework Javascript Functions
 * Copyright (c) PageLines 2008 - 2013
 *
 * Written By PageLines
 */

!function ($) {
	
$(document).ready(function(){

	if($("#pl-dms-less").length){
	
		var cm_mode = jQuery("#pl-dms-less").data('mode')
		,	cm_config = jQuery.extend( cm_base_config, { cm_mode : cm_mode } )
		,	editor3 = CodeMirror.fromTextArea(jQuery("#pl-dms-less").get(0), cm_config)
		
	}

	if(jQuery("#pl-dms-scripts").length){
		
		var cm_mode = jQuery("#pl-dms-scripts").data('mode')
		,	cm_config = jQuery.extend( cm_base_config, { cm_mode : cm_mode } )
		,	editor4 = CodeMirror.fromTextArea(jQuery("#pl-dms-scripts").get(0), cm_config);
	}
	
	jQuery('.dms-update-setting').on('submit', function(e){
		
		var theSetting = jQuery(this).data('setting')
		,	theValue = jQuery('.input_'+theSetting).val()
		,	saveText = jQuery(this).find('.saving-confirm');

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'pl_dms_admin_actions'
				, value: theValue
				, setting: theSetting
				, mode: 'setting_update'
				, flag: 'admin_fallback'
			},
			beforeSend: function(){
			
				
				saveText.show().text('Saving'); // text while saving
				
				interval = window.setInterval(function(){
					var text = saveText.text();
					if (text.length < 10){	saveText.text(text + '.'); }
					else { saveText.text('Saving'); }
				}, 400);
				
				
			},
			success: function(response) {
				window.clearInterval(interval); // clear dots...
			
				saveText.text('Saved!');
				
				saveText
					.delay(800)
					.fadeOut('slow')
			}
		});

		return false;
		
	})



	
	$('.pl-account-action').on('click', function() {
	
	
		var theButton = $(this)
		,	theForm = theButton.closest('.pl-account-form')
		,	saveText = theForm.find('.saving-confirm');
	
		var key = theForm.find('#pl_activation').val()
		,	email = theForm.find('#pl_email').val()
		,	reset = (theButton.hasClass('deactivate-key')) ? true : false
		,	update = (theButton.hasClass('refresh-user')) ? true : false
		, 	theData = {
				action: 'pl_admin_ajax'
			,	mode: 'pl_account_actions'
			,	key: key
			,	email: email
			,	reset: reset
			, 	update: update
		}


		$.ajax({		
				type: 'POST'
			, 	url: ajaxurl
			, 	data: theData
			,	beforeSend: function(){
			
				
					saveText.show().text('Updating'); // text while saving
				
					interval = window.setInterval(function(){
						var text = saveText.text();
						if (text.length < 10){	saveText.text(text + '.'); }
						else { saveText.text('Updating'); }
					}, 200);
				
				
				}
			, 	success: function(response) {
					window.clearInterval(interval); // clear dots...
					var rsp	= $.parseJSON( response )
					
					
			
			
					// check for errors...
					if( '' ==  rsp.email ) {
						saveText.text('Please enter an email address.');
						 saveText
							.delay(1200)
							.fadeOut('slow')
					} else if ( '' ==  rsp.key ) {
						saveText.text('Please enter a valid key.');
						 saveText
							.delay(1200)
							.fadeOut('slow')
					} else {
						saveText.text('Updated!');
						 saveText
							.delay(800)
							.fadeOut('slow')



						var theMessages = ''

						$.each(rsp.messages, function(i, val){ 
							 theMessages += '<div>'+val+'</div>'
						})

						theForm.find('.the-msg').html(theMessages)
						
						pl_url_refresh( false, 800 );
					}
					
				
				//console.log( response );
			}
		})
		return false
	})
});
// End AJAX Uploading


/*
 * ###########################
 *   jQuery Extension
 * ###########################
 */

jQuery.fn.center = function ( relative_element ) {

    this.css("position","absolute");
    this.css("top", ( jQuery(window).height() - this.height() ) / 4+jQuery(window).scrollTop() + "px");
    this.css("left", ( jQuery(relative_element).width() - this.width() ) / 2+jQuery(relative_element).scrollLeft() + "px");
    return this;
}

jQuery.fn.exists = function(){return jQuery(this).length>0;}

}(window.jQuery);