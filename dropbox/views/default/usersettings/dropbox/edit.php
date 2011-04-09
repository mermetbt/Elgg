<?php

$user_id = get_loggedin_userid();
$dropbox_name = get_plugin_usersetting('dropbox_name', $user_id, 'dropbox');
$access_key = get_plugin_usersetting('access_key', $user_id, 'dropbox');
$access_secret = get_plugin_usersetting('access_secret', $user_id, 'dropbox');

echo '<p>' . elgg_echo('dropbox:usersettings:description') . '</p>';

$access_key_string = elgg_echo('dropbox:access_key');
$access_key_view = elgg_view('input/text', array(
	'internalname' => 'params[access_key]',
	'value' => $vars['entity']->access_key,
	'class' => 'text_input',
));

$access_secret_string = elgg_echo('dropbox:access_secret');
$access_secret_view = elgg_view('input/password', array(
	'internalname' => 'params[access_secret]',
	'value' => $vars['entity']->access_secret,
	'class' => 'text_input',
));

$settings = <<<__HTML
<div id="dropboxservice_site_settings">
	<div>$insert_view</div>
	<div>$access_key_string $access_key_view</div>
	<div>$access_secret_string $access_secret_view</div>
</div>
__HTML;

echo $settings;
