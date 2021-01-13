
<div id="{$Widget['elementID']}_main" class="form-group row ui-widget-DatesRangeInput ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
		<div class="col-md-6">
			<div class="input-group date-input">
				<span class="input-group-addon"> <span class="fa fa-calendar"></span> </span>
				<input class="form-control ui-widget-DatesRangeInput-input-start" {$Widget['elementClasses']} type="text" name="{$Widget['name']}_start" value="{$Widget['valueStart']}" />
			</div>
		</div>
		<div class="col-md-6">
			<div class="input-group date-input">
				<span class="input-group-addon"> <span class="fa fa-calendar"></span> </span>
				<input class="form-control ui-widget-DatesRangeInput-input-stop" {$Widget['elementClasses']} type="text" name="{$Widget['name']}_stop" value="{$Widget['valueStop']}" />
			</div>
		</div>	
		
		{if $Widget['hasDescription']}
		<span class="help-block">{$Widget['description']}</span>
		{endif}
	</div>
</div>
