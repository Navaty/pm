<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");

include ("functions.php");

$action = $_REQUEST["action"];
$ObjectType = $_REQUEST["objecttype"];//default => "alltypes";
$projectid = $_REQUEST["projectid"];

switch($action) {
case "list_workspace_objects":
  $arr = webservice_list_workspace_objects($projectid,$ObjectType);
  break;
}
print_r( array2xml($arr));
//print_r($arr);
mysql_close();
file_put_contents("webservices.post.log", date("D M j G:i:s ").$arr."\n", FILE_APPEND);
?>
