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
		
		$('.pl-credit').show()
		
		
	})
	
	$(window).load(function() {
		$.plCommon.plVerticalCenter('.pl-centerer', '.pl-centered')
		$('.pl-section').on('plresize', function(){
			$.plCommon.plVerticalCenter('.pl-centerer', '.pl-centered')
		})
	})
	
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
						, smoothHeight: true
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
			    } else {
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