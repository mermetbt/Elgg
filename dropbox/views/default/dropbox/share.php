<?php

$path = $vars['path'];

echo '<div class="contentWrapper">';

/* Add path parameter to the form */
$param = elgg_view('input/hidden', array(
			'internalname' => 'path',
			'value' => $path,
		));

echo '<div class="contentWrapper">';
if(empty($path)) {
	$path = '/';
}

echo sprintf(elgg_echo('dropbox:folder:share:info'), $path);

$select_create = elgg_view('input/radio', array(
			'internalname' => 'choice',
			'options' => array(elgg_echo('dropbox:folder:share:new') => '1'),
		));

$field = elgg_view('input/text', array(
			'internalname' => 'name',
			'value' => '',
		));

$select_existing = elgg_view('input/radio', array(
			'internalname' => 'choice',
			'options' => array(elgg_echo('dropbox:folder:share:existing') => '2'),
		));

$share = elgg_view('input/submit', array(
			'internalname' => 'submit',
			'value' => elgg_echo('dropbox:share'),
		));

$cancel = elgg_view('input/submit', array(
			'internalname' => 'submit',
			'value' => elgg_echo('cancel'),
		));

$form = $param . $select_create . $field . $select_existing . $share.$cancel;

echo elgg_view('input/form', array(
			'body' => $form,
			'action' => $vars['url'] . 'action/dropbox/share',
			'method' => 'post'));

echo '</div>';
