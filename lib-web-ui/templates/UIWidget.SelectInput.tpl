
<div id="{$Widget['elementID']}_main" class="form-group ui-widget-SelectInput ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
		{if $Widget['flag-multi']}
		<select id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}" name="{$Widget['name']}[]" multiple="multiple" tabindex="{$Widget['index']}">{$Widget['SelectOptions']}</select>
		{else}
		<select id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}" name="{$Widget['name']}" tabindex="{$Widget['index']}">{$Widget['SelectOptions']}</select>
		{endif}
		
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>
