<?php

/* Need to be logged in to do this. */
gatekeeper();

$user_id = elgg_get_logged_in_user_guid();

/* Remove token to unlink the account. */
elgg_set_plugin_user_setting('token', '', $user_id, 'dropbox');
elgg_set_plugin_user_setting('token_secret', '', $user_id, 'dropbox');
			
system_message(elgg_echo('dropbox:usersettings:unlinked'));
forward($_SERVER['HTTP_REFERER']);
