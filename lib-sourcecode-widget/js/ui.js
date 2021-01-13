
Site.addUIAdapter(function(root) {
	
	$(root).find('.ui-widget-SourceCodeTextInput').each(function(){
		var mode = $(this).data('mode');
		var theme = $(this).data('theme');
		var input = $(this).find('textarea');
		
		var container = $('<div></div>');
		$(container).css({
			position: 'absolute',
			width: $(input).width(),
			height: $(input).height(),
		});
		$(container).attr('class', $(input).attr('class'));
		$(container).insertBefore(input);

		$(input).css('visibility','hidden');
		
		var editor = ace.edit(container.get(0));
		editor.setTheme("ace/theme/" + theme);
		editor.renderer.setShowPrintMargin(false);
		editor.getSession().setMode("ace/mode/" + mode);
		editor.getSession().setValue($(input).val());
		
		editor.on('change',function(){
			$(input).val(editor.getSession().getValue());
		});
	});
	
});
