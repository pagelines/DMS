!function ($) {

$.plCode = {

	activateLESS: function( ){

		var lessText = $(".custom-less")
		,	scriptsText = $(".custom-scripts")


		if( !lessText.hasClass('mirrored') ){

			var editorDefaultObject = {
					
					onKeyEvent: function(instance, e){
console.log('yo')
					lessText.val( instance.getValue() )
					var theCode = lessText.parent().formParams()

					$.pl.data.global = $.extend(true, $.pl.data.global, theCode)
				
					// Keyboard shortcut for live LESS previewing
					if(e.type == 'keydown' && e.which == 13 && (e.metaKey || e.ctrlKey) ){
						
						var mixinsFile = $('#pl-custom-less').data('mixins')
						
						$('#pl-custom-less')
							.text(instance.getValue())
							.prepend( sprintf('@import "%s";', mixinsFile))
							.attr('type', 'text/less')
							
						
						less.refresh()

					}
				}
			}

			$.extend(editorDefaultObject, CMCustomCSS)

			var editor = CodeMirror.fromTextArea( lessText.addClass('mirrored').get(0), editorDefaultObject)

			editor.on('blur', function(instance, changeObj){

				$.plAJAX.saveData(	)
			})


		}


	}

	, 	activateScripts: function(){

		var lessText = $(".custom-less")
		,	scriptsText = $(".custom-scripts")

		if( !scriptsText.hasClass('mirrored') ){

			var editor2 = CodeMirror.fromTextArea( scriptsText.addClass('mirrored').get(0), {
					lineNumbers: true
				,	mode: 'htmlmixed'
				, 	lineWrapping: true
				, 	onKeyEvent: function(instance, e){

					scriptsText.val( instance.getValue() )
					var theCode = scriptsText.parent().formParams()

					$.pl.data.global = $.extend(true, $.pl.data.global, theCode)


				}

			})
			editor2.on('blur', function(instance, changeObj){

				$.plAJAX.saveData(	)
			})

		}
	}

}

}(window.jQuery);