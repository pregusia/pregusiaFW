
<div id="{$Widget['elementID']}_main" class="form-group ui-widget-StringInput ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
	
		{if $Widget['hasSuffix'] && $Widget['hasPrefix']}
		<div class="input-group">
			<span class="input-group-addon"> {$Widget['prefix']} </span>
			<input id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}" type="text" name="{$Widget['name']}" tabindex="{$Widget['index']}" value="{$Widget['value:html']}" />
			<span class="input-group-addon"> {$Widget['suffix']} </span>
		</div>
		{elseif $Widget['hasSuffix']}
		<div class="input-group">
			<input id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}" type="text" name="{$Widget['name']}" tabindex="{$Widget['index']}" value="{$Widget['value:html']}" />
			<span class="input-group-addon"> {$Widget['suffix']} </span>
		</div>		
		{elseif $Widget['hasPrefix']}
		<div class="input-group">
			<span class="input-group-addon"> {$Widget['prefix']} </span>
			<input id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}" type="text" name="{$Widget['name']}" tabindex="{$Widget['index']}" value="{$Widget['value:html']}" />
		</div>		
		{else}
		<input id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}" type="text" name="{$Widget['name']}" tabindex="{$Widget['index']}" value="{$Widget['value:html']}" />
		{endif}	
		
		
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>
