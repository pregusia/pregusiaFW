

<div id="{$Widget['elementID']}_main" class="form-group ui-widget-PropertiesMap ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}" data-inputname="{$Widget['name']}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
	
		<div class="ui-widget-PropertiesMap-items">
			{foreach from=$Widget['Items'] item=$item index=$nr}
			<div class="input-group" style="margin-bottom: 10px;">
				<input class="form-control ui-widget-PropertiesMap-input-name" type="text" name="{$Widget['name']}_{$nr}_name" value="{$item['name:html']}" />
				<span class="input-group-addon"> = </span>
				<input class="form-control ui-widget-PropertiesMap-input-value" type="text" name="{$Widget['name']}_{$nr}_value" value="{$item['value:html']}" />
				<span class="input-group-btn">
					<button class="btn btn-danger btn-remove ui-widget-PropertiesMap-delete-link"><i class="fa fa-remove"></i>&nbsp;</button>
				</span>
			</div>
			{endforeach}
		</div>
				
		<div class="ui-widget-PropertiesMap-adder">
			<div class="input-group">
				<input class="form-control ui-widget-PropertiesMap-input-name" type="text" name="" value="" />
				<span class="input-group-addon"> = </span>
				<input class="form-control ui-widget-PropertiesMap-input-value" type="text" name="" value="" />
				<span class="input-group-btn">
					<button class="btn btn-success btn-add ui-widget-PropertiesMap-add-link"><i class="fa fa-plus"></i>&nbsp;</button>
				</span>
			</div>		
		</div>
		
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>

