
<div id="{$Widget['elementID']}_main" class="form-group ui-widget-name-{$Widget['name']} ui-widget-StaticText {$Widget['Errors'] ? 'has-error' : ''}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
		<p class="form-control-static">{$Widget['staticText']}</p>
		
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>
