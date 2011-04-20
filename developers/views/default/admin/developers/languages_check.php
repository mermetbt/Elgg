<?php
elgg_generate_plugin_entities();
$installed_plugins = elgg_get_plugins('any', true);

$selected_plugin = get_input('plugins_select', null);

$plugins = array();
foreach($installed_plugins AS $id => $plugin) {
	$plugins[] = $plugin->getId();
}

$plugin_dropdown = elgg_view('input/dropdown', array(
	'name' => 'plugins_select',
	'options_values' => $plugins,
	'value' => $selected_plugin
));

$plugin_button = elgg_view('input/submit', array(
	'value' => elgg_echo('check'),
	'class' => 'elgg-button elgg-button-action'
));

$plugin_form = elgg_view('input/form', array(
	'body' => $plugin_dropdown . $plugin_button,
	'method' => 'get',
	'action' => 'admin/developers/languages_check',
	'disable_security' => true,
));

?>
<div id="content_header" class="mbm clearfix">
	<div class="content-header-options"><?php echo $plugin_form ?></div>
</div>

<div id="missing-keys">
<?php

if($selected_plugin != null) {
	$plugin = $installed_plugins[$selected_plugin]->getId();

	/* Open the plugin and check. */
	$pl = new LanguageChecker();
	$pl->load($plugin);
	$unmatched = $pl->check();

	
	echo elgg_echo('languages_check:plugin_name') . ' : <b>' . $plugin .'</b><br><br>';

	if(!empty($unmatched['en'])) {
		echo elgg_echo('languages_check:missing_keys') . '<br>';
	} else {
		echo elgg_echo('languages_check:found_keys') . '<br>';
	}

	/* Print the list of unmatched keys. */
	echo '<ul>';
	foreach($unmatched['en'] AS $key => $val) {
		echo '<li>' . $val . '</li>';
	}
	echo '</ul>';
}
?>
</div>
<?php
