<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
$projectid = 2484;
$sql = "
SELECT text FROM og_project_messages WHERE id in
  (
SELECT object_id
FROM  `og_workspace_objects` 
WHERE  `workspace_id` = '2484'
AND `object_manager` = 'ProjectMessages'
ORDER BY created_on DESC
)";
$res = ssql($sql);
//print_r($res);
if(is_array($res)) {
  foreach($res as $v) {
    echo $v["text"]."&nbsp;&nbsp;|&nbsp;&nbsp;";
  }
}

mysql_close($con);
?>