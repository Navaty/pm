<?php
include_once "statusage.php"; //by almaz - usage control
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

include_once "webservices/includes.php";
$result=array(
	      'taskId' => NULL
	      );
$logdata = json_encode($_REQUEST);
if($_POST && $_POST['appeal']){
  $ProjectId     = '5667';
  $Title	 = 'ФОС Народный Контроль';
  $Assigned2iD	 = 472;
  $AssignedById	 = 106;
  $Appeal	 = $_POST['appeal'];
  
  unset($_POST['appeal']);

  $Data		= $_POST;
  $Subscrip	= array("user_id"=>array(217,325,316,644,472,616,522,556));
//217-Олеся Головкова;204Альбина;171Эльза;51Елена;290Лилия Шаих;325-Суркова Марина;316-Назарова Надежда;644-Базгутдинова Рузана;472-Саженкова Екатерина;
//385-Мансурова Фарюза;522-Кузьмина Алина,616-Григорян Нарине;556-Гарипова Резеда;

  $result['taskId'] = opengoo_webservice_insert_task($ProjectId,$Title,$Assigned2iD,$AssignedById,$Appeal,$Data,$Subscrip);
echo json_encode($result);
}
//file_put_contents("files/logs/inspektor.post.log", date("D M j G:i:s ").$logdata." ip: ".$_SERVER['REMOTE_ADDR']." result: ".$result['taskId']."\n", FILE_APPEND);
?>
