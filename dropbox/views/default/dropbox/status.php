<?php

$dropbox = $CONFIG->dropbox;

/* Get the account informations. */
$account = $dropbox->getAccountInfo();

$quota = $account['quota_info'];

echo elgg_echo('dropbox:account:name'), ' ', $account['display_name'], '<br>';
echo elgg_echo('dropbox:account:email'), ' ', $account['email'], '<br>';
echo '<br>';
echo elgg_echo('dropbox:account:space:available'), ' ', $quota['quota'] / (1024 * 1024), ' Mo<br>';
echo elgg_echo('dropbox:account:space:free'), ' ', round((100 * $quota['normal']) / $quota['quota'], 2) . ' %';

