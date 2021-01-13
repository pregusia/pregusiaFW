 

<div id="{$Widget['elementID']}_main" class="form-group ui-widget-RadioGroup ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
		{foreach from=$Widget['Items'] item=$item}
		{if $item['value'] == $Widget['value']}
		<div class="radio">
			<label>
				<input type="radio" name="{$Widget['name']}" value="{$item['value']}" checked="checked" />
				<span class="cr"><i class="cr-icon fa fa-check"></i></span>
				{$item['caption']}
			</label>
		</div>
		{else}
		<div class="radio">
			<label>
				<input type="radio" name="{$Widget['name']}" value="{$item['value']}" />
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
