<?php

$path = $vars['path'];

/* Add path parameter to the form */
$param = elgg_view('input/hidden', array(
			'name' => 'path',
			'value' => $path,
		));

echo '<div class="contentWrapper">';
if (empty($path)) {
	$path = '/';
}

echo sprintf(elgg_echo('dropbox:folder:new:info'), $path);

$field = elgg_view('input/text', array(
			'name' => 'name',
			'value' => '',
		));

$save = elgg_view('input/submit', array(
			'name' => 'submit',
			'value' => elgg_echo('save'),
			'class' => 'elgg-button elgg-button-submit',
		));

$cancel = elgg_view('input/submit', array(
			'name' => 'submit',
			'value' => elgg_echo('cancel'),
			'class' => 'elgg-button elgg-button-cancel',
		));

echo elgg_view('input/form', array(
	'body' => $param . $field . $save . $cancel,
	'action' => $vars['url'] . 'action/dropbox/mkdir',
	'method' => 'post'));
echo '</div>';
