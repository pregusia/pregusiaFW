
Site.addUIAdapter(function(root) {
	
	$(root).find('.ui-widget-IntegerInput input').each(function(){
		$(this).numeric();
	});
	
});
