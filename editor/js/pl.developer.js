!function ($) {


	/*
	 * Developer Functions
	 */
	$.plDev = {
		
		init: function(){
			
			//$('.plprint-container').hide()
		
			$('[data-tab-action="dev_log"]').on('click', function(){
				
				
				//$('.current-panel .tab-panel').css('background', 'red')
			})
			
			$( ".panel-dev" ).on( "tabsactivate tabscreate", function( event, ui ) {
				
				var theTab = ( plIsset(ui.newTab) ) ? ui.newTab : ui.tab
				,	tabMeta = theTab.attr('data-tab-meta') || ''
				, 	tabAction = theTab.attr('data-tab-action') || ''
				,	tabPanel = $("[data-panel='"+tabAction+"']")
				
				if( theTab.hasClass('tab-dev_log') ){
					
					if( $('.plprint-container').length != 0 ){
						
						var plprints = ''
						
						$('.plprint-container').each( function(){
							plprints += '<div class="alert">Print</div>'
							plprints += $(this).html()
						})
						
						$('body').on('panel-setup', function(){
							tabPanel.find('.panel-tab-content').html( plprints )
						
						})
						
					}
					
					
				}
				
				
				
			} );
		}

	}



}(window.jQuery);