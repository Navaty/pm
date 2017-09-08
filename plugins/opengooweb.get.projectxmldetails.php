<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
$projectid = $_REQUEST["projectid"];
$project_xml = opengoo_get_project_xml($projectid);

echo "<"."?xml version=\"1.0\" encoding=\"UTF-8\"?".">\n";

echo "<projects>\n";

preg_match("/(\<xml[\d\D]*\>)/", $project_xml, $result);
echo $data = $result[0];

echo "</projects>";

?>