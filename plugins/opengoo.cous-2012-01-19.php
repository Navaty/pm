<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
logger(print_r($_REQUEST,1),"error");

$appeal = $_REQUEST["appeal"];
$data = $_REQUEST["data"];
$isname = $_REQUEST["isname"];
$debug = @$_REQUEST["debug"];
$title = @$_REQUEST["title"];
$person = $_REQUEST["person"];

if(is_array($_REQUEST["m3g"])) {
    $adata = $_REQUEST["m3g"];
} elseif(is_array($_REQUEST["internet"])) {
    $adata = $_REQUEST["internet"];
} elseif(is_array($_REQUEST["post"])) {
    $adata = $_REQUEST["post"];
} elseif(is_array($_REQUEST["broadcasting"])) {
    $adata = $_REQUEST["broadcasting"];
} elseif(is_array($_REQUEST["mobile"])) {
    $adata = $_REQUEST["mobile"];
} elseif(is_array($_REQUEST["wired"])) {
    $adata = $_REQUEST["wired"];
}

$xml = file_get_contents("config.xml");
$xmlObj = simplexml_load_string($xml);
$xmldata = objectsIntoArray($xmlObj);

foreach($xmldata["is"] as $k=>$v) {
 if($isname==$xmldata["is"][$k]["isid"]) {
  $iskey = $k;
  break;
 }
}



$FIO = $person["Фамилия"]." ".$person["Имя"]." ".$person["Отчество"];
$title = $adata["Проблема"]." - ".$FIO;
$appeal = $adata["problem_desc"]."      ";
$email = $person["emailaddress"]."@".$person["emaildomain"];
unset($person["emailaddress"]);
unset($person["emaildomain"]);
unset($adata["problem_desc"]);
unset($adata["place_desc"]);
$data = array_merge($person,$adata);
$data["э-mail"]= $email;

$projectid = $_REQUEST["projectid"];//$xmldata["is"][$iskey]["workspace_id"];
if(!$_REQUEST["assignedbyid"]) {
    $assignedbyid = 36;//$xmldata["is"][$iskey]["assigned_by_id"];
} else {
    $assignedbyid = $_REQUEST["assignedbyid"];
}
$assigned2id = 133;//$xmldata["is"][$iskey]["assigned_to_user_id"];
$subscriptions = $xmldata["is"][$iskey]["subscriptions"];

if(isset($debug)) {
 echo "<pre>";
 print_r($_REQUEST);
} elseif(strlen($title)<5) {
 echo -1;
 echo "\n";
 echo "title length less than 5";
} elseif(!$projectid || !is_numeric($projectid)) {
  logger("cous","error",__FUNCTION__);
  logger($projectid,"error",__FUNCTION__);
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
  echo opengoo_webservice_insert_task($projectid,$title,$assigned2id,$assignedbyid,$appeal,$data,$subscriptions);
}
//echo "<pre>";
//print_r($_REQUEST);
loggerOLD(print_r($_REQUEST,true),"error",__FUNCTION__);
mysql_close($con);
?>
