ff.jQueryPlugins_checkbox = function () {
	jQuery('input:checkbox:not([safari])').checkbox({
		empty: ff.base_path + '/themes/library/plugins/jquery.checkbox/empty.png'
	});

	jQuery('input[safari]:checkbox').checkbox({
		cls:'jquery-safari-checkbox'
		, empty: ff.base_path + '/themes/library/plugins/jquery.checkbox/empty.png'
	});
}

ff.pluginAddInitLoad("ff.ffPageNavigator", function () {
	ff.ffPageNavigator.addEvent({
		"event_name" : "onGoToPage"
		, "func_name" : ff.jQueryPlugins_checkbox
	});

});

ff.pluginAddInitLoad("ff.ajax", function () {
	ff.ajax.addEvent({
		"event_name" : "onEmptyQueue"
		, "func_name" : ff.jQueryPlugins_checkbox
	});
});

jQuery(document).ready(function() {
	ff.jQueryPlugins_checkbox();
});
