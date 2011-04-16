<?php

/* Get the path and listing in args. */
$path = $vars['path'];
$files = $vars['files'];

/* Get only the contents informations and sort to obtain dir on the top. */
$d = array();
$f = array();
foreach($files['contents'] AS $file) {
	if($file['is_dir'] == 1) {
		$d[] = $file;
	}else {
		$f[] = $file;
	}
}
$contents = array_merge($d, $f);

/* Add path parameter to the forms */
$body = elgg_view('input/hidden', array(
			'internalname' => 'path',
			'value' => $path,
		));

echo '<div class="contentWrapper">';

echo '<div class="dropbox-buttons">';
/* Create the 'upload' button. */
$upload_button = elgg_view('input/submit', array(
			'internalname' => 'upload',
			'value' => elgg_echo('upload'),
			'class' => 'upload_button',
		));
echo elgg_view('input/form', array('body' => $body.$upload_button, 'action' => $vars['url'] . 'pg/dropbox/upload', 'method' => 'post'));

/* Create the 'new folder' button. */
$mkdir_button = elgg_view('input/submit', array(
			'internalname' => 'mkdir',
			'value' => elgg_echo('dropbox:newfolder'),
			'class' => 'mkdir_button',
		));
echo elgg_view('input/form', array('body' => $body.$mkdir_button, 'action' => $vars['url'] . 'pg/dropbox/mkdir', 'method' => 'post'));

/* Create the 'share a folder' button. */
$share_button = elgg_view('input/submit', array(
			'internalname' => 'share',
			'value' => elgg_echo('dropbox:sharefolder'),
			'class' => 'share_button',
		));
echo elgg_view('input/form', array('body' => $body.$share_button, 'action' => $vars['url'] . 'pg/dropbox/share', 'method' => 'post'));

echo '</div>';


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
	$link = '<a class="dropbox-sprite-link" href="' . $url . '"><img class="sprite s_arrow_turn_up" alt=""></a>';
    $link .= '<a class="dropbox-link" href="' . $url . '">' . elgg_echo('dropbox:parent_folder') . '</a>';

	/* Create the row */
	$body .= '<td class="selector"></td>';
	$body .= '<td class="filename">' . $link . '</td>';
	$body .= '<td class="size"></td>';
	$body .= '<td class="modified"></td>';
	$body .= '</tr>';
}

$counter = 0;
$form_accu = '';
/* List all files from the Dropbox directory. */
foreach ($contents AS $file) {
	$body .= '<tr>';

	/* Remove the / at the begining of the filename. */
	$filename = substr($file['path'], 1 + strlen($path));

	/* Select the icon */
	switch($file['icon']) {
		case 'folder':
			$css = 's_folder_blue';
			break;
		case 'folder_photos':
			$css = 's_folder_photos_blue';
			break;
		case 'folder_public':
			$css = 's_folder_public';
			break;
		default:
			$css = 's_page_white_text';
	}
	
	/* Create the link to the change the Dropbox directory. */
	if($file['is_dir']) {
		$url = elgg_http_add_url_query_elements($_SERVER['REQUEST_URI'], array('path' => $file['path']));
		$link = '<a class="dropbox-sprite-link" href="' . $url . '"><img class="sprite ' . $css . '" alt=""></a>';
		$link .= '<a class="dropbox-link" href="' . $url . '">' . $filename . '</a>';
	} else {
		$value = elgg_view('input/hidden', array('internalname' => 'file', 'value' => $file['path']));
		$link = '<a class="dropbox-sprite-link" href="#" onclick="javascript:document.dropbox_form_'.$counter.'.submit();"><img class="sprite ' . $css . '" alt=""></a>';
		$link .= '<a class="dropbox-link" href="#" onclick="javascript:document.dropbox_form_'.$counter.'.submit();">' . $filename . '</a>';
		$form_accu .= elgg_view('input/form', array(
						'id' => 'dropbox_form_'.$counter,
						'internalname' => 'dropbox_form_'.$counter,
						'body' => $value,
						'action' => $vars['url'] . 'action/dropbox/getfile',
						'method' => 'post'));
	}

	/* Create the checkbox */
	$checkbox = elgg_view('input/checkboxes', array(
				'internalname' => 'selected_files[]',
				'options' => array('' => $file['path']),
				'class' => 'checkbox',
			));

	/* Remove the size and modified fields if the file is a directory. */
	if ($file['is_dir'] == 1) {
		$size = '';
		$modified = '';
	} else {
		$size = $file['size'];
		$modified = $file['modified'];
	}

	/* Create the row */
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
echo $form_accu;
