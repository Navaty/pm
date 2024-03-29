<?php
	$submit_url = get_url('contact', 'export_to_csv_file');
	$genid = gen_id();
?>
<script>
og.download_exported_file = function() {
	if (!Ext.isIE) {
		location.href = og.getUrl('contact', 'download_exported_file');
		og.openLink(og.getUrl('contact', 'init'));
	}
}
</script>

<form style="height:100%;background-color:white" id="<?php echo $genid ?>csvexport" name="<?php echo $genid ?>csvexport" class="internalForm" method="post" enctype="multipart/form-data" action="<?php echo $submit_url ?>">

<div class="file">
<div class="coInputHeader">
<div class="coInputHeaderUpperRow">
<div class="coInputTitle">
	<table style="width:535px"><tr><td><?php echo ($import_type == 'contact' ? lang('export contacts to csv') : lang('export companies to csv')) ?></td>
	<?php if (!isset($result_msg)) { ?>
	<td style="text-align:right"><?php echo submit_button(lang('export'), 'e', array('style'=>'margin-top:0px;margin-left:10px', 'tabindex' => '10','id' => $genid.'csv_export_submit1', 'onclick'=>"javascript:og.openLink(og.getUrl('contact', 'export_to_csv_file'), {callback:og.download_exported_file});")) ?></td>
	<?php } //if ?>
	<td><div id="<?php echo $genid."downloadlink"?>" style="padding-left:20px; font-size: 9pt; vertical-align: middle;"></div></td>
	</tr></table>
</div>
</div>
</div>
	<div class="coInputMainBlock adminMainBlock">
	<?php if (!isset($result_msg)) { ?>
	<table style="width:350px;"><tr><th style="text-align:center;" colspan="2"><?php echo lang('fields to export'); ?></th></tr>
	
	<?php
		if ($import_type == 'contact') 
			$contact_fields = Contacts::getContactFieldNames();
		else $contact_fields = Companies::getCompanyFieldNames();
		
		$isAlt = false;
		$i = 0; $label_w = $label_h = $label_o = false;
		foreach ($contact_fields as $c_field => $c_label) {
			if (str_starts_with($c_field, 'contact[w') && !$label_w) {
				?><tr><td colspan="3" style="text-align:center;"><b><?php echo lang('work')?></b></td></tr> <?php
				$label_w = true;
			} else if (str_starts_with($c_field, 'contact[h') && !$label_h) {
				?><tr><td colspan="3" style="text-align:center;"><b><?php echo lang('home')?></b></td></tr> <?php
				$label_h = true;
			} else if (str_starts_with($c_field, 'contact[o') && !$label_o) {
				?><tr><td colspan="3" style="text-align:center;"><b><?php echo lang('other')?></b></td></tr> <?php
				$label_o = true;
			}
			
			$isAlt = !$isAlt;
			$i++;
	?>	
				<tr<?php echo ($isAlt ? ' class="altRow"': '') ?>>
				<td><?php echo checkbox_field('check_'.$c_field, true, array('tabindex' => 20 + $i)) ?></td><td><?php echo $c_label ?></td></tr>
	<?php	
		} //foreach ?>
	</table>

	<br>
	<div><table style="width:535px">
		<tr><td><?php echo submit_button(lang('export'), 'e', array('style'=>'margin-top:0px;margin-left:10px','id' => $genid.'csv_export_submit1', 'tabindex' => '100', 'onclick'=>"javascript:og.openLink(og.getUrl('contact', 'export_to_csv_file'), {callback:og.download_exported_file});")) ?></td></tr></table>
	</div>
	
	</div>
	<?php } else { ?>
	<div><b><?php echo $result_msg ?></b></div>
	<?php } ?>
</div>
</form>

<?php if (isset($_SESSION['contact_export_filename'])) {?>
<script>
	if (Ext.isIE) {
		var el = Ext.get('<?php echo $genid ?>downloadlink');
		var html = '<a class="internalLink" href="javascript:location.href=\''+og.getUrl('contact', 'download_exported_file')+'\';" onclick="og.openLink(og.getUrl(\'contact\', \'init\'));">'+lang('download')+'</a><span class="desc" style="font-size=7pt;"><br>'+lang('click here to download the csv file')+'</span>';
		el.insertHtml('beforeEnd', html);
	}
</script>
<?php } ?>
<script>
	btn = Ext.get('<?php echo $genid ?>csv_export_submit1');
	if (btn != null) btn.focus();
</script>