!function ($) {

	// --> Initialize
	$(document).ready(function() {

		$.plAdminMeta.init()

	})


	/*
	 * Data/Settings handling functions.
	 */
	$.plAdminMeta = {
		
		init: function(){
			
			$('#post-formats-select input').change( $.plAdminMeta.checkFormat );
			$('.wp-post-format-ui .post-format-options > a').click( $.plAdminMeta.checkFormat );

			$(window).load(function(){
				$.plAdminMeta.checkFormat();
			})
			
		}
		
		, checkFormat: function( ){
			
			var format = $('#post-formats-select input:checked').attr('value');

			//only run on the posts page
			if(typeof format != 'undefined'){


				$('#post-body div[id^=nectar-metabox-post-]').hide();
				$('#post-body #nectar-metabox-post-'+format+'').stop(true,true).fadeIn(500);

			}
			
		}
	}



}(window.jQuery);