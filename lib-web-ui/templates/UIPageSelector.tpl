<ul class="pagination ui-widget-PageSelector">
	{if $PageSelector['Prev']}
	<li><a href="{$PageSelector['Prev']['link']}" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
	{else}
	<li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
	{endif}
	
	{foreach from=$PageSelector['All'] item=$page}
	{if $page['active']}
	<li class="active"><a href="{$page['link']}">{$page['page']}</a></li>
	{else}
	<li><a href="{$page['link']}">{$page['page']}</a></li>
	{endif}
	{endforeach}
	
	{if $PageSelector['Next']}
	<li><a href="{$PageSelector['Next']['link']}" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
	{else}
	<li class="disabled"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
	{endif}
</ul>

