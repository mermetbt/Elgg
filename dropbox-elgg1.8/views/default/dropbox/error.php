<div  class="contentWrapper">
<b>
<?php
switch ($vars['errcode']) {
	case DROPBOX_CONSUMER_FAILED:
		echo elgg_echo('dropbox:error:badconsumer');
		break;
	case DROPBOX_CONSUMER_MISSING:
		echo elgg_echo('dropbox:error:missingconsumer');
		break;
	case DROPBOX_USERPASS_FAILED:
		echo elgg_echo('dropbox:error:baduserpass');
		break;
	case DROPBOX_USERPASS_MISSING:
		echo elgg_echo('dropbox:error:missinguserpass');
		break;
	case DROPBOX_FORBIDDEN:
		echo elgg_echo('dropbox:error:forbidden');
		break;
	case DROPBOX_NOTFOUND:
		echo elgg_echo('dropbox:error:notfound');
		break;
	default:
		echo elgg_echo('dropbox:error:unknow');
}
?>
</b></div>
<?php
