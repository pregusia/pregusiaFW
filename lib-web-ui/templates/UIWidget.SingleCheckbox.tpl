 

<div id="{$Widget['elementID']}_main" class="form-group ui-widget-SingleCheckbox ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}">
	<label class="col-md-2 control-label">&nbsp;</label>
	<div class="col-md-9">
		{if $Widget['value']}
		<div class="checkbox">
			<label>
				<input type="checkbox" name="{$Widget['name']}" checked="checked" />
				<span class="cr"><i class="cr-icon fa fa-check"></i></span>
				{$Widget['caption']}
			</label>
		</div>
		{else}
		<div class="checkbox">
			<label>
				<input type="checkbox" name="{$Widget['name']}" />
				<span class="cr"><i class="cr-icon fa fa-check"></i></span>
				{$Widget['caption']}
			</label>
		</div>
		{endif}
		
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>
