<?php 
if (!$object->isNew()) {
?>
	<span style="color:#333333;font-weight:bolder;"><?php echo lang('object subscribers') ?>:</span>
	<div class="objectFiles">
	<div class="objectFilesTitle"></div>
	<table style="width:100%;margin-left:2px;margin-right:3px">
	<?php $counter = 0;
	if (!$object->isSubscriber(logged_user()) && !$object->isTrashed()) {
		if ($object->canEdit(logged_user())) {
			echo "<tr><td colspan=\"2\">";
			echo lang("user not subscribed to object");
			echo " (";
			echo '<a class="internalLink" href="#" onclick="if (confirm(\''.escape_single_quotes(lang("confirm subscribe")).'\')) og.openLink(\''.$object->getSubscribeUrl().'\');">'.lang("subscribe to object").'</a>';
			echo ")";
			echo "</td></tr>";
		}
	} else {
	?>
		<tr class="subscriber<?php echo $counter % 2 ? 'even' : 'odd' ?>">
		<td style="padding-left:1px;vertical-align:middle;width:22px">
		<a class="internalLink" href="<?php echo logged_user()->getCardUrl() ?>">
		<div class="db-ico unknown ico-user"></div>
		</a></td><td><b><a class="internalLink" href="<?php echo logged_user()->getCardUrl() ?>">
		<span><?php echo lang("you") ?></span> </a></b> <?php if (!$object->isTrashed()) {?>
			(<a class="internalLink" href="#" onclick="if (confirm('<?php echo escape_single_quotes(lang("confirm unsubscribe")) ?>')) og.openLink('<?php echo $object->getUnsubscribeUrl() ?>');"><?php echo lang("unsubscribe from object") ?></a>)
		<?php } ?></td></tr>
	<?php } ?>
	<?php $subscribers = $object->getSubscribers();
		if($subscribers){
			foreach ($subscribers as $subscriber) {
				if (!$subscriber instanceof User || $subscriber->getId() == logged_user()->getId() || !$object->canView($subscriber)) continue;
				$counter++;?>
				<tr class="subscriber<?php echo $counter % 2 ? 'even' : 'odd' ?>">
				<td style="padding-left:1px;vertical-align:middle;width:22px">
				<a class="internalLink" href="<?php echo $subscriber->getCardUrl() ?>">
				<div class="db-ico unknown ico-user"></div>
				</a></td><td><b><a class="internalLink" href="<?php echo $subscriber->getCardUrl() ?>">
				<span><?php echo clean($subscriber->getDisplayName()) ?></span> </a></b> </td></tr>
			<?php 	} // foreach 
		} 		?>
	</table>
	</div>
<?php } // if ?>