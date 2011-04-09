<?php

$dropbox = $CONFIG->dropbox;

/* Get the account informations. */
$account = $dropbox->getAccountInfo();

$quota = $account['quota_info'];
echo '<div class="contentWrapper">';
echo '<b>', elgg_echo('dropbox:account:name'), '</b> ', $account['display_name'], '<br>';
echo '<b>', elgg_echo('dropbox:account:email'), '</b> ', $account['email'], '<br>';
echo '<br>';
echo '<b>', elgg_echo('dropbox:account:space:available'), '</b> ', $quota['quota'] / (1024 * 1024), ' Mo<br>';
echo '<b>', elgg_echo('dropbox:account:space:free'), '</b> ', round((100 * $quota['normal']) / $quota['quota'], 2) . ' %';

echo '</div>';