<?php
/**
 * Elgg Dropbox topbar extender
 * 
 * @package ElggDropbox
 */

//need to be logged in to access to dropbox
gatekeeper();

?>
<a href="<?php echo $vars['url']; ?>pg/dropbox/" class="dropbox_root" ><img class="sprite s_dropbox_blue" alt=""></a>
