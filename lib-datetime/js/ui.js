
Site.addUIAdapter(function(root) {
	
	$(root).find('.ui-widget-DateInput').each(function(){
		if ($(this).hasClass('ui-widget-DateInput-mode-full')) {
			$(this).find('input').datetimepicker({
		    	format: 'YYYY-MM-DD',
		    	allowInputToggle: true,
		    	showClear: true,
		    });			
		}
		if ($(this).hasClass('ui-widget-DateInput-mode-monthyear')) {
			$(this).find('input').datetimepicker({
		    	format: 'YYYY-MM',
		    	allowInputToggle: true,
		    	showClear: true,
		    	viewMode: "years", 
		    });			
		}
	});
	
	$(root).find('.ui-widget-TimeInput input').datetimepicker({
    	format: 'HH:mm:SS',
    	allowInputToggle: true,
    	showClear: true,
    });

	$(root).find('.ui-widget-DateAndTimeInput input').datetimepicker({
    	format: 'YYYY-MM-DD HH:mm:SS',
    	allowInputToggle: true,
    	showClear: true,
    });
	
	$(root).find('.ui-widget-DatesRangeInput').each(function(){
		$(this).find('.ui-widget-DatesRangeInput-input-start').datetimepicker({
        	format: 'YYYY-MM-DD',
        	allowInputToggle: true,
        	showClear: true,
        });
		$(this).find('.ui-widget-DatesRangeInput-input-stop').datetimepicker({
        	format: 'YYYY-MM-DD',
        	allowInputToggle: true,
        	showClear: true,
        });
		
	});
	
});
