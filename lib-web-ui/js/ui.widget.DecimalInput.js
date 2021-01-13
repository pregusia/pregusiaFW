
Site.addUIAdapter(function(root) {
	
	$(root).find('.ui-widget-DecimalInput input').each(function(){
        precision = parseInt($(this).data('precision'));
        if (precision > 0) {
        	$(this).numeric({ decimal : ".",  negative : false, scale: precision });
        }
	});
	
});
