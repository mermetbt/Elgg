<?php

	/**
	 * Elgg hoverover extender
	 * 
	 * @package ElggDropbox
	 */
	 
	 //need to be logged in to send a message
	 if (isloggedin()) {
?>
	<p class="user_menu_dropbox">
		<a href="<?php echo $vars['url']; ?>pg/dropbox/"><?php echo elgg_echo("dropbox:sharefolder"); ?></a>
	</p>
<?php
	}
?>