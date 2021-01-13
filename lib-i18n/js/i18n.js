
Site.addStartFunction(function() {
	
	$('.ui-widget-I18NStringInput').each(function(){
		$(this).find('.dropdown-menu a').click(function(){
			var lang = $(this).data('lang');
			var widget = $(this).closest('.ui-widget-I18NStringInput');
			var btn = $(widget).find('.dropdown-toggle');
			
			$(btn).text(lang);
			if ($(btn).attr('aria-expanded') == 'true') {
				$(btn).dropdown("toggle");
			}
			
			$(widget).find('input').each(function(){
				if ($(this).data('lang') == lang) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
			
			return false;
		});
		
		$(this).find('.dropdown-menu a').first().click();
	});
	
	$('.ui-widget-I18NTextInput').each(function(){
		$(this).find('.dropdown-menu a').click(function(){
			var lang = $(this).data('lang');
			var widget = $(this).closest('.ui-widget-I18NTextInput');
			var btn = $(widget).find('.dropdown-toggle');
			
			$(btn).text(lang);
			if ($(btn).attr('aria-expanded') == 'true') {
				$(btn).dropdown("toggle");
			}
			
			$(widget).find('textarea').each(function(){
				if ($(this).data('lang') == lang) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
			
			return false;
		});
		
		$(this).find('.dropdown-menu a').first().click();
	});
	
	
	
});
