<?php

$dropbox = $CONFIG->dropbox;

/* Get the account informations. */
$account = $dropbox->getAccountInfo();

$quota = $account['quota_info'];
$value = 37;
?>
<div class="elgg-module elgg-module-info">
	<div class="elgg-head">
		<h3>
			<?php echo elgg_echo('dropbox:youraccount'); ?>
		</h3>
	</div>
</div>
<table class="dropbox-table" cellspacing="0" cellpadding="4" width="100%">
	<tr>
		<td class="namefield">
			<p>
				<?php echo elgg_echo('dropbox:account:name'); ?>
			</p>
		</td>
		<td>
			<p>
				<?php echo $account['display_name']; ?>
			</p>
		</td>
	</tr>
	<tr>
		<td class="namefield">
			<p>
				<?php echo elgg_echo('dropbox:account:email'); ?>
			</p>
		</td>
		<td>
			<p>
				<?php echo $account['email']; ?>
			</p>
		</td>
	</tr>
	<tr>
		<td class="namefield">
			<p>
				<?php echo elgg_echo('dropbox:account:space:available'); ?>
			</p>
		</td>
		<td>
			<p>
				<?php echo ($quota['quota'] / (1024 * 1024)); ?> Mo
			</p>
		</td>
	</tr>
	<tr>
		<td class="namefield">
			<p>
				<?php echo elgg_echo('dropbox:account:space:free'); ?>
			</p>
		</td>
		<td>
			<p>
				<?php echo round(100 * $quota['normal'] / $quota['quota'], 2); ?> %
			</p>
		</td>
	</tr>
</table>
<?php


