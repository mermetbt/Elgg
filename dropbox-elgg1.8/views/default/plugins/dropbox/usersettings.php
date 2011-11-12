<?php

$user_id = elgg_get_logged_in_user_guid();

/* Get the plugin main setting. */
$consumer_key = elgg_get_plugin_setting('consumer_key', 'dropbox');
$consumer_secret = elgg_get_plugin_setting('consumer_secret', 'dropbox');

/* Check the validity of the consumer. */
try {
	$oauth = new Dropbox_OAuth_ELGG($consumer_key, $consumer_secret);
	$dropbox = new Dropbox_API($oauth);
} catch (Dropbox_Exception $e) {
	register_error(elgg_echo('dropbox:error:badconsumer'));
}

/* Get the current tokens */
$token = elgg_get_plugin_user_setting('token', $user_id, 'dropbox');
$token_secret = elgg_get_plugin_user_setting('token_secret', $user_id, 'dropbox');
if($oauth && $token && $token_secret) {
	$oauth->setToken($token, $token_secret);
	$state = 3;
} else {
	if($_SESSION['oauth_tokens'] != '' && isset($_GET['uid']) && isset($_GET['oauth_token']))
		$state = 2;
	else
		$state = 1;
}

$dropbox_name = elgg_get_plugin_user_setting('dropbox_name', $user_id, 'dropbox');

echo '<p>' . elgg_echo('dropbox:usersettings:description') . '</p>';

switch($state) {

    /* In this phase we grab the initial request tokens
       and redirect the user to the 'authorize' page hosted
       on dropbox */
    case 1 :
	try {
	    $url = elgg_get_site_url();
	    $user = elgg_get_logged_in_user_entity();
	    $url .= "settings/plugins/{$user}";
	    //$entity = elgg_get_pl;
	    //$url .= $entity->getURL();
	    $tokens = $oauth->getRequestToken($url);

	    // Note that if you want the user to automatically redirect back, you can
	    // add the 'callback' argument to getAuthorizeUrl.
	    echo '<p>' . elgg_echo('dropbox:usersettings:step1') . '</p>';
	    echo '<a href="'.$oauth->getAuthorizeUrl().'&oauth_callback=' . $url . '">';
	    echo elgg_echo('dropbox:usersettings:step1_link') . '</a>' . "\n";
	    $_SESSION['oauth_tokens'] = $tokens;
	} catch(Dropbox_Exception $e) {
	    register_error(elgg_echo('dropbox:error:requesttoken'));
	    elgg_log($e, 'WARNING');
	}
	break;

    /* In this phase, the user just came back from authorizing
       and we're going to fetch the real access tokens */
    case 2 :
	echo '<p>' . elgg_echo('dropbox:usersettings:step2') . '</p>';
		try {
			/* Get the access token. */
			$tokens = $_SESSION['oauth_tokens'];
			$oauth->setToken($tokens['oauth_token'], $tokens['oauth_token_secret']);
			$tokens = $oauth->getAccessToken();
			
			/* Store the token. */
			$_SESSION['oauth_tokens'] = '';
			$token = $tokens['oauth_token'];
			$token_secret = $tokens['oauth_token_secret'];
			elgg_set_plugin_user_setting('token', $token, $user_id, 'dropbox');
			elgg_set_plugin_user_setting('token_secret', $token_secret, $user_id, 'dropbox');
			$oauth->setToken($token, $token_secret);
		} catch(Dropbox_Exception $e) {
			$_SESSION['oauth_tokens'] = '';
			elgg_set_plugin_user_setting('token', '', $user_id, 'dropbox');
			elgg_set_plugin_user_setting('token_secret', '', $user_id, 'dropbox');
			register_error(elgg_echo('dropbox:error:accesstoken'));
			elgg_log($e, 'WARNING');
		}
}

/*
 * Get account information. This initiate a request to Dropbox,
 * and allow us to know if the consumer and tokens are good.
 */
try {
	if ($dropbox && $token && $token_secret) {
		$dropbox->getAccountInfo();
		echo elgg_echo('dropbox:usersettings:linked');
	} else {
		echo elgg_echo('dropbox:usersettings:authentication');
	}
} catch (Dropbox_Exception $e) {
	register_error(elgg_echo('dropbox:error:badtoken'));
	elgg_set_plugin_user_setting('token', '', $user_id, 'dropbox');
	elgg_set_plugin_user_setting('token_secret', '', $user_id, 'dropbox');
}

/**
 * The following code is unused.
 * TODO: Create a unlink button, and available settings.
 */

$access_key_string = elgg_echo('dropbox:access_key');
$access_key_view = elgg_view('input/text', array(
			'name' => 'params[access_key]',
			'value' => $vars['entity']->access_key,
			'class' => 'text_input',
		));

$access_secret_string = elgg_echo('dropbox:access_secret');
$access_secret_view = elgg_view('input/password', array(
			'name' => 'params[access_secret]',
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

//echo $settings;
