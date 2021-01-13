
<div id="{$Widget['elementID']}_main" class="form-group ui-widget-SuggestInput ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}" data-enumerable="{$Widget['enumerableRef']}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
		<div class="input-group">
			<span class="input-group-addon"> <i class="fa fa-ellipsis-h"></i> </span>
			<select id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}" name="{$Widget['name']}" tabindex="{$Widget['index']}">
				<option value="{$Widget['value']}">{$Widget['valueText']}</option>
			</select> {$Widget['suffix']}
		</div>
		
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>
