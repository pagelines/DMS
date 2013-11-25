!function ($) {


	/*
	 * Data/Settings handling functions.
	 */
	$.plDatas = {

		setElementDelete: function( deleted ){
			
			var that = this
			,	uniqueID = deleted.data('clone')
			
			deleted.find("[data-clone]").each(function(){
				that.setElementDelete( $(this) )
			})
			
			// recursive
			deleted.remove()

			if( plIsset($.pl.data.local[ uniqueID ]) )
				delete $.pl.data.local[ uniqueID ]
				
			if( plIsset($.pl.data.type[ uniqueID ]) )
				delete $.pl.data.type[ uniqueID ]
				
			if( plIsset($.pl.data.global[ uniqueID ]) )
				delete $.pl.data.global[ uniqueID ]
		
		}
		
		, handleNewItemData: function( cloned ){

			var that = this
			,	scope = plItemScope( cloned )
			,	newUniqueID = that.newPageItemData( cloned, 'clone', scope ) // recursive function
			
			cloned
				.find('.tooltip')
				.removeClass('in')
			
			$.plSave.save({ 
				  run: 'scope'
				, store: $.pl.data[scope]
				, scope: scope 
			})
			
			return newUniqueID

		}
		
		, newPageItemData: function( element, mode, scope ){
			
			var that = this
			,	mode = mode || 'clone'
			,	scope = scope || 'local'
			,	oldUniqueID = element.data('clone')
			, 	newUniqueID = plUniqueID()
			
			// Recursion
			element.find("[data-clone]").each(function(){
				that.newPageItemData( $(this), mode, scope )
			})
			
			// Set element meta for mapping
			element
				.attr('data-clone', newUniqueID)
				.data('clone', newUniqueID)
				
			// Copy and move around meta data
			var oldDatas = ( plIsset( $.pl.data[ scope ][ oldUniqueID ])) ? $.pl.data[ scope ][ oldUniqueID ] : false
			
			if( oldDatas )
				$.pl.data[ scope ][ newUniqueID ] = $.extend({}, oldDatas) // must clone the element, not just assign as they stay connected
			
			// If unlocking a section, pull for custom page data	
			if( mode == 'unlock' ){
				
				var customDat 	= ( plIsset( $.pl.data.custom[ oldUniqueID ])) ? $.pl.data.custom[ oldUniqueID ] : false
				
				if( customDat ){
					$.pl.data[scope][ newUniqueID ] = $.extend({}, customDat) // must clone the element, not just assign as they stay connected
				}
				
			}
			
			// Handle options configuration
			var	theOpts 	= ( plIsset( $.pl.config.opts[ oldUniqueID ])) ? $.pl.config.opts[ oldUniqueID ] : ''
			
			$.pl.config.opts[ newUniqueID ] = theOpts
			
			return newUniqueID
		}


	}



}(window.jQuery);