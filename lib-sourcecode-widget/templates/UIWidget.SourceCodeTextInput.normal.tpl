
<div id="{$Widget['elementID']}_main" class="form-group ui-widget-SourceCodeTextInput ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}" data-theme="{$Widget['theme']}" data-mode="{$Widget['mode']}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
		<textarea id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}"  name="{$Widget['name']}" tabindex="{$Widget['index']}" style="height: 280px;">{$Widget['value:html']}</textarea>
	
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>
