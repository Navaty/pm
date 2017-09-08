<?php

include_once "statusage.php"; //by almaz - usage control
function file_add($Filename,$Description='',$userid) {
$sql = "INSERT  INTO  og_project_files (
`filename` ,
`description` ,
`is_private` ,
`is_important` ,
`is_locked` ,
`is_visible` ,
`expiration_time` ,
`comments_enabled` ,
`anonymous_comments_enabled` ,
`created_on` ,
`created_by_id` ,
`updated_on` ,
`updated_by_id` ,
`trashed_on` ,
`trashed_by_id` ,
`checked_out_on` ,
`checked_out_by_id` ,
`archived_on` ,
`archived_by_id` ,
`was_auto_checked_out` ,
`type` ,
`url` ,
`mail_id`
)
VALUES (
 '$Filename',  '$Description',  '0',  '0',  '0',  '1',  '0000-00-00 00:00:00',  '1',  '0',  NOW(),  '$userid',  NOW(),  '$userid',  '0000-00-00 00:00:00',  '0',  '0000-00-00 00:00:00',  '0',  '0000-00-00 00:00:00',  '0',  '0',  '0',  '',  '0’
)
";
}

function file_add_revision($FileID,$FileType=0,$RepositoryHash,$MimeType,$FileSize,$UserID) {
$sql = "INSERT  INTO  `fengoffice`.`og_project_file_revisions` (
`file_id` ,
`file_type_id` ,
`repository_id` ,
`thumb_filename` ,
`revision_number` ,
`comment` ,
`type_string` ,
`filesize` ,
`created_on` ,
`created_by_id` ,
`updated_on` ,
`updated_by_id` ,
`trashed_on` ,
`trashed_by_id`
)
VALUES (
'$FileID',  '$FileType',  '$RepositoryHash',  '',  '1',  '-- Начальная версия --',  '$MimeType',  '$FileSize',  NOW(),  '$UserID',  NOW(),  '$UserID',  '0000-00-00 00:00:00',  '0'
)
";
}

function file_get_type_id($Ext) {
  $sql = "SELECT id FROM og_file_types WHERE extension = '$Ext'";
  $res = ssql($sql);
  return $res[1][id];
}