!function ($) {

	$(document).ready(function() {
		$.plConfigData.init()
	})

$.plConfigData = {
	init: function() {
		var check = window.plconfigfile || false
		
		if( check ) {
			$.plConfigData.show()
		}
	}

,	cancelled: function() {
	var args = {
			mode: 'settings'
		,	run: 'import_from_child_cancelled'
		,	confirm: false
		,	refresh: false
		, 	log: true
	}
	var response = $.plAJAX.run( args )
}

	
,	show: function() {
		
		var confirmText = '<h3>Holy Shit!</h3><p>Looks like this theme author was kind enough to include some demo data!!<br />Click OK to import it right now!</p>'
		,	savingText = $.pl.lang("Importing From Child Theme")
		,	refreshText = $.pl.lang("Successfully Imported. Refreshing page")
		,	theAction = 'import_from_child'
		var args = {
				mode: 'settings'
			,	run: theAction
			,	confirm: true
			,	confirmText: confirmText
			,	savingText: savingText
			,	refresh: true
			,	refreshText: refreshText
			, 	log: true
			,	onFalse: $.plConfigData.cancelled
		}
		var response = $.plAJAX.run( args )
	}
	
}

}(window.jQuery);