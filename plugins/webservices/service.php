<?php
include "includes.php";
$result=array(
	      'taskId' => NULL
	      );
if($_POST && $_POST[id] && $_POST['themeId']){
  $ProjectId     = $_POST['id'];
  $Title	 = opengoo_get_projectname_by_projectID($_POST['themeId']);
  $Assigned2iD	 = 472;
  $AssignedById	 = 106;
  $Appeal	 = $_POST['Сообщение'];
  
  unset($_POST['id']);
  unset($_POST['themeId']);
  unset($_POST['Сообщение']);
  
  $Data		= $_POST;
  $Subscrip	= array("user_id"=>array(104,290,325,316,644,472,532,522,616,556, 345));
//104Неля;290Лилия Шаих;325-Суркова Марина;316-Назарова Надежда;522-Кузьмина Алина;644-Базгутдинова Рузана;472-Саженкова Екатерина;385-Мансурова Фарюза;532-Данилова Алёна;616-Нарине Григорян;
//556-Гарипова Резеда; 345 - Хабибрахманова Эльвира;
  $result['taskId'] = opengoo_webservice_insert_task($ProjectId,$Title,$Assigned2iD,$AssignedById,$Appeal,$Data,$Subscrip);
}
echo json_encode($result);
?>
