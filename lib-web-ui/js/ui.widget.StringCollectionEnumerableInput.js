
if (typeof(UI) == 'undefined') UI = { };

UI.StringCollectionEnumerableInput = function(el) { this.ctor(el); };

$.extend(UI.StringCollectionEnumerableInput.prototype,{
	
	ctor: function(el) {
		this.el = el;
		this.inputType = $(el).data('inputtype');
		this.inputName = $(el).data('inputname');
		this.enumerable = $(el).data('enumerable');
		
		$(this.el).find('.ui-widget-StringCollectionEnumerableInput-add-link').click($.proxy(function(){
			this.add(this.getAdderInputValue());
			return false;
		}, this));
					
		$(this.el).find('.ui-widget-StringCollectionEnumerableInput-add-all-link').click($.proxy(function(){
			var self = this;
			$('.ui-widget-StringCollectionEnumerableInput-adder select option',this.el).each(function(){
				self.add({
					value: $(this).val(),
					text: $(this).text()
				});
			});
			return false;
		}, this));
		
		this.updateLinks();
		
		if (this.inputType == 'select') {
			$(this.el).find('.ui-widget-StringCollectionEnumerableInput-adder select').select2();
		}
		if (this.inputType == 'suggest') {
			$(this.el).find('.ui-widget-StringCollectionEnumerableInput-adder select').select2({
				ajax: {
					url: '/ui/suggest/search/' + this.enumerable,
					dataType: 'json',
					delay: 250,
					cache: true,
				},
				minimumInputLength: 1,
			});
		}
		
	},
	
	updateLinks: function() {
		$(this.el).find('.ui-widget-StringCollectionEnumerableInput-delete-link').click(function(){
			$(this.parentNode).remove();
			return false;
		});		
	},
	
	getAdderInputValue: function() {
		if (this.inputType == 'select') {
			return {
				value: $('.ui-widget-StringCollectionEnumerableInput-adder select option:selected',this.el).val(),
				text: $('.ui-widget-StringCollectionEnumerableInput-adder select option:selected',this.el).text(),
			};
		}
		if (this.inputType == 'suggest') {
			return {
				value: $('.ui-widget-StringCollectionEnumerableInput-adder select option:selected',this.el).val(),
				text: $('.ui-widget-StringCollectionEnumerableInput-adder select option:selected',this.el).text(),
			};
		}
		return { value: '', text: '' }
	},
	
	exists: function(v) {
		res = { ret: false };
		$('.ui-widget-StringCollectionEnumerableInput-value',this.el).each(function(){
			if ($(this).val() == v) res.ret = true;
		});
		return res.ret;
	},
	
	add: function(item) {
		if (item.value && !this.exists(item.value)) {
			$div = $('<div class="btn-group"></div>');
			$div.append($('<button class="btn btn-default" type="button">' + item.text + '</button>'));
			
			$input = $('<input class="ui-widget-StringCollectionEnumerableInput-value" type="hidden" value="" />');
			$input.attr('name',this.inputName + '[]');
			$input.val(item.value);
			$div.append($input);
			
			$div.append($('<button class="btn btn-danger ui-widget-StringCollectionEnumerableInput-delete-link" type="button"><i class="fa fa-remove"></i>&nbsp;</button>'));
			
			$(this.el).find('.ui-widget-StringCollectionEnumerableInput-inner').append($div);
			$(this.el).find('.ui-widget-StringCollectionEnumerableInput-inner').append(' ');
			
			this.updateLinks();
		}
	},
	
});



Site.addUIAdapter(function(root) {
	$(root).find('.ui-widget-StringCollectionEnumerableInput').each(function(){
		this._ui_StringCollectionEnumerableInput = new UI.StringCollectionEnumerableInput(this);
	});
});
