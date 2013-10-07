!function ($) {

	PL_Lessify = function () {

		this.compiler = new ( less.Parser )
		
		this.core_less = $('#pl-less-inline')
		
		this.setUIBindings()
		
		this.loadCSS()

		$(document).trigger('pl-less-loaded')
	}
	PL_Lessify.prototype = {

		loadCSS : function(){
			
			//var core = this.core_less.text()
			

			
			   
		
			var code = $('#pl-less-vars').text()
			code += $('#pl-less-tools').text()
			
			code += $('#pl-less-core').text()
			code += $('#pl-less-sections').text()
			
			var start = new Date();
		
			var CSS = this.compile( code )
			
			console.log(new Date() - start);
			
			$('#pagelines-draft-css').text( CSS )
	
		}

		, 	compile : function ( code ) {
				
				var compiled = ''
				this.compiler.parse( code, function ( err, tree ) {
					if ( err )
						return console.log( err )
					
					
					compiled = tree.toCSS()
					
				} )
				return compiled || ''
			}


		,	setUIBindings : function () {
				that = this

			}

		,	setEditorBindings : function ( mode, $area ) {
			that = this
			editor = this.editors[ mode ]

			// common bindings
			editor.on('blur', that.triggerSave )
			editor.on('change', function ( instance ) {
				// Update the content of the textarea.
				instance.save()
				// get data object
				dataobj = $area.parent().formParams();
				// extend
				$.pl.data.global = $.extend(true, $.pl.data.global, dataobj)
			})

			if ('less' === mode) {
				editor.on('keydown', function (instance, e) {
					if ( e.which == 13 && (e.metaKey || e.ctrlKey) ) {
						var code = instance.getValue()
						// update custom css
						$('#pagelines-custom').text( that.compile( code ) )
					}
				} )
			}
		}

		


	}

//	$.plLessify = new PL_Lessify
	$(document).ready(function() {
			$.plLessify = new PL_Lessify
		})

}( window.jQuery );