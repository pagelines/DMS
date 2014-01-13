!function ($) {

	$(document).ready(function() {
		
		$('.pl-slider-container').each(function(){
			
			
			$(this).find('.pl-slider').revolution({
				delay: 9000,
				onHoverStop:"on",
				hideThumbs: 10,
				navigationType:"bullet",
				navigationArrows:"solo",
				navigationStyle:"square",
				navigationHAlign:"center",
				navigationVAlign:"bottom",
				navigationHOffset:0,
				navigationVOffset:20,
				soloArrowLeftHalign:"left",
				soloArrowLeftValign:"center",
				soloArrowLeftHOffset:0,
				soloArrowLeftVOffset:0,
				soloArrowRightHalign:"right",
				soloArrowRightValign:"center",
				soloArrowRightHOffset:0,
				soloArrowRightVOffset:0,
				touchenabled:"on",
				stopAtSlide:-1,
				stopAfterLoops:-1,
				hideCaptionAtLimit:0,
				hideAllCaptionAtLilmit:0,
				hideSliderAtLimit:0,
				fullWidth:"on",
				shadow:0,
				fullWidth:"off",
				fullScreen:"on",
				fullScreenOffsetContainer: ""

			}).slideDown()
			
			$(this).find('.tp-leftarrow').html('<i class="icon-angle-left"></i>')
			$(this).find('.tp-rightarrow').html('<i class="icon-angle-right"></i>')
		})
		
	})
	

}(window.jQuery);