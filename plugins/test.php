<?php
include_once "statusage.php"; //by almaz - usage control
include_once "functions.php";
include_once "db.inc.php";
//print_r(apc_sma_info());

apply_project_params(test,(array)2586);

function apply_project_params($taskid,$projects) {
$taskid = '638096';
$priority_values = array ('100', '200', '300', '400');
foreach ($projects as &$project) {
	$projects_params = (array) simplexml_load_string(opengoo_get_project_xml($project));
	if(isset($projects_params['worktime']) && is_numeric($projects_params['worktime'])) 
		set_worktime($taskid,$projects_params['worktime']); else set_worktime($taskid);
	if(isset($projects_params["priority-class"]) && in_array($projects_params["priority-class"], $priority_values, true)) 
		ssql("update og_project_tasks set priority = ".$projects_params["priority-class"]." WHERE id = '$taskid'");
   }
}

function set_worktime($taskid, $worktime=72) {
 $begin = get_start_date($taskid);
 $end = $begin[1]['start_date'];
//Достали время
 if (DateTime::createFromFormat('Y-m-d H:i:s', $end) !== FALSE) {
  // it's a date
	while($worktime>0) {//Работаем, пока к задаче не добавим все время
	  if($worktime>24) //Добавляем по дням, если возможно
		$end = (date('Y-m-d H:i:s', strtotime($end . " +24 hours")));
			else $end = (date('Y-m-d H:i:s', strtotime($end . " +".$worktime." hours"))); //Ну или остаток дня
		if (!isWeekend($end)) $worktime = $worktime - 24; //Вычитаем рабочие часы, выходные не учитываем
	}
  ssql("update og_project_tasks set due_date = '$end' WHERE id = '$taskid'"); //Обновляем информацию по задаче 
 }
 return true;
}

function isWeekend($date) {
    return (date('N', strtotime($date)) >= 6);
}

function get_start_date($taskid) {
 return ssql("SELECT start_date FROM og_project_tasks WHERE id = '$taskid'");
}

?>
