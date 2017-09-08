<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
$projectid = 3408;
$sql = "
SELECT text,title FROM og_project_messages WHERE id in
  (
SELECT object_id
FROM  `og_workspace_objects` 
WHERE  `workspace_id` = '$projectid'
AND `object_manager` = 'ProjectMessages'
ORDER BY created_on DESC
)";
$res = ssql($sql);
//print_r($res);
if(is_array($res)) {
  foreach($res as $v) {
    echo "<option value='".$v["text"]."'>".$v["title"]."</option>\n";
  }
}

mysql_close($con);
?>