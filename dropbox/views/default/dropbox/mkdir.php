<?php

$path = $vars['path'];

/* Add path parameter to the form */
$param = elgg_view('input/hidden', array(
			'internalname' => 'path',
			'value' => $path,
		));

echo '<div class="contentWrapper">';
if(empty($path)) {
	$path = '/';
}

echo sprintf(elgg_echo('dropbox:folder:new:info'), $path);

$field = elgg_view('input/text', array(
			'internalname' => 'name',
			'value' => '',
		));

$save = elgg_view('input/submit', array(
			'internalname' => 'submit',
			'value' => elgg_echo('save'),
		));

$cancel = elgg_view('input/submit', array(
			'internalname' => 'submit',
			'value' => elgg_echo('cancel'),
		));

echo elgg_view('input/form', array(
			'body' => $param . $field . $save . $cancel,
			'action' => $vars['url'] . 'action/dropbox/mkdir',
			'method' => 'post'));
echo '</div>';
