
if (typeof(UI) == 'undefined') UI = { };

UI.PropertiesMap = function(el) { this.ctor(el); };

$.extend(UI.PropertiesMap.prototype,{
	
	ctor: function(el) {
		this.el = el;
		this.inputName = $(el).data('inputname');
		this.nextNr = 100;
		
		$(this.el).find('.ui-widget-PropertiesMap-add-link').click($.proxy(function(){
			this.add(this.getAdderInputValue());
			this.clearAdderInputValue();
			return false;
		}, this));
					
		this.updateLinks();
	},
	
	updateLinks: function() {
		$(this.el).find('.ui-widget-PropertiesMap-delete-link').click(function(){
			$(this).closest('.input-group').remove();
			return false;
		});		
	},
	
	getAdderInputValue: function() {
		return {
			name: $('.ui-widget-PropertiesMap-adder .ui-widget-PropertiesMap-input-name',this.el).val(),
			value: $('.ui-widget-PropertiesMap-adder .ui-widget-PropertiesMap-input-value',this.el).val(),
		};
	},
	
	clearAdderInputValue: function() {
		$('.ui-widget-PropertiesMap-adder .ui-widget-PropertiesMap-input-name',this.el).val('');
		$('.ui-widget-PropertiesMap-adder .ui-widget-PropertiesMap-input-value',this.el).val('');
	},
	
	add: function(item) {
		if (item && item.name && item.value) {
			
			$div = $('<div class="input-group" style="margin-bottom: 10px;"></div>');
			
			$nameInput = $('<input class="form-control" type="text" />');
			$nameInput.attr('name', this.inputName + '_' + this.nextNr + '_name');
			$nameInput.val(item.name);
			$div.append($nameInput);
			
			$div.append($('<span class="input-group-addon"> = </span>'));
			
			$valueInput = $('<input class="form-control" type="text" />');
			$valueInput.attr('name', this.inputName + '_' + this.nextNr + '_value');
			$valueInput.val(item.value);
			$div.append($valueInput);
			
			$div.append($('<span class="input-group-btn"><button class="btn btn-danger btn-remove ui-widget-PropertiesMap-delete-link"><i class="fa fa-remove"></i>&nbsp;</button></span>'));
			
			
			$div.appendTo($(this.el).find('.ui-widget-PropertiesMap-items'));
			this.updateLinks();
			this.nextNr += 1;
		}
	},
	
});



Site.addUIAdapter(function(root) {
	$(root).find('.ui-widget-PropertiesMap').each(function(){
		this._ui_PropertiesMap = new UI.PropertiesMap(this);
	});
});
