 

<div id="{$Widget['elementID']}_main" class="form-group ui-widget-Checkboxes ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
		{foreach from=$Widget['Checkboxes'] item=$item}
		{if $item['checked']}
		<div class="checkbox" data-name="{$item['name']}" data-value="{$item['value']}">
			<label>
				<input type="checkbox" name="{$Widget['name']}_{$item['value']}" checked="checked" />
				<span class="cr"><i class="cr-icon fa fa-check"></i></span>
				{$item['caption']}
			</label>
		</div>
		{else}
		<div class="checkbox" data-name="{$item['name']}" data-value="{$item['value']}">
			<label>
				<input type="checkbox" name="{$Widget['name']}_{$item['value']}" />
				<span class="cr"><i class="cr-icon fa fa-check"></i></span>
				{$item['caption']}
			</label>
		</div>
		{endif}
		{endforeach}	
		
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>
