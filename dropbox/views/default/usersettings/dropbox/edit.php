<?php

$user_id = get_loggedin_userid();

/* Get the plugin main setting. */
$consumer_key = get_plugin_setting('consumer_key', 'dropbox');
$consumer_secret = get_plugin_setting('consumer_secret', 'dropbox');

/* Get the plugin user setting. */
$access_key = get_plugin_usersetting('access_key', $user_id, 'dropbox');
$access_secret = get_plugin_usersetting('access_secret', $user_id, 'dropbox');
set_plugin_usersetting('access_key', '', $user_id, 'dropbox');
set_plugin_usersetting('access_secret', '', $user_id, 'dropbox');

/* Check the validity of the consumer. */
try {
	$oauth = new Dropbox_OAuth_ELGG($consumer_key, $consumer_secret);
	$dropbox = new Dropbox_API($oauth);
}catch(Dropbox_Exception $e) {
	register_error(elgg_echo('dropbox:error:badconsumer'));
}

/* Check the validity of the username and password. */
if($access_key && $access_secret) {
	try {
		$tokens = $dropbox->getToken($access_key, $access_secret);
		set_plugin_usersetting('token', $tokens['token'], $user_id, 'dropbox');
		set_plugin_usersetting('token_secret', $tokens['token_secret'], $user_id, 'dropbox');
	}catch(Dropbox_Exception $e) {
        /* Set the token and token_secret to empty. */
		set_plugin_usersetting('token', '', $user_id, 'dropbox');
		set_plugin_usersetting('token_secret', '', $user_id, 'dropbox');

		register_error(elgg_echo('dropbox:error:baduserpass'));
	}
}

/* Get the current tokens */
$token = get_plugin_usersetting('token', $user_id, 'dropbox');
$token_secret = get_plugin_usersetting('token_secret', $user_id, 'dropbox');
$oauth->setToken($token, $token_secret);

/*
 * Get account information. This initiate a request to Dropbox,
 * and allow us to know if the consumer and tokens are good.
 */
try {
	if($token && $token_secret) {
		$dropbox->getAccountInfo();
		echo elgg_echo('dropbox:usersettings:linked');
	} else {
		echo elgg_echo('dropbox:usersettings:userpass');
	}
}
catch(Dropbox_Exception $e) {
	register_error(elgg_echo('dropbox:error:baduserpass'));
}

$dropbox_name = get_plugin_usersetting('dropbox_name', $user_id, 'dropbox');

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
