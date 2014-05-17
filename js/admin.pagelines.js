/**
 * Framework Javascript Functions
 * Copyright (c) PageLines 2008 - 2013
 *
 * Written By PageLines
 */

!function ($) {
	
$(document).ready(function(){

	if($("#pl-dms-less").length){
	
		var cm_mode = $("#pl-dms-less").data('mode')
		,	cm_config = $.extend( {}, cm_base_config, { mode : cm_mode } )
		,	editor3 = CodeMirror.fromTextArea($("#pl-dms-less").get(0), cm_config)
		
	}

	if($("#pl-dms-scripts").length){
		
		var cm_mode = $("#pl-dms-scripts").data('mode')
		,	cm_config = $.extend( {}, cm_base_config, { mode : cm_mode } )
		,	editor4 = CodeMirror.fromTextArea($("#pl-dms-scripts").get(0), cm_config);
	}
	
	$('.dms-update-setting').on('submit', function(e){
		
		var theSetting = $(this).data('setting')
		,	theValue = $('.input_'+theSetting).val()
		,	saveText = $(this).find('.saving-confirm');

		$.ajax({
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
});
// End AJAX Uploading


/*
 * ###########################
 *   jQuery Extension
 * ###########################
 */

$.fn.center = function ( relative_element ) {

    this.css("position","absolute");
    this.css("top", ( $(window).height() - this.height() ) / 4+$(window).scrollTop() + "px");
    this.css("left", ( $(relative_element).width() - this.width() ) / 2+$(relative_element).scrollLeft() + "px");
    return this;
}

$.fn.exists = function(){return $(this).length>0;}

}(window.jQuery);