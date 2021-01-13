
<div id="{$Widget['elementID']}_main" class="form-group ui-widget-FloatInput ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
		{if $Widget['hasSuffix'] || $Widget['hasPrefix']}
		<div class="input-group">
			{if $Widget['hasPrefix']} <span class="input-group-addon"> {$Widget['prefix']} </span> {endif}
			<input id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}" type="text" name="{$Widget['name']}" tabindex="{$Widget['index']}" value="{$Widget['value']}" />
			{if $Widget['hasSuffix']} <span class="input-group-addon"> {$Widget['suffix']} </span> {endif}
		</div>
		{else}
		<input id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}" type="text" name="{$Widget['name']}" tabindex="{$Widget['index']}" value="{$Widget['value']}" />
		{endif}
		
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>
