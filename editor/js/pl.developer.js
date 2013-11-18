!function ($) {


	/*
	 * Developer Functions
	 */
	$.plDev = {
		
		init: function(){
			
			$('.plprint-container').hide()
		
			
			$( "body" ).on( "pl-tab-build", function( event, tab ) {
				
				var theTab = tab
				,	tabMeta = theTab.attr('data-tab-meta') || ''
				, 	tabAction = theTab.attr('data-tab-action') || ''
				,	tabPanel = $("[data-panel='"+tabAction+"']")
				, 	output = ''
				
				
				if( theTab.hasClass('tab-dev_log') ){
					
					if( $('.plprint-container').length != 0 ){
						
						$('.plprint-container').each( function(){
							output += '<div class="alert">Print</div>'
							output += $(this).html()
						})
						
						
						
					}
					
				} else if( theTab.hasClass('tab-dev-page') ){
					
					var tbl = ''
					for ( var key in $.plDevData.php ) {
						if ($.plDevData.php.hasOwnProperty(key)) {
							var obj = $.plDevData.php[key];


							tbl += sprintf( '<tr><th>%s</th><td>%s %s</td><td>%s</td></tr>', obj.title, obj.num, obj.label, obj.info )

						}
					}
					
					output += sprintf( '<table class="data-table" >%s</table>', tbl )
					
				}
				
				if( output != ''){
					
					$('body').on('panel-setup', function(){
						tabPanel.find('.panel-tab-content').html( output )
					
					})
					
				}
				
			} );
		}

	}



}(window.jQuery);