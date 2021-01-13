
<div class="form-group ui-widget-IntCollectionEnumerableInput ui-widget-name-{$Widget['name']}" id="{$Widget['elementID']}" data-inputtype="{$Widget['enumerableType']}" data-inputname="{$Widget['name']}" data-enumerable="{$Widget['enumerableRef']}">

	<label class="col-md-2 control-label">{$Widget['caption']}</label>
	<div class="col-md-8">
		<div class="ui-widget-IntCollectionEnumerableInput-inner">
			{foreach from=$Widget['Items'] item=$item}
			<div class="btn-group">
				<button class="btn btn-default" type="button">{$item['title']}</button>
				<input class="ui-widget-IntCollectionEnumerableInput-value" type="hidden" name="{$Widget['name']}[]" value="{$item['id']}" />
				<button class="btn btn-danger ui-widget-IntCollectionEnumerableInput-delete-link" type="button"><i class="fa fa-remove"></i>&nbsp;</button>
			</div>
			{endforeach}
		</div>		
		<div class="ui-widget-IntCollectionEnumerableInput-adder">
			<div class="input-group">
				<select name="" class="form-control">{$Widget['SelectOptions']}</select>
				
				<span class="input-group-btn">
					<button class="btn btn-success ui-widget-IntCollectionEnumerableInput-add-link" type="button"><i class="fa fa-plus"></i>&nbsp;</button>
				</span>
				
				{if $Widget['enumerableType'] == 'select'}
				<span class="input-group-btn">
					<button class="btn btn-info ui-widget-IntCollectionEnumerableInput-add-all-link" type="button"><i class="fa fa-plus"></i>&nbsp;</button>
				</span>
				{endif}
			</div>
		</div>
	</div>
	<div class="col-md-2"></div>

</div>
  