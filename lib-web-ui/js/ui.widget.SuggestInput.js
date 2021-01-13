
Site.addUIAdapter(function(root) {
	
	$(root).find('.ui-widget-SuggestInput').each(function(){
		var enumerable = $(this).data('enumerable');
		
		$(this).find('select').select2({
			ajax: {
				url: '/ui/suggest/search/' + enumerable,
				dataType: 'json',
				delay: 250,
				cache: true,
			},
			allowClear: true,
			placeholder: '[EMPTY]',
			minimumInputLength: 0,
		});
	});
	
});
