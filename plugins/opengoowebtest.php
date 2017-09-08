<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");

include ("functions.php");

$appeal = $_REQUEST["appeal"];
$data = @$_REQUEST["data"];
$isname = $_REQUEST["isname"];
$debug = @$_REQUEST["debug"];
$title = @$_REQUEST["title"];

$xml = file_get_contents("config.xml");
$xmlObj = simplexml_load_string($xml);
$xmldata = objectsIntoArray($xmlObj);

foreach($xmldata["is"] as $k=>$v) {
 if($isname==$xmldata["is"][$k]["isid"]) {
 $iskey = $k;
 break;
 }
}



$projectid = $xmldata["is"][$iskey]["workspace_id"];
$assignedbyid = $xmldata["is"][$iskey]["assigned_by_id"];
$assigned2id = $xmldata["is"][$iskey]["assigned_to_user_id"];
$subscriptions = $xmldata["is"][$iskey]["subscriptions"];

echo "<pre>";
print_r($_REQUEST);

if(isset($debug)) {
 echo "<pre>";
 print_r($_REQUEST);
} elseif(strlen($title)<5) {
 echo -1;
 echo "\n";
 echo "title length less than 5";
} elseif(!$projectid) {
 echo -1;
 echo "\n";
 echo "no projectid";
} elseif(!$assigned2id) {
 echo -1;
 echo "\n";
 echo "no assigned2id user";
} elseif(!$assignedbyid) {
 echo -1;
 echo "\n";
 echo "no assignedbyid user";
} elseif(strlen($appeal)<5 ) {
 echo -1;
 echo "\n";
 echo "appeal less than 5";
} else {
print_r($_REQUEST);
//  echo opengoo_webservice_insert_task($projectid,$title,$assigned2id,$assignedbyid,$appeal,$data,$subscriptions);
}
mysql_close($con);
?>