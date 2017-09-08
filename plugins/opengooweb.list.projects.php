<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
$projectid = $_REQUEST["projectid"];
$projects_arr = opengoo_list_subprojects($projectid);

echo "<"."?xml version=\"1.0\" encoding=\"UTF-8\"?".">\n";

echo "<projects>\n";

if(is_array($projects_arr)) {
 foreach($projects_arr as $k=>$v) {
 if($v["description"]) { $desc = $v["description"];} else { $desc = "нет описания";}
  echo "\t<project>\n";
  echo "\t\t<id>".$v["id"]."</id>\n";
  echo "\t\t<name>".$v["name"]."</name>\n";
  echo "\t\t<description>".$desc."</description>\n";
  echo "\t</project>\n";
 }			   
}

echo "</projects>";

?>