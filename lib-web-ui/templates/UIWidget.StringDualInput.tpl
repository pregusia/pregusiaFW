
<div id="{$Widget['elementID']}_main" class="form-group ui-widget-StringDualInput ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
	
		<div class="col-md-6">
			<input {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}" type="text" name="{$Widget['name']}-0" tabindex="{$Widget['index']}" value="{htmlspecialchars($Widget['value'][0])}" />
		</div>
		<div class="col-md-6">
			<input {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}" type="text" name="{$Widget['name']}-1" tabindex="{$Widget['index']}" value="{htmlspecialchars($Widget['value'][1])}" />
		</div>	
		
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>
