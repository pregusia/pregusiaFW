
<div id="{$Widget['elementID']}_main" class="form-group row ui-widget-DateShiftInput ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-5">
		<input id="{$Widget['elementID']}_value" {$Widget['elementParams']} class="_field_{$Widget['name']} form-control {$Widget['elementClasses']}" type="text" name="{$Widget['name']}_value" tabindex="{$Widget['index']}" value="{$Widget['shiftValue']}" />
	</div>
	<div class="col-md-4">
		<select id="{$Widget['elementID']}_unit" {$Widget['elementParams']} class="_field_{$Widget['name']} form-control {$Widget['elementClasses']}" name="{$Widget['name']}_unit" tabindex="{$Widget['index']}">{$Widget['shiftUnitSelectOptions']}</select>
	</div>
</div>
