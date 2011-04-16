<?php

/* Get the path and listing in args. */
$path = $vars['path'];
$files = $vars['files'];

/* Get only the contents informations and sort to obtain dir on the top. */
$contents = array_sort($files['contents'], 'is_dir');

echo '<div class="contentWrapper">';

/* Create the 'upload' button. */
$upload_button = elgg_view('input/submit', array(
			'internalname' => 'params[upload]',
			'value' => elgg_echo('dropbox:upload'),
			'class' => 'upload_button',
		));

/* Create the 'new folder' button. */
$mkdir_button = elgg_view('input/submit', array(
			'internalname' => 'params[mkdir]',
			'value' => elgg_echo('dropbox:newfolder'),
			'class' => 'mkdir_button',
		));

/* Create the 'share a folder' button. */
$share_button = elgg_view('input/submit', array(
			'internalname' => 'params[share]',
			'value' => elgg_echo('dropbox:sharefolder'),
			'class' => 'share_button',
		));

/* Print the buttons. */
echo '<div class="dropbox_buttons">';
$body = $upload_button . $mkdir_button . $share_button;
echo elgg_view('input/form', array('body' => $body, 'action' => $vars['url'] . 'actions/dropbox/upload', 'method' => 'post'));
echo '</div>';

/* Add parameters to the form */
$body = elgg_view('input/hidden', array(
			'internalname' => 'path',
			'value' => $path,
		));

/* Add the table header of the filelist. */
$body .= '<table class="dropbox-list">' . '<thead>';

$checkbox = elgg_view('input/checkboxes', array(
			'class' => 'selector-checkbox',
			'options' => array('' => 'select'),
			'js' => 'onclick="javascript:$(\'.checkbox\').click();"',
		));

$body .= '<th class="selector">' . $checkbox . '</th>';
$body .= '<th class="filename">' . elgg_echo('dropbox:filename') . '</th>';
$body .= '<th class="size">' . elgg_echo('dropbox:size') . '</th>';
$body .= '<th class="modified">' . elgg_echo('dropbox:modified') . '</th>';
$body .= '</thead>';

/* Show the link to go to the parent directory if needed. */
if (!empty($files['path']) || empty($files)) {
	$body .= '<tr>';

	/* Remove the last directory in the path. */
	$sub_path = substr($path, 1);
	$reduced_path = substr($path, 0, -strlen(strchr($sub_path, '/')));

	/* Create the link to the change the Dropbox directory. */
	$url = elgg_http_add_url_query_elements($_SERVER['REQUEST_URI'], array('path' => $reduced_path));
	$link = '<a href="' . $url . '">' . elgg_echo('dropbox:parent_folder') . '</a>';

	$body .= '<td class="selector"></td>';
	$body .= '<td class="filename">' . $link . '</td>';
	$body .= '<td class="size"></td>';
	$body .= '<td class="modified"></td>';
	$body .= '</tr>';
}

$counter = 0;
/* List all files from the Dropbox directory. */
foreach ($contents AS $file) {
	$body .= '<tr>';

	/* Remove the / at the begining of the filename. */
	$filename = substr($file['path'], 1 + strlen($path));


	/* Create the link to the change the Dropbox directory. */
	$url = elgg_http_add_url_query_elements($_SERVER['REQUEST_URI'], array('path' => $file['path']));
	$link = '<a href="' . $url . '">' . $filename . '</a>';

	$checkbox = elgg_view('input/checkboxes', array(
				'internalname' => 'selected_files[]',
				'options' => array('' => $file['path']),
				'class' => 'checkbox',
			));
	if ($file['is_dir'] == 1) {
		$size = '';
		$modified = '';
	} else {
		$size = $file['size'];
		$modified = $file['modified'];
	}

	$body .= '<td class="selector">' . $checkbox . '</td>';
	$body .= '<td class="filename">' . $link . '</td>';
	$body .= '<td class="size">' . $size . '</td>';
	$body .= '<td class="modified">' . $modified . '</td>';
	$body .= '</tr>';
	$counter++;
}

$body .= '</table>';

/* Create the 'share a folder' button. */
$body .= elgg_view('input/submit', array(
			'value' => elgg_echo('delete'),
			'class' => 'delete_button',
		));

echo elgg_view('input/form', array('body' => $body, 'action' => $vars['url'] . 'action/dropbox/delete', 'method' => 'post'));

echo '</div>';