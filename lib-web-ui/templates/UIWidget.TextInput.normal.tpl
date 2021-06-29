
<div id="{$Widget['elementID']}_main" class="form-group ui-widget-TextInput ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
		{if $Widget['Tags']['mod.full']}
		<textarea id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}"  name="{$Widget['name']}" tabindex="{$Widget['index']}" style="height: 180px;" placeholder="{$Widget['placeholder']}">{$Widget['value:html']}</textarea>
		{else}
		<textarea id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}"  name="{$Widget['name']}" tabindex="{$Widget['index']}" placeholder="{$Widget['placeholder']}">{$Widget['value:html']}</textarea>
		{endif}
	
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>
