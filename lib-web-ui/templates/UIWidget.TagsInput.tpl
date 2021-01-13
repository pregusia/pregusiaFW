
<div id="{$Widget['elementID']}_main" class="form-group ui-widget-TagsInput ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}" data-enumerable="{$Widget['enumerableRef']}" data-inputname="{$Widget['name']}" data-inputtype="{$Widget['enumerableType']}" >
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
		<div class="input-group">
			<span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
			<select id="{$Widget['elementID']}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}" name="{$Widget['name']}[]" tabindex="{$Widget['index']}" multiple="multiple">{$Widget['SelectOptions']}</select>
		</div>	
	
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>
