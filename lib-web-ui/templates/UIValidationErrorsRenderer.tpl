
{if $ValidationErrors["Errors"]}
<div class="alert alert-danger">
	{foreach from=$ValidationErrors["Errors"] item=$err}
	<i class="fa fa-exclamation-triangle"></i>
	{if !$err['fieldCaptionEmpty']}
	<strong>{$err["fieldCaption"]}:</strong>
	{endif}
	{$err["errorText"]}
	<br />
	{endforeach}
</div>
{endif}
