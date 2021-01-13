{if $Notification['type'] == 'error'}
<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> {$Notification['text']}</div>
{endif}

{if $Notification['type'] == 'information'}
<div class="alert alert-info"><i class="fa fa-info-circle"></i> {$Notification['text']}</div>
{endif}

{if $Notification['type'] == 'success'}
<div class="alert alert-success"><i class="fa fa-check-square-o"></i> {$Notification['text']}</div>
{endif}

{if $Notification['type'] == 'warning'}
<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> {$Notification['text']}</div>
{endif}
