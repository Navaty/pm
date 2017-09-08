<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
$taskid = $_REQUEST["taskid"];
$comment = $_REQUEST["comment"];
$userid = $_REQUEST["userid"];
//echo $project_arr = opengoo_get_projectdescription_by_projectID($projectid);
echo $add_task_commentd = opengoo_insert_task_comment($taskid,$comment,$userid);

mysql_close($con);
?>