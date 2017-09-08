<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
$projectid = $_REQUEST["projectid"];
echo $project_arr = opengoo_get_projectdescription_by_projectID($projectid);

mysql_close($con);
?>