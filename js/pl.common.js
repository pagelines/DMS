!function ($) {


	

	// --> Initialize
	$(document).ready(function() {
		
		$(document).trigger( 'sectionStart' )
		
		$.plCommon.init()
		$.plMobilizer.init()
	
		$(".fitvids").fitVids(); // fit videos
	
		$.plAnimate.initAnimation()
		
		$.plNavigation.init()
		
		$.plParallax.init()
		
		$.plLove.init()
		
		$.plGallery.init()
		
		$.plVideos.init()
		
		$('.pl-credit').show()
		
		
	})
	
	$(window).load(function() {
		$.plCommon.plVerticalCenter('.pl-centerer', '.pl-centered')
		$('.pl-section').on('plresize', function(){
			$.plCommon.plVerticalCenter('.pl-centerer', '.pl-centered')
		})
	})
	
	$.plVideos = {
		init: function(){
			
			$(window).resize(function () { 
				
				$(".bg-video").each(function () {
				
				
					
					var vid = $(this)
					, 	canvas = vid.closest('.bg-video-canvas')
					,	viewport = vid.parent()
					
					var vW = this.videoWidth
					, 	vH = this.videoHeight
					
				
					$.plVideos.resizeToCover( vid, canvas, viewport, vH, vW )
						
					

				})
				
			})
			
			$(window).trigger('resize')
			
			$('.bg-video-canvas').on('plresize', function(){
				$(window).trigger('resize')
			})
			
		}
		
		, resizeToCover: function( vid, canvas, viewport, vH, vW ){
			
			
			var canvasWidth	= canvas.width()
			, 	canvasHeight = canvas.height()
			
		    viewport.width( canvasWidth )
		    viewport.height( canvasHeight )

		    var scale_h = canvasWidth / vW
		    var scale_v = canvasWidth / vH
		    var scale = scale_h > scale_v ? scale_h : scale_v

		    // don't allow scaled width < minimum video width
		    if (scale * vW < 300) {
				scale = 300 / vW 
			} 

		    // now scale the video
		    vid.width(scale * vW);
		    vid.height(scale * vH);
		
		    // and center it by scrolling the video viewport
		    viewport.scrollLeft(( vid.width() - canvasWidth ) / 2);
		    viewport.scrollTop(( vid.height() - canvasHeight ) / 2);
		  
		}
	}
	
	$.plGallery = {
		init: function(){
			//gallery
			$('.flex-gallery').each(function(){

				var gallery = $(this)
				,	animate = gallery.data('animate') || true
				,	transition = gallery.data('transition') || 'fade'

				gallery.imagesLoaded( function(instance){

					gallery.flexslider({
				        animation: transition
						, smoothHeight: false
						, slideshow: animate
				    })
					
					if( gallery.find('.slides li').length <= 1 ){
						gallery.find('.flex-direction-nav').hide()
					}

					////gallery slider add arrows
					$('.flex-gallery .flex-direction-nav li a.flex-next').html('<i class="icon-angle-right"></i>')
					$('.flex-gallery .flex-direction-nav li a.flex-prev').html('<i class="icon-angle-left"></i>')

				});

			});
			
			
			
		}
	}
	

	
	$.plLove = {
		
		init: function(){
			
			$('body').on('click','.pl-love', function() {

					var loveLink = $(this)
					,	id = loveLink.attr('id')

					if( loveLink.hasClass('loved') ) 
						return false

					if( loveLink.hasClass('inactive') ) 
						return false;

					var passData = {
						action: 'pl_love', 
						loves_id: id
					}

					$.post( plLove.ajaxurl, passData, function( data ){

						loveLink
							.find( 'span' )
							.html( data )
							.end()
								.addClass( 'loved' )
								.attr( 'title', 'You already love this!' )
							.end()
								.find( 'span')
								.css({ 'opacity': 1, 'width':'auto' } )

					});

					loveLink
						.addClass('inactive')

					return false
			})
		}
		
		
	}
	
	$.plNavigation = {
		init: function(){
			
			var that = this
			
			that.initDrops()
		}
		, initDrops: function(){
			
			var a = 1
			
			$(".pl-dropdown > li > ul").each(function(){

				var b = ""

				$(this).addClass("dropdown-menu");

				if( $(this).siblings("a").children("i").length===0 ){
					b = ' <i class="icon-caret-down"></i>'
				}

				$(this).siblings("a")
					.addClass("dropdown-toggle")
					.attr( "href", "#m" + a )
					.attr("data-toggle","dropdown")
					.append(b)
					.parent()
					.attr( "id", "m" + a++ )
					.addClass("dropdown")

				$(this)
					.find('.sub-menu')
					.addClass("dropdown-menu")
					.parent()
					.addClass('dropdown-submenu')
			})

			$(".dropdown-toggle").dropdown()

		}
	}
	
	$.plParallax = {
	

		init: function(speed){
			
			var that = this

			if( $('.pl-parallax').length >= 1){
				
				$('.pl-parallax .pl-area-wrap').each(function(element){
					$(this).parallax('50%', .3, true, 'background')
				})
			}
			
			if( $('.pl-scroll-translate').length >= 1){

				$('.pl-scroll-translate .pl-area-wrap').each(function(element){
					
					$(this).parallax('50%', .4, true, 'translate')
				})
			}
			
			

		}
	
	}
	
	$.plMobilizer = {
		
		init: function(){
			var that = this
			
			that.mobileMenu()
		}
		
		, mobileMenu: function(){
			var that = this
			, 	theBody = $('body')
			, 	menuToggle = $('.mm-toggle')
			,	siteWrap = $('.site-wrap')
			, 	mobileMenu = $('.pl-mobile-menu')
			
		//	mobileMenu.css('max-height', siteWrap.height()-10)
			
			menuToggle.on('click.mmToggle', function(e){
				
				e.stopPropagation()
			//	mobileMenu.css('max-height', siteWrap.height())
				
				if( !siteWrap.hasClass('show-mobile-menu') ){
					
					siteWrap.addClass('show-mobile-menu')
					mobileMenu.addClass('show-menu')
					
					
					$('.site-wrap, .mm-close').one('click touchstart', function(){
						siteWrap.removeClass('show-mobile-menu')
						mobileMenu.removeClass('show-menu')
					})
					
					
					
				} else {
					siteWrap.removeClass('show-mobile-menu')
					mobileMenu.removeClass('show-menu')
					
				}
			
			})
			
		
			
		}
		
	}

	$.plAnimate = {
		
		initAnimation: function(){
			
			var that = this
						
			$.plAnimate.plWaypoints()
			
			$.plAnimate.doHoverFlag()
		}
		
		// adds a hover class on hover to items that have the flag
		, doHoverFlag: function(){
			
			$( ".pl-hover-flag" ).hover(
				function() {
			    	$( this ).addClass( "pl-hover" )
			  	}, function() {
			    	$( this ).removeClass( "pl-hover" )
			  	}
			)
			
		}
		
		, plWaypoints: function(selector, options_passed){
			
			var defaults = { 
					offset: '85%' // 'bottom-in-view' 
					, triggerOnce: true
				}
				, options  = $.extend({}, defaults, options_passed)
				, delay = 150
				
			$('.pl-animation-group')
				.find('.pl-animation')
				.addClass('pla-group')
				
			$('.pl-animation-group').each(function(){
				
				var element = $(this)

				element.waypoint(function(direction){
				 	$(this)
						.find('.pl-animation')
						.each( function(i){
							var element = $(this)
							
							setTimeout(
								function(){ 
									element.addClass('animation-loaded') 
								}
								, (i * 250)
							);
						})

				}
				, { offset: '80%' 
					, triggerOnce: true
				})
			})

			$('.pl-animation:not(.pla-group)').each(function(){
				
				var element = $(this)

				element.waypoint(function(direction){
					
					 	$(this)
							.addClass('animation-loaded')
							.trigger('animation_loaded')

					}
					, { offset: '85%' 
					, triggerOnce: true
				})

			
			})
		}
		
	}

	$.plCommon = {

		init: function(){
			var that = this
			that.setHeight()

			$.resize.delay = 100 // resize throttle

			var fixedTop = $('.pl-fixed-top')
			, 	fixedOffset = fixedTop[0].offsetTop
			
			fixedTop.on('plresize', function(){
				that.setHeight()
			})
			
			$(document).on('ready scroll', function() {
			    var docScroll = $(document).scrollTop()

			    if (docScroll >= fixedOffset) {
			        fixedTop.addClass('is-fixed');
			 		fixedTop.removeClass('is-not-fixed');
			    } else {
					fixedTop.addClass('is-not-fixed');
			        fixedTop.removeClass('is-fixed')
			    }

			})
			
			$('.pl-make-link').on('click', function(){
				var url = $(this).data('href') || '#'
				, 	newWindow = $(this).attr('target') || false
				
				if( newWindow )
					window.open( url, newWindow )
				else
					window.location.href = url
			
			})
			
			that.handleSearchfield()
			

		}
		
		, handleSearchfield: function(){
			
			$('.btn-search').on('click', function(e){
				
				$(this).parent().find( '.searchfield' ).focus()
				
			})
			
			$('.searchfield').on('focus', function(e){
				
				$(this).parent().parent().addClass('has-focus')
					
			}).on( 'blur', function(e){
				
				$(this).parent().parent().removeClass('has-focus')
			
			})
			
		}

		, setHeight: function(){

			var height = $('.pl-fixed-top').height()

			$('.fixed-top-pusher').height(height)

		}
		
		, plVerticalCenter: function( container, element, offset ) {

			jQuery( container ).each(function(){

				var colHeight = jQuery(this).height()
				,	centeredElement = jQuery(this).find( element )
				,	infoHeight = centeredElement.height()
				, 	offCenter = offset || 0

				centeredElement.css('margin-top', ((colHeight / 2) - (infoHeight / 2 )) + offCenter )
			})

		}
		

	}

}(window.jQuery);