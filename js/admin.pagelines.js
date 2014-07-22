/**
 * Framework Javascript Functions
 * Copyright (c) PageLines 2008 - 2013
 *
 * Written By PageLines
 */

!function ($) {
	
$(document).ready(function(){
	
	$.plOptions = {
		init: function(){

			that = this
			
			that.specialOptions()
	
		}
		
		, 	specialOptions: function(){
			
			var that = this
			
			// Use WP image upload script
			that.imageUploaders()
			
			// chosen selectors
			$('.chosen-select').chosen()
			
			// color pickers, using WP colorpicker API
			$('.pl-colorpicker').wpColorPicker().addClass('is-ready')
		
			// use hidden inputs for checkboxes (0 or 1)
			$('.checkbox-input').on('change', function(){
			
				var checkToggle = $(this).prev()

				if( $(this).is(':checked') )
				    checkToggle.val(1)
				else
				    checkToggle.val(0)
			})
				
				
			$('.pl_script_input').each( function(i){
				
				var cm_mode = $(this).data('mode')
				,	cm_config = $.extend( {}, cm_base_config, { mode : cm_mode } )
				
				var theEditor = CodeMirror.fromTextArea($(this).get(0), cm_config)
			
				$(this).parent().addClass('is-ready')
			
			})
		
			
		}
		,	imageUploaders: function(){
		
				var custom_uploader

				$('.image_upload_button').click(function(e) {

					e.preventDefault()
					
					var button = $(this)
					,	theOption = $(this).parent().parent().parent()

					//If the uploader object has already been created, reopen the dialog
					if (custom_uploader) {
						custom_uploader.open()
						return
					}

					//Extend the wp.media object
					custom_uploader = wp.media.frames.file_frame = wp.media({
						title: 'Choose Image',
						button: {
							text: 'Choose Image'
						},
						multiple: false
					});

					//When a file is selected, grab the URL and set it as the text field's value
					custom_uploader.on('select', function() {
					
						attachment = custom_uploader.state().get('selection').first().toJSON()
					
						theOption
							.find('.upload_image_option')
							.val( attachment.url )
								
						theOption.find('.the_preview_image')
							.attr('src', attachment.url)
							
					
					})

					//Open the uploader dialog
					custom_uploader.open()

				});
						
		} 
	}
	
	$.plOptions.init()

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