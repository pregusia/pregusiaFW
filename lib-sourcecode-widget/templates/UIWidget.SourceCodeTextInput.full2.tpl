
<div id="{$Widget['elementID']}_main" class="form-group ui-widget-SourceCodeTextInput ui-widget-mod-full2 ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}" data-theme="{$Widget['theme']}" data-mode="{$Widget['mode']}">
	<div class="col-md-12">
		<textarea id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}"  name="{$Widget['name']}" tabindex="{$Widget['index']}" style="width: 100%; height: 350px;">{$Widget['value:html']}</textarea>
	
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>
