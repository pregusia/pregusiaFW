
<div id="{$Widget['elementID']}_main" class="form-group row ui-widget-I18NStringInput ui-widget-name-{$Widget['name']} {$Widget['Errors'] ? 'has-error' : ''}">
	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-9">
	
		<div class="input-group">
			<div class="input-group-btn">
				<div class="btn-group">
					{foreach from=$Widget['Languages'] item=$lang index=$idx}
					{if $idx == 1}
					<a class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{$lang}</a>
					{endif}
					{endforeach}
					
					<ul class="dropdown-menu" role="menu">
						{foreach from=$Widget['Languages'] item=$lang index=$idx}
						<li><a href="#" data-lang="{$lang}">{strtoupper($lang)}</a></li>
						{endforeach}
					</ul>
				</div>
			</div>
			
			{foreach from=$Widget['Languages'] item=$lang}
			<input data-lang="{$lang}" {$Widget['elementParams']} class="form-control {$Widget['elementClasses']}" type="text" name="{$Widget['name']}_{$lang}" tabindex="{$Widget['index']}" value="{htmlspecialchars($Widget['ValueArray'][$lang])}" />
			{endforeach}
		</div>
	</div>
</div>
