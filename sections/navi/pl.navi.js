!function ($) {

	$(document).ready(function() {
		
		$('.sf-menu').each(function(){
		
			$(this).superfish({
				 delay: 800,
				 speed: 'fast',
				 speedOut: 'fast',             
				 animation:   {opacity:'show'}
			});
			
			var offset = $(this).data('offset') || false
			
			if( offset ){
				$(this)
					.find('> li > ul')
					.css('top', offset)
			}
			
			$(this).find('.megamenu').each(function(){
				var cols = $(this).find('> .sub-menu > li').length
				
				$(this).addClass('mega-col-'+cols)
			})
			
		})
		
		
		
	})
	

}(window.jQuery);