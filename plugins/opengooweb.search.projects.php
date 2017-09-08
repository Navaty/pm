<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
$projectname = $_REQUEST["projectname"];
print_r(opengoo_search_projects($projectname));

mysql_close($con);
?>