!function ($) {

	$.areaControl = {

        toggle: function(btn) {

			if(!jQuery.areaControl.isActive){

				$('body')
					.addClass('area-controls')
					.find('area-tag')
					
				//.effect('highlight')

				btn.addClass('active')

				jQuery.areaControl.isActive = true

				jQuery.areaControl.listen()

			} else {
				btn.removeClass('active')
				jQuery.areaControl.isActive = false
				$('body').removeClass('area-controls')

			}

		}

		, listen: function() {
			$('.btn-region').tooltip({placement: 'right'})
			
			$('.area-control').tooltip({ placement: 'top' })

			$('.area-control:not(".pro-only-disabled")').on('click.areaControl', function(e){
				e.preventDefault()

				var action = $(this).data('area-action')

				if(action == 'clone' ){
					$.areaControl.areaTools('clone', $(this))
					
				} else if (action == 'settings' ){
					$.areaControl.areaSettings($(this))

				} else if (action == 'delete' ){
					$.areaControl.deleteArea($(this))

				} else if (action == 'save' ){
					$.areaControl.saveArea($(this))

				}
			})


		}
		
		, saveArea: function( btn ){
			
			var that = this
			,	theArea = btn.closest('.pl-area')
			,	theID = theArea.attr('id')
			,	message = sprintf('<div class="ttl">Save New User Section</div><form class="modal-form" data-area="%s"><input name="user-section-name" placeholder="Enter Section Name..." type="text" val="" /></form>', theID)
		
		
			bootbox.confirm(
				message
				, '<i class="icon-remove"></i> Cancel'
				, '<i class="icon-save"></i> Save Section'
				, function( result ){
					if( result == true ){

						var areaID = $('.modal-form').data('area')
						,	theArea = $('#'+areaID)
						, 	settings = {}
						,	form = $('.modal-form').formParams()
						,	args = {
									mode: 'set_user_section'
								,	run: 'save'
								, 	log: true
								,	config: $.plMapping.getAreaMapping( theArea )
							
							}
						,	args = $.extend({}, args, form) // add form fields to post

					
						var response = $.plAJAX.run( args )
					
					}
				})
		}
		
		, areaTools: function( action, btn ){

			var that = this
			,	theArea = btn.closest('.pl-area')
				
			if( action == 'clone'){
				
				var	cloned = theArea.clone( false )

				cloned
					.insertAfter( theArea )
					.hide()
					.slideDown()
					
				cloned.find('.area-control').data('tooltip', false).tooltip('destroy')
				cloned.find('.area-control').tooltip({placement: 'top'})

				$.pageBuilder.handleCloneData( cloned )
				
				$.pageBuilder.reloadAllEvents()
			}

		}

		
		, areaSettings: function( btn ){

			var that = this
			,	theArea = btn.closest('.pl-area')
			,	theID = theArea.attr('id')
			,	object = theArea.data('object') || false

			var config	= {
					sid: theArea.data('sid')
					, sobj: theArea.data('object')
					, clone: theArea.data('clone')
					, uniqueID: theArea.data('clone')
					, scope: ( theArea.parents(".template-region-wrap").length == 1 ) ? 'local' : 'global'
				}

			$('body').toolbox({
				action: 'show'
				, panel: 'section-options'
				, info: function(){

					$.optPanel.render( config )

				}
			})

		}


		, deleteArea: function( btn ){

			var currentArea = btn.closest('.pl-area')
			, 	confirmText = $.pl.lang("<h3>Are you sure?</h3><p>This action will delete this area and all its elements from this page.</p>")

			bootbox.confirm( confirmText, function( result ){
				if(result == true){

					currentArea.slideUp(500, function(){
						$.pageBuilder.setElementDelete( currentArea )
						$.pageBuilder.reloadConfig( {location: 'area-delete'} )
					
					})


				}

			})



		}

	

	}
}(window.jQuery);