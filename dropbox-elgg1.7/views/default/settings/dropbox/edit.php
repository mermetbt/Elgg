<?php
/**
 *
 */
$insert_view = elgg_view('dropboxsettings/extend');

$consumer_key_string = elgg_echo('dropbox:consumer_key');
$consumer_key_view = elgg_view('input/text', array(
	'internalname' => 'params[consumer_key]',
	'value' => $vars['entity']->consumer_key,
	'class' => 'text_input',
));

$consumer_secret_string = elgg_echo('dropbox:consumer_secret');
$consumer_secret_view = elgg_view('input/text', array(
	'internalname' => 'params[consumer_secret]',
	'value' => $vars['entity']->consumer_secret,
	'class' => 'text_input',
));

$settings = <<<__HTML
<div id="dropboxservice_site_settings">
	<div>$insert_view</div>
	<div>$consumer_key_string $consumer_key_view</div>
	<div>$consumer_secret_string $consumer_secret_view</div>
</div>
__HTML;

echo $settings;
