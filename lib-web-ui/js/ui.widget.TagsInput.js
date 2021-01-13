
Site.addUIAdapter(function(root) {
	
	$(root).find('.ui-widget-TagsInput').each(function(){
		var enumerable = $(this).data('enumerable');
		var inputName = $(this).data('inputname');
		var inputType = $(this).data('inputtype');
		
		if (inputType == 'select') {
			$(this).find('select').select2({
				tags: true
			});
		}
		if (inputType == 'suggest') {
			
			if (enumerable) {
				$(this).find('select').select2({
					tags: true,
					tokenSeparators: [",", " "],
					multiple: true,
					minimumInputLength: 1,
					ajax: {
						url: '/ui/suggest/search/' + enumerable,
						dataType: "json",
						delay: 250,
						cache: true,
					}
				});				
			} else {
				$(this).find('select').select2({
					tags: true,
					tokenSeparators: [",", " "],
					multiple: true,
					minimumInputLength: 1,
				});			
			}
		}
	});
	
});
