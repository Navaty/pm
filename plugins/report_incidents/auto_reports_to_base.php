<?php
//include ("../db.inc.php");
//include ("functions_find_tasks.php");
include ("../connect_db_func.php");
ini_set('max_execution_time', 9999);
//Удаляем старые данные
        $query_truncate_table_first = "TRUNCATE TABLE `report_incidents`";
        ssql($query_truncate_table_first);
// Берем все задачи из проекта "ВСЕ ИНЦИДЕНТЫ"
$project_task_id = 2794;
$post = $_REQUEST;
$array_open_prev_month = Array();
if(isset($post['rangedata']) && $post['rangedate']=='true') {
	$starttime = $post["starttime"];
	$endtime = $post["endtime"];
}
else {
// Месяц берем предыдущий, т.к. отчет идет за предыдущий месяц
	$month = date("m")-1;
	if($month < 1) {
		$month= 12;
		$starttime = '01.'.$month.'.'.(date("Y")-1);
        	$endtime = date("t", mktime(0, 0, 0, $month, 1, (date("Y")-1))).'.'.$month.'.'.(date("Y")-1);
	}
	else {
		$starttime = '01.'.$month.'.'.date("Y");
		$endtime = date("t", mktime(0, 0, 0, $month, 1, date("Y"))).'.'.$month.'.'.date("Y");
	}

	$month_prev = $month -1;
	if($month_prev < 1) {
                $month_prev = 12;
                $starttime_prev = '01.'.$month_prev.'.'.(date("Y")-1);
                $endtime_prev = date("t", mktime(0, 0, 0, $month_prev, 1, (date("Y")-1))).'.'.$month_prev.'.'.(date("Y")-1);
        }
        else {
                $starttime_prev = '01.'.$month_prev.'.'.date("Y");
                $endtime_prev = date("t", mktime(0, 0, 0, $month_prev, 1, date("Y"))).'.'.$month_prev.'.'.date("Y");
        }

	$tasks_open_prev_month = project_task_count($project_task_id, $starttime_prev, $endtime_prev, "open", "yes");
}

// Берем открытые и закрытые задачи
$tasks_close = project_task_count($project_task_id,$starttime,$endtime,"close","yes");
foreach($tasks_close as $item){
	$array_tasks_close[] = $item["task_id"];
}
$tasks_close = array_unique($array_tasks_close);

$tasks_open = project_task_count($project_task_id,$starttime,$endtime,"open","yes");
foreach($tasks_open as $item){
        $array_tasks_open[] = $item["task_id"];
}
$tasks_open = array_unique($array_tasks_open);

//print_r($tasks_close);
//print_r($tasks_open);
//echo '<pre>';

$query_bind = "SELECT
			ricl.name as classifier, ricl.id as classifier_id, ris.name as source, ris.id as source_id
		FROM
			report_incidents_criteria as ric, report_incidents_sources as ris, report_incidents_classifier as ricl 
		WHERE
			ric.active=1
		AND
			ricl.id = ric.classifier
		AND
			ris.id = ric.source";

 $result_bind = ssql($query_bind);

//print_r($result_bind);

foreach($result_bind as $item_bind) {
	$classifier = htmlspecialchars_decode($item_bind["classifier"]);
	$source = htmlspecialchars_decode($item_bind["source"]);
	$classifier_id = $item_bind["classifier_id"];
        $source_id = $item_bind["source_id"];

	$overdue_tasks = Array();
	$overdue_prev_tasks = Array();
	$good_close = 0;
	$bad_close = 0;
	$count_close_task = 0;

	foreach($tasks_close as $item_close) {
		$task_id_close = $item_close;
		$query_object_properties = "SELECT
							*
						FROM
							`og_object_properties`
						WHERE
							rel_object_id = ".$task_id_close."
						AND
							rel_object_manager = 'ProjectTasks'";

		$res_object_properties = ssql($query_object_properties);

		$sourceprop = '';
		$classifierprop = '';

		foreach((array)$res_object_properties as $item_object_properties) {

			switch($item_object_properties["name"]) {
				case "Источник":
					$sourceprop = $item_object_properties["value"];
				break;
				case "Классификатор":
                                        $classifierprop = $item_object_properties["value"];
                                break;
			}
//print_r($item_object_properties);
		}

//		echo '<br />-------- '.$sourceprop.' --- '.$classifierprop;
		if(($sourceprop == $source)&&($classifierprop == $classifier)) {
			$count_close_task++;
			$query_created_completed_on = "SELECT created_on, completed_on FROM `og_project_tasks` WHERE id = ".$task_id_close;
			$created_completed_on = ssql($query_created_completed_on);
			foreach($created_completed_on as $item_created_completed_on) {
				$created_on = $created_completed_on[1]["created_on"];
				$completed_on = $created_completed_on[1]["completed_on"];
				$datetime = explode(' ', $created_on);
				$date = explode('-', $datetime[0]);
				$time = explode(':', $datetime[1]);
				$createtask = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
				$day_of_week = date("w", $createtask);
				switch($day_of_week) {
        				case "0": //Sunday
                				$plus = 259200;
			                break;
        			        case "5": //Friday
                			        $plus = 432000;
	                		break;
	        	        	case "6": //Saturday
	        	        		$plus = 345600;
		        	        break;
        		        	default:
                				$plus = 172800;
		                	break;
	      			}
				$needtoclose = $createtask + $plus;
				$datetime = explode(' ', $completed_on);
        	        	$date = explode('-', $datetime[0]);
	                	$time = explode(':', $datetime[1]);
		                $closetask = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
				if($closetask > $needtoclose) {
//					echo $task_id_close.' '.date("d-m-Y H:i:s", $createtask).' - '.date("d-m-Y H:i:s", $closetask).'<br />';
					$overdue_tasks[] = $task_id_close;
					$bad_close++;
				}
				else {
					$good_close++;
				}
			}
		}
	}
//	echo '<br /><br /><br /><br />'.$source.' '.$classifier.'<br />Вовремя закрытых задач: '.$good_close.'<br />Просроченных закрытых задач: '.$bad_close.'<br />Всего закрытых задач: '.($good_close+$bad_close);

	$bad_open = 0;
	$good_open = 0;
	$count_open_tasks = 0;
//	$good_open_tasks = 0;
//	$bad_open_tasks = 0;
//	echo '<br />';
	foreach($tasks_open as $item_open) {
		$task_id_open = $item_open;
		$query_object_properties = "SELECT * FROM `og_object_properties` WHERE rel_object_id = ".$task_id_open." AND rel_object_manager = 'ProjectTasks'";
                $res_object_properties = ssql($query_object_properties);
                $sourceprop = '';
                $classifierprop = '';
                foreach($res_object_properties as $item_object_properties) {
                        switch($item_object_properties["name"]) {
                                case "Источник":
                                        $sourceprop = $item_object_properties["value"];
                                break;
                                case "Классификатор":
                                        $classifierprop = $item_object_properties["value"];
                                break;
                        }
                }
                if(($sourceprop == $source)&&($classifierprop == $classifier)) {
			$count_open_tasks++;
		        $query_created_on = "SELECT created_on FROM `og_project_tasks` WHERE id = ".$task_id_open;
        		$array_created_on = ssql($query_created_on);
	        	foreach($array_created_on as $item_created_on) {
        	        	$created_on = $item_created_on["created_on"];
	                	$datetime = explode(' ', $created_on);
		                $date = explode('-', $datetime[0]);
        		        $time = explode(':', $datetime[1]);
                		$createtask = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
		                $day_of_week = date("w", $createtask);
        		        switch($day_of_week) {
                		        case "0": //Sunday
                        		        $plus = 259200;
	                        	break;
	        	                case "5": //Friday
        	        	                $plus = 432000;
                	        	break;
	                	        case "6": //Saturday
        	                	        $plus = 345600;
	                	        break;
        	                	default:
                	                	$plus = 172800;
	                	        break;
        	        	}
	                	$needtoclose = $createtask + $plus;
		                if(time() > $needtoclose) {
        		                $bad_open++;
					$overdue_tasks[] = $task_id_open;
                		}
		                else {
        		                $good_open++;
                		}
	        	}
		}
	}

	if(!isset($post['rangedate'])) {
//		echo '<pre>';
//		var_dump($tasks_open_prev_month);
	        foreach($tasks_open_prev_month as $this_task_id_prev_open) {
			$task_id_prev_open = $this_task_id_prev_open["task_id"];
                	$query_object_properties = "SELECT
								*
							FROM
								`og_object_properties`
							WHERE
								rel_object_id = ".$task_id_prev_open."
							AND
								rel_object_manager = 'ProjectTasks'";

	                $res_object_properties = ssql($query_object_properties);
        	        $sourceprop = '';
                	$classifierprop = '';
	                foreach((array)$res_object_properties as $item_object_properties) {
        	                switch($item_object_properties["name"]) {
                	                case "Источник":
                        	                $sourceprop = $item_object_properties["value"];
                                	break;
	                                case "Классификатор":
        	                                $classifierprop = $item_object_properties["value"];
                	                break;
                        	}
	                }
//			echo $sourceprop.' == '.$source.'         '.$classifierprop.' == '.$classifier.'<br />';
        	        if(($sourceprop == $source)&&($classifierprop == $classifier))
				$overdue_prev_tasks[] = $task_id_prev_open;
	        }
	}

//	echo '<br />'.$source.' '.$classifier.'<br />Непросроченных открытых задач: '.$good_open.'<br />Просроченных открытых задач: '.$bad_open.'<br />Всего открытых задач: '.($good_close+$bad_close);
//	var_dump($overdue_tasks);
	$query_write_result = "INSERT INTO report_incidents (source_id, classifier_id, count_tasks, solved, overdue, overdue_tasks, overdue_tasks_prev_month) VALUES (".$source_id.", ".$classifier_id.", ".($count_close_task + $count_open_tasks).", ".$count_close_task.", ".($bad_close + $bad_open).", '".json_encode($overdue_tasks)."', '".json_encode($overdue_prev_tasks)."')";
//	echo $query_write_result.'<br/ >';
	$result = usql($query_write_result);
//	var_dump($result);
}

?>
