
{if count($ActionsRenderer['Actions']) <= $ActionsRenderer['limit']}
<div class="ui-UIActionsRenderer">
	{foreach from=$ActionsRenderer['Actions'] item=$action index=$idx}
	<a class="{$action['elementClasses']}" href="{$action['url']}" title="{$action['tooltip']}" {$action['elementParams']}>{$action['title']}</a>
	{endforeach}
</div>

{else}

<div class="ui-UIActionsRenderer">
	
	{foreach from=$ActionsRenderer['Actions'] item=$action index=$idx}
	
	{if $idx == $ActionsRenderer['limit']}
	<div class="btn-group">
	<button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-h"></i></button>
	<ul class="dropdown-menu pull-right">
	{'',$menuOpened=true,''}
	{endif}
	
	{if $idx <= ($ActionsRenderer['limit'] - 1)}
	<a class="{$action['elementClasses']}" href="{$action['url']}" title="{$action['tooltip']}" {$action['elementParams']}>{$action['title']}</a>
	{else}
	<li><a class="{$action['elementClassesWithoutBtn']}" href="{$action['url']}" title="{$action['tooltip']}" {$action['elementParams']}>{$action['title']} {$action['tooltip']}</a></li>
	{endif}
	
	{endforeach}
	
	{if $menuOpened}
	</ul>
	</div>
	{endif}
	
</div>


{endif}

