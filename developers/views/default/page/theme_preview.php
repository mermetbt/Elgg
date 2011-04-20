<?php
/**
 * Page shell for theme preview
 */

$elgg = elgg_get_simplecache_url('css', 'elgg');
$ie_url = elgg_get_simplecache_url('css', 'ie');
$ie6_url = elgg_get_simplecache_url('css', 'ie6');

// Set the content type
header("Content-type: text/html; charset=UTF-8");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $vars['title']; ?></title>
	<link rel="stylesheet" href="<?php echo $elgg; ?>" type="text/css" />
	<style>
		.elgg-page-body { margin: 20px }
		.elgg-page-header:after {content:'.'; display:block; clear:both; height:0; visibility: hidden}
		.elgg-page-header .elgg-menu-page > li {float:left; padding: 5px}
		.elgg-page-header .elgg-menu-page > li > a {padding: 2px 4px}
		.elgg-page-header .elgg-menu-page > li.elgg-state-selected > a {background-color:#0054A7;}
	</style>
	<!--[if gt IE 6]>
		<link rel="stylesheet" type="text/css" href="<?php echo $ie_url; ?>" />
	<![endif]-->
	<!--[if IE 6]>
		<link rel="stylesheet" type="text/css" href="<?php echo $ie6_url; ?>" />
	<![endif]-->

<?php
foreach (elgg_get_loaded_js() as $script) {
?>
	<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php
}
?>

</head>
<body>
<div class="elgg-page">
	<div class="elgg-page-header">
		<div class="elgg-inner">
			<?php echo elgg_view_menu('page'); ?>
		</div>
	</div>
	<div class="elgg-page-body">
		<div class="elgg-inner">
			<?php echo elgg_view_title($vars['title']); ?>
			<?php echo $vars['body']; ?>
		</div>
	</div>
	<div class="elgg-page-header">
		<div class="elgg-inner">
			<?php echo elgg_view_menu('page'); ?>
		</div>
	</div>
</div>
</body>
</html>