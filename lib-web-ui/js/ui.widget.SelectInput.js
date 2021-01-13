
Site.addUIAdapter(function(root) {
	
	var renderFunc = function(obj) {
		var el = obj.element;
		var out = $('<span></span>');
		
		if ($(el).data('html')) {
			$(out).html($(el).data('html'));
		} else {
			$(out).text(obj.text);
		}
		return out;
	};
	
	$(root).find('.ui-widget-SelectInput').each(function(){
		$(this).find('select').select2({
			templateResult: renderFunc,
			templateSelection: renderFunc
		});
	});
	
});
