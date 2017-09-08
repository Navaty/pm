<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");

$appeal = $_REQUEST["appeal"];
$data = $_REQUEST["data"];
$isname = $_REQUEST["isname"];
$debug = @$_REQUEST["debug"];
$title = @$_REQUEST["title"];
$autoclose = @$_REQUEST["autoclose"];
$projectids = @$_REQUEST["projectid"];
$mainprojectname = @$_REQUEST["mainprojectname"];
$mainprojectlevel = @$_REQUEST["mainprojectlevel"];
$email_prefix = @$_REQUEST["email_prefix"];
$email_suffix = @$_REQUEST["email_suffix"];
$data["Контактный_э-адрес"] = $email_prefix ."@".$email_suffix;

logger(print_r($_REQUEST,1),"error");


if(is_array($projectids)) { // Смотрим передаются ли со стороны клинета проекты
  foreach($projectids as $k=>$v) { 
    if($v) {
      $data[$k] = opengoo_get_projectname_by_projectID($v);
    }
  }
}
logger("mobileS","error",__FUNCTION__);
logger(print_r($_REQUEST,true),"error",__FUNCTION__);
logger("mobileE","error",__FUNCTION__);

$xml = file_get_contents("config.xml"); // читаем содержимое xml файла
$xmlObj = simplexml_load_string($xml);
$xmldata = objectsIntoArray($xmlObj); // xml конвертируем в массив

foreach($xmldata["is"] as $k=>$v) {
 if($isname==$xmldata["is"][$k]["isid"]) {
  $iskey = $k;
  break;
 }
}
 
$projectid = $xmldata["is"][$iskey]["workspace_id"];
if(!$_REQUEST["assignedbyid"]) {
    $assignedbyid = $xmldata["is"][$iskey]["assigned_by_id"];
} else {
    $assignedbyid = $_REQUEST["assignedbyid"];
}
/* 
Если находим мой FengID, то isname переделываем на test
*/
//if($assignedbyid=='328') {
//$isname = 'testlenaphp';
//}
/*
*/
$subscriptions = $xmldata["is"][$iskey]["subscriptions"];
$assigned2id4project = opengoo_get_project_role($projectids[$mainprojectname],"исполнитель",$mainprojectlevel);
if($assigned2id4project) {
  $assigned2id = $assigned2id4project;
} elseif($_REQUEST["assigned2id"]) {
  $assigned2id = $_REQUEST["assigned2id"];
} else {
  $assigned2id = $xmldata["is"][$iskey]["assigned_to_user_id"];
}

if(isset($debug)) {
 echo "<pre>";
 print_r($_REQUEST);
} elseif(strlen($title)<5) {
 echo -1;
 echo "\n";
 echo "title length less than 5";
 logger("title less than 5","error",__FUNCTION__);
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
} elseif(strlen($appeal)<3 ) {
 echo -1;
 echo "\n";
 echo "appeal less than 3";
} else {
  // Создаем название задачи
  // Добавляем задачу в СУП
  switch($isname) {
  case "stiseochered":
    $title = opengoo_get_projectname_by_projectID($projectids[$mainprojectname])." : ".$data[Фамилия]." ".$data[Имя]." ".$data[Отчество];
    echo $TASKID = opengoo_webservice_insert_task($projectid,$title,$assigned2id,$assignedbyid,$appeal,$data,$subscriptions);
    break;
  
  case "oktell":
    $title = opengoo_get_projectname_by_projectID($projectids[$mainprojectname])." : ".$data[Фамилия]." ".$data[Имя]." ".$data[Отчество];
    $TASKID = opengoo_webservice_insert_task($projectid,$title,$assigned2id,$assignedbyid,$appeal,$data,$subscriptions);
    echo $TASKID;
    break;

  case "cceducation":
    logger("test","error");
    $title = opengoo_get_projectname_by_projectID($projectids[$mainprojectname])." : ".$data[Фамилия]." ".$data[Имя]." ".$data[Отчество];
    $TASKID = opengoo_webservice_insert_task($projectid,$title,$assigned2id,$assignedbyid,$appeal,$data,$subscriptions);
    echo $TASKID;
    break;
  case "mobilefeedback":
    $title = "test-".$title;
    $TASKID = opengoo_webservice_insert_task($projectid,$title,$assigned2id,$assignedbyid,$appeal,$data,$subscriptions);
    echo $TASKID;
    logger("taskid for mobile:".$TASKID,"error",__FUNCTION__);
    break;

  case "moninfomats":
    $oldtaskid = opengoo_search_task_by_titleName($projectid,"{feng} ".$title);
    if($oldtaskid>0) {
      logger("comment added to taskid:".$oldtaskid,"error",__FUNCTION__);
      $comment = $appeal;
      $comment_res = opengoo_insert_task_comment($oldtaskid,$comment,$assignedbyid);
      echo $oldtaskid;
    } else {
      logger("comment not found, adding task","error",__FUNCTION__);
      $TASKID = opengoo_webservice_insert_task($projectid,$title,$assigned2id,$assignedbyid,$appeal,$data,$subscriptions);
      echo $TASKID;
    }
    break;

  case "incidentinfomats":
    $title = $data["Классификация_инцидента"]." : ".$data[Фамилия]." ".$data[Имя]." ".$data[Отчество];
    $TASKID = opengoo_webservice_insert_task($projectid,$title,$assigned2id,$assignedbyid,$appeal,$data,$subscriptions);
    echo $TASKID;
    break;

  default:
    $TASKID = opengoo_webservice_insert_task($projectid,$title,$assigned2id,$assignedbyid,$appeal,$data,$subscriptions);
    echo $TASKID;
    break;
  }

  // Добавляем задачи также в другие проекты
  if(is_array($projectids)) {
    foreach($projectids as $k=>$v) {
      if($v>0) {
	$projectid_temp = opengoo_insert_workspace_objects($TASKID,$v,$assignedbyid);
      }
    }
  }
  
  switch($isname) {
  case "kindergarten":
    if($data["org_obr_id"]>0) {
      $org_obr_id = opengoo_insert_workspace_objects($TASKID,$data["org_obr_id"],$assignedbyid);
    }
    if($data["problem_id"]>0) {
      $problem_id = opengoo_insert_workspace_objects($TASKID,$data["problem_id"],$assignedbyid);
    }
    break;

  case "moninfomats":
    $infomatname = "Инфомат".$data[Номер];
    $projects = opengoo_search_projects($infomatname);
    if(is_array($projects)) {
      logger("infomat found, adding task into it","error",__FUNCTION__);
      $infomat_id = opengoo_insert_workspace_objects($TASKID,$projects[1][id],$assignedbyid);
    } else {
      logger("infomat not found,creating project and adding task into it","error",__FUNCTION__);
      $infomatprojectid = opengoo_add_project($infomatname,$data[Инфомат],
					      "'60','7','916','917','1070','999999','0','0','0','0'",
					      '6',10000029,
					      $UserID=25,$ProjectColor=18);
      logger("adding project permission","error",__FUNCTION__);
      $infomat_id = opengoo_insert_workspace_objects($TASKID,$infomatprojectid,$assignedbyid);
    }
    break;

  case "incidentinfomats":
    if($d["incidenttype"]>0) {
      $newprojectid = opengoo_insert_workspace_objects($TASKID,$d["incidenttype"],$assignedbyid);
    }
    if($d["infomatno"]>0) {
      $newprojectid = opengoo_insert_workspace_objects($TASKID,$d["infomatno"],$assignedbyid);
    }
    if($d["service"]>0) {
      $newprojectid = opengoo_insert_workspace_objects($TASKID,$d["service"],$assignedbyid);
    }
    break;

  default:
    break;
  }

  if($autoclose==1) {
    $taskid = opengoo_complete_task($TASKID,$assignedbyid);
  } else {
    logger("nothing","error",__FUNCTION__);
  }
  if($TASKID > 1) {
    logger("taskid: $TASKID","error",__FUNCTION__);
  } else {
    logger("no taskid $TASKID","error",__FUNCTION__);
  }
}
mysql_close($con);
?>
