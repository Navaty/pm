<?php
include ("../connect_db_func.php");
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

function bz_pm_get_xml_sub_projects2($ProjectID) {
  $sub_projects_xml = file_get_contents("http://pm.citrt.net/plugins/opengooweb.list.projects.php?projectid=".$ProjectID);
  //Creating Instance of the Class
  $xmlObj = new XmlToMassive($sub_projects_xml);
  //Creating Array
  $sub_projects_arr = $xmlObj->createArray();
  return $sub_projects_arr;
}

function give_me_sons_data($project_id) {
     $role = bz_pm_get_xml_sub_projects2($project_id);
     return $role["projects"]["project"];
}

function number_tasks_from_filter($array_tasks) {
	if (!$array_tasks) return 0;
     $project_id = 4051;
/*     $role = bz_pm_get_xml_sub_projects2($project_id);
     $class_error_array = $role["projects"]["project"];*/
     $klassificator_id = 4732;
     $class_error_array = give_me_sons_data($klassificator_id);
/*	var_dump($class_error_array);*/
     $filter_array = Array();
     foreach($array_tasks as $close_task) {

          $query_object_properties = "SELECT
                                            *
                                      FROM
                                            `og_object_properties`
                                      WHERE
                                            rel_object_id = ".$close_task."
                                      AND
                                            rel_object_manager = 'ProjectTasks'";

          $res_object_properties = ssql($query_object_properties);
	  //print_r($res_object_properties);
	  $class_error_prop = '';
          $type_problem_prop = '';
          foreach($res_object_properties as $item_object_properties) {
               switch($item_object_properties["name"]) {
     	     	     case "Тип_ошибки":
	     		$class_error_prop = $item_object_properties["value"];
                     break;
               }
          }
          foreach($class_error_array as $class_error) {
             $class_error_name = $class_error['name'];
  	     $class_error_id = $class_error['id'];
             if($class_error_prop == $class_error_name) {
		$filter_array[$class_error_id][] = $close_task;
	     }
	     else {
			//Для проверки
	     }
          }
     }
     return $filter_array;
}

function datetime_to_timestamp($datetime_string) {
        $datetime = explode(' ', $datetime_string);
        $date = explode('-', $datetime[0]);
        $time = explode(':', $datetime[1]);
        $result = mktime($time[0]+4, $time[1], $time[2], $date[1], $date[2], $date[0]);
	return $result;
}

function get_holidays_in_period($startdate, $enddate) {
	$count_holiday_days = 0;
	while($startdate < $enddate) {
		if((date("w", $startdate)==6)||(date("w", $startdate)==0)) {
			$count_holiday_days++;
		}
		$startdate += 60*60*24;
	}
	return $count_holiday_days*60*60*24;
}

function get_work_time_in_period($startdate, $enddate) {
        $timework = 0;
	$localenddate = 0;

	while($startdate < $enddate) {

		$flag1 = false;
		$flag2 = false;

		$startdayofweek = date("w", $startdate);
		$startday = date("d", $startdate);
	        $startmonth = date("m", $startdate);
	        $startyear = date("Y", $startdate);

		if(($startdayofweek == 6)||($startdayofweek == 0)) {
//			echo 'Начало сб или вс <br />';
			$flag1 = true;
			if($startdayofweek == 6)
				$startday += 2;
			else
				$startday++;
	               	if($startday > date("t", $startdate)) {
	               		$startday = $startday - date("t", $startdate);
	                        $startmonth++;
	       	                if($startmonth > 12) {
	               	        	$startyear++;
					$startmonth = 1;
	                        }
	       	        }
			$starttime = mktime(0, 0, 0, $startmonth, $startday, $startyear);
		}

		$localenddate = $startdate + (60*60*24);
//		echo '$startdate = '.date("d-m-Y H:i:s", $startdate).' $localenddate = '.$localenddate.' '.date("d-m-Y H:i:s", $localenddate).'<br />';
//		echo 'Берем отрезок '.$startdate.' - '.$localenddate.'<br />';

		$enddayofweek = date("w", $localenddate);
                $endday = date("d", $localenddate);
                $endmonth = date("m", $localenddate);
                $endyear = date("Y", $localenddate);

		if(($enddayofweek == 6)||($enddayofweek == 0)) {
			$flag2 = true;
//			echo 'Конец сб или вс <br />';
                        if($enddayofweek == 0)
                                $endday--;
                        if($endday < 1) {
                                $endmonth--;
                                if($endmonth < 1) {
                                        $startyear--;
                                        $startmonth = 12;
                                }
                                $endday = date("t", mktime(23, 59, 59, $endmonth, 1, $endyear));
                        }
//			echo $endday.'-'.$endmonth.'-'.$endyear.'<br />';
                        $localenddate = mktime(0, 0, 0, $endmonth, $endday, $endyear);
//			echo 'Изменили дату окончания, теперь она '.date("d-m-Y H:i:s", $localenddate).'<br />';
                }
		if($flag1&&$flag2) {

		}
		else {
				if($enddate > $localenddate) {
					if($localenddate > $startdate) {
//						echo $startdate.' - '.$localenddate.'<br />';
						$timework += $localenddate - $startdate;
					}
				}
				else {
//					echo $startdate.' - '.$enddate.'<br />';
//					echo 'Проигнорили новую дату окончания<br />';
					$timework += $enddate - $startdate;
				}
		}
		if($flag2) {
			$startdate = mktime(0, 0, 0, date("m", $localenddate), date("d", $localenddate)+2, date("Y", $localenddate));
//			echo 'Поменяли дату начала, конец был '.date("d-m-Y H:i:s", $localenddate).' начало стало '.date("d-m-Y H:i:s", $startdate).'<br />';
		}
		else {
			$startdate = mktime(date("H", $startdate), date("i", $startdate), date("s", $startdate), date("m", $startdate), date("d", $startdate)+1, date("Y", $startdate));
		}
//		echo '<br />';
	}
        return $timework;
}

function count_tasks($tasks_from_filter) {
     $klassificator_id = 4732;
     $class_error_array = give_me_sons_data($klassificator_id);
     $result_statistics = Array();
     foreach($class_error_array as $class_error) {
          $statistics = Array();
          $class_error_name = $class_error['name'];
          $class_error_id = $class_error['id'];
               $statistics["class_error_id"] = $class_error_id;
	       $statistics["class_error_name"] = $class_error_name;
	       if(isset($tasks_from_filter[$class_error_id])) {
		       $statistics["count"] = count($tasks_from_filter[$class_error_id]);
		       $statistics["tasks"] = $tasks_from_filter[$class_error_id];
//		       echo 'Открытых задач по комбинации: '.$class_error_id.' = '.count($tasks_from_filter[$class_error_id]).'<br />';
	       }
	       else {
//	   	       echo 'Открытых задач по комбинации: '.$class_error_id.' = 0<br />';
		       $statistics["count"] =  0;
		       $statistics["tasks"] = Array();
	       }
     	       $result_statistics[] = $statistics;
    }
//	var_dump($result_statistics);
    return $result_statistics;
}
//var_dump($open_tasks_from_filter);
//var_dump($close_tasks_from_filter);

function get_info_about_task($idtask, $current_time) { // Получаем полную информацию по работе с задачей
	$taskinfo = Array();
	$taskinfo["taskid"] = $idtask;
	/* Смотрим даты создания и закрытия задач */
	$query_created_completed_on = "SELECT created_on, completed_on, assigned_to_company_id FROM `og_project_tasks` WHERE id = ".$idtask;
	$created_completed_on = ssql($query_created_completed_on);
	/* Для каждого результата вычисляем дату создания и закрытия, чтобы определить задача просрочена или нет */
	/* Дата создания и закрытия задачи в timestamp */
	$createtask = datetime_to_timestamp($created_completed_on[1]["created_on"]);
	$completetask = datetime_to_timestamp($created_completed_on[1]["completed_on"]);
	if($completetask < $createtask) {
		$taskinfo["tasklive"] = $current_time - $createtask - get_holidays_in_period($createtask, $current_time);
	}
	else {
		$taskinfo["tasklive"] = $completetask - $createtask - get_holidays_in_period($createtask, $completetask);
/*		if($idtask == 376756) {
			echo 'Задача открыта в '.$createtask.' Задача закрыта в '.$completetask.' Выходные дни '.get_holidays_in_period($createtask, $completetask).'<br />';
		}*/
//		echo $idtask.' '.get_holidays_in_period($createtask, $completetask).'<br />';
	}
	$taskinfo["companiesworktime"] = get_info_about_workers_time($idtask, $current_time, $createtask, $completetask, $created_completed_on[1]["assigned_to_company_id"]);
//echo '<pre>';
//var_dump($taskinfo);
	return $taskinfo;
}

function get_info_about_lukina_task($idtask, $current_time) { // Получаем полную информацию по работе с задачей
        $taskinfo = Array();
        $taskinfo["taskid"] = $idtask;
        /* Смотрим даты создания и закрытия задач */
        $query_created_completed_on = "SELECT created_on, completed_on, assigned_to_company_id FROM `og_project_tasks` WHERE id = ".$idtask;
        $created_completed_on = ssql($query_created_completed_on);
        /* Для каждого результата вычисляем дату создания и закрытия, чтобы определить задача просрочена или нет */
        /* Дата создания и закрытия задачи в timestamp */
        $createtask = datetime_to_timestamp($created_completed_on[1]["created_on"]);
        $completetask = datetime_to_timestamp($created_completed_on[1]["completed_on"]);
        if($completetask < $createtask) {
                $taskinfo["tasklive"] = $current_time - $createtask - get_holidays_in_period($createtask, $current_time);
        }
        else {
                $taskinfo["tasklive"] = $completetask - $createtask - get_holidays_in_period($createtask, $completetask);
/*              if($idtask == 376756) {
                        echo 'Задача открыта в '.$createtask.' Задача закрыта в '.$completetask.' Выходные дни '.get_holidays_in_period($createtask, $completetask).'<br />';
                }*/
//              echo $idtask.' '.get_holidays_in_period($createtask, $completetask).'<br />';
        }
        $taskinfo["companiesworktime"] = get_info_about_lukina_workers_time($idtask, $current_time, $createtask, $completetask, $created_completed_on[1]["assigned_to_company_id"]);
//echo '<pre>';
//var_dump($taskinfo);
        return $taskinfo;
}

function get_info_about_lukina_workers_time($idtask, $current_time, $createtask, $completetask, $assigned_to_company_id) {
        $infoworkers = Array();
        $result_time = Array();
        $query_time = "SELECT
                             oal.created_on, oal.created_by_id, ou.company_id, action
                       FROM
                            `og_application_logs` as oal, og_users as ou
                       WHERE
                            oal.rel_object_id = ".$idtask."
                       AND
                           ( oal.action =  'open' or  oal.action =  'edit' or oal.action =  'close'  )
                       AND
                            ou.id = oal.created_by_id
                    ";
        $res_time = ssql($query_time);
//      echo $createtask.'<br />'.$completetask.'<br />';
//      var_dump($res_time);
        if(isset($res_time)) {
//              echo '<br />--------------------------------------------------------------------------<br />';
//              echo 'Количество редактирований '.count($res_time).'<br />';
                for($i=1; $i<=count($res_time); $i++) {
                                $edittask = datetime_to_timestamp($res_time[$i]["created_on"]);
                                $idcomp = give_me_id_organization_user($res_time[$i]["created_by_id"]);
                                $inf["name"] = give_me_name_organization($idcomp);
//                              echo $res_time[$i]["created_on"].'<br />';
                        switch($res_time[$i]["action"]) {
                                case "edit":
                                if($i == 1) {
//                                      $index_company_id[$idcomp] += $edittask - $createtask;
                                        $inf["starttime"] = $createtask;
                                }
                                else {
//                                      $index_company_id[$idcomp] += $edittask - $edittask_prev;
                                        $inf["starttime"] = datetime_to_timestamp($res_time[$i-1]["created_on"]);
//                                      if ($res_time[$i]["action"] == "edit") echo "aloe <br/>";
//                                      var_dump($endexecutorcompany);
                                }
                                $inf["endtime"] = $edittask;
                                        if (!isset($res_time[$i+1]["action"]) )  {
                                        $infoworkers[] = $inf;
                                        $inf["starttime"] = $edittask;
                                        $inf["name"] = give_me_name_organization($assigned_to_company_id);
                                                if ($createtask > $completetask ) {
                                                $inf["endtime"] = $current_time;
//                                              echo $inf["name"];
                                                }
                                        }
                                $infoworkers[] = $inf;
//                              var_dump($idcomp);
                                break;
                                case "close":
                                if ($i == 1)    {
                                        $inf["starttime"] = $createtask;
                                }
                                else    {
                                        $inf["starttime"] = datetime_to_timestamp($res_time[$i-1]["created_on"]);
                                }
                                        if (isset($res_time[$i+1]["action"]) )  {
                                        $inf["endtime"] = $edittask;
                                        }
                                        else    {
                                        $inf["endtime"] = $completetask;
                                        }
                                $infoworkers[] = $inf;
                                break;
                                case "open":
                                        if (!isset($res_time[$i+1]["action"]) )  {
                                        $inf["name"] = give_me_name_organization($assigned_to_company_id);
                                        $inf["starttime"] = $edittask;
                                        $inf["endtime"] = $current_time;
                                        }
                                $infoworkers[] = $inf;
                                break;
                        }
//              var_dump($inf);
                }
// Если задача в итоге и не закрыта нужно добавить запись о текущей организации
//                                      var_dump($index_company_id);
//                                      echo '<br />--------------------------------------------------------------------------<br />';
                $companies_array = Array();
                foreach($infoworkers as $infoworker) {
                        if(empty($companies_array)) {
                                $companies_array[] = $infoworker["name"];
                        }
                        else {
                                if(!in_array($infoworker["name"], $companies_array)) {
                                        $companies_array[] = $infoworker["name"];
                                }
                        }
                }
//              var_dump($companies_array);
                $res = Array();
                foreach($companies_array as $company) {
                        $count_time_work = 0;
                        foreach($infoworkers as $infoworker) {
//                              echo 'Компания '.$infoworker["name"].' работала '.date('d/m/Y H:i:s', get_work_time_in_period($infoworker["starttime"], $infoworker["endtime"])).' <br />';
                                if($infoworker["name"] == $company) {
                                        $count_time_work += get_work_time_in_period($infoworker["starttime"], $infoworker["endtime"]);
//                                      echo 'Компания '.$company.' работала '.$count_time_work.'<br />';
                                }
                        }
                        $res["name"] = $company;
                        $res["time"] = $count_time_work;
//                              echo  'Компания '.$res["name"].' в итоге  проработала '.$res["time"].' секунд <br/>';
                        $result_time[] = $res;
                }
        }
        else {
//              echo $createtask,"   ",$completetask,"    ",$current_time;
                $inf["name"] = give_me_name_organization($assigned_to_company_id);
                if($completetask < $createtask) {
                        $inf["time"] = $current_time - $createtask - get_holidays_in_period($createtask, $current_time);
                }
                else {
                        $inf["time"] = $completetask - $createtask - get_holidays_in_period($createtask, $completetask);
                }
                $result_time[] = $inf;
//              var_dump($inf);
        }
        return $result_time;
}


function give_me_name_organization($idorganization) {
	$sql = "
                SELECT name
                FROM og_companies
                WHERE
                     id = ".$idorganization;
        $name_organization = ssql($sql);
	return $name_organization[1]["name"];
}

function give_me_id_organization_user($id_user){
$sql = "
        SELECT company_id
        FROM og_users
        WHERE
                id = ".$id_user."
        ";
$organization_id = ssql($sql);
return $organization_id[1]["company_id"];
}


function get_info_about_workers_time($idtask, $current_time, $createtask, $completetask, $assigned_to_company_id) {
	$infoworkers = Array();
	$result_time = Array();
	$query_time = "SELECT
                             oal.created_on, oal.created_by_id, ou.company_id
                       FROM
                            `og_application_logs` as oal, og_users as ou
                       WHERE
                            oal.rel_object_id = ".$idtask."
                       AND
                            oal.action =  'edit'
                       AND
                            ou.id = oal.created_by_id
                    ";
        $res_time = ssql($query_time);
        if(isset($res_time)) {
		foreach($res_time as $this_res_time) {
	                $different_companies[] = $this_res_time["company_id"];
                }

		$different_companies = array_unique($different_companies);
                foreach($different_companies as $item_different_companies) {
	                $index_company_id[$item_different_companies] = 0;
                }
		$index_company_id = Array();
//					echo '<br />--------------------------------------------------------------------------<br />';
//		echo 'Количество редактирований '.count($res_time).'<br />';
		for($i=1; $i<=count($res_time); $i++) {
			if(!isset($res_time[$i+1]["company_id"])) {
				$sql = "
                                        SELECT assigned_to_company_id
                                        FROM
                                               `og_project_tasks`
                                        WHERE
                                               `id` = ".$idtask."
                                        AND
                                               `assigned_by_id` = ".$res_time[$i]['created_by_id'];
				$endexecutorcompany = ssql($sql);
				$endexecutorcompany = $endexecutorcompany[1]["assigned_to_company_id"];
//				var_dump($endexecutorcompany);
				$edittask = datetime_to_timestamp($res_time[$i]["created_on"]);
				$idcomp = $res_time[$i]["company_id"];
				$inf["name"] = give_me_name_organization($idcomp);

				if(count($res_time)>1) {
					$edittask_prev = datetime_to_timestamp($res_time[$i-1]["created_on"]);
//					$index_company_id[$idcomp] += $edittask - $edittask_prev;
						$inf["starttime"] = $edittask_prev;
						$inf["endtime"] = $edittask;

/*		if($idtask == 376756) {
			echo '<pre>';
//			var_dump($infoworkers);
			echo 'Задача открыта '.date("d-m-Y H:i:s", $createtask).', а закрыта'.date("d-m-Y H:i:s", $completetask).'<br />';
		}*/

				}
				else {
//					$index_company_id[$endexecutorcompany] += $current_time - $edittask;
//					$index_company_id[$idcomp] += $edittask - $createtask;

						$inf["starttime"] = $createtask;
						$inf["endtime"] = $edittask;

				}
				$infoworkers[] = $inf;
				$inf["name"] = give_me_name_organization($assigned_to_company_id);
				$inf["starttime"] = $edittask;
				if($createtask < $completetask) {
					$inf["endtime"] = $completetask;
				}
				else {
	                                $inf["endtime"] = $current_time;
				}
				$infoworkers[] = $inf;
			}
			else {
				$edittask = datetime_to_timestamp($res_time[$i]["created_on"]);
				$idcomp = $res_time[$i]["company_id"];
				$inf["name"] = give_me_name_organization($idcomp);

				if($i == 1) {
//					$index_company_id[$idcomp] += $edittask - $createtask;
					$inf["starttime"] = $createtask;
					$inf["endtime"] = $edittask;
				}
				else {
					$edittask_prev = datetime_to_timestamp($res_time[$i-1]["created_on"]);
					$idcomp = $res_time[$i]["company_id"];
					$inf["name"] = give_me_name_organization($idcomp);
//					$index_company_id[$idcomp] += $edittask - $edittask_prev;
					$inf["starttime"] = $edittask_prev;
					$inf["endtime"] = $edittask;
				}
				$infoworkers[] = $inf;
			}
		}
//		var_dump($index_company_id);
//					echo '<br />--------------------------------------------------------------------------<br />';

		$companies_array = Array();
		foreach($infoworkers as $infoworker) {
			if(empty($companies_array)) {
				$companies_array[] = $infoworker["name"];
			}
			else {
				if(!in_array($infoworker["name"], $companies_array)) {
					$companies_array[] = $infoworker["name"];
				}
			}
		}
//		var_dump($companies_array);
		$res = Array();
		foreach($companies_array as $company) {
			$count_time_work = 0;
			foreach($infoworkers as $infoworker) {
//				echo 'Компания '.$infoworker["name"].' работала '.get_work_time_in_period($infoworker["starttime"], $infoworker["endtime"]).' секунд<br />';
				if($infoworker["name"] == $company) {
					$count_time_work += get_work_time_in_period($infoworker["starttime"], $infoworker["endtime"]);
				}
			}
			$res["name"] = $company;
			$res["time"] = $count_time_work;
			$result_time[] = $res;
		}
	}
	else {
		$inf["name"] = give_me_name_organization($assigned_to_company_id);
		if($completetask < $createtask) {
                	$inf["time"] = $current_time - $createtask - get_holidays_in_period($createtask, $current_time);
	        }
        	else {
                	$inf["time"] = $completetask - $createtask - get_holidays_in_period($createtask, $completetask);
	        }
		$result_time[] = $inf;
	}
	return $result_time;
}

function count_overdue_tasks($tasks, $isopen) {
//     var_dump($tasks);
     $count_overdue_tasks = 0;
     $result_overdue = Array();
     $result_overdue_all = Array();
     $index_company_id = Array();
//     foreach($open_count_tasks as $tasks) {
//	foreach($tasks["tasks"] as $one_task) {
	/* Проходимся по задачам */
	foreach($tasks as $one_task) {
	    /* Смотрим даты создания и закрытия задач */
	    $query_created_completed_on = "SELECT created_on, completed_on FROM `og_project_tasks` WHERE id = ".$one_task;
	    $created_completed_on = ssql($query_created_completed_on);
	    /* Для каждого результата вычисляем дату создания и закрытия, чтобы определить задача просрочена или нет */
	    foreach($created_completed_on as $item_created_completed_on) {
		/* Дата создания в timestamp */
               	$created_on = $created_completed_on[1]["created_on"];
                $completed_on = $created_completed_on[1]["completed_on"];
                $datetime = explode(' ', $created_on);
                $date = explode('-', $datetime[0]);
                $time = explode(':', $datetime[1]);
                $createtask = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
		/* Какой день недели был, когда задача создавалась. Необходимо, чтобы знать на сколько продливать "непросрочки" задачи */
                $day_of_week = date("w", $createtask);

                switch($day_of_week) {
               	     case "0": //Sunday
                          $plus = 432000;
                     break;
		     case "3": // Среда
			  $plus = 518400;
                     break;
                     case "4": // Четверг
                          $plus = 518400;
                     break;
                     case "5": //Friday
                          $plus = 518400;
                     break;
                     case "6": //Saturday
                          $plus = 518400;
                     break;
                     default: // Понедельник и вторник
                          $plus = 345600;
                     break;
                }

		/* Время, к которому задача должна быть закрыта */
		$needtoclose = $createtask + $plus;
		/* Вычисление даты закрытия задачи в timestamp */
		$datetime = explode(' ', $completed_on);
                $date = explode('-', $datetime[0]);
                $time = explode(':', $datetime[1]);
                $closetask = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);

		if($isopen) { // Если задача открыта, то выполняем ниже
			if(time() > $needtoclose) { // Проверка: задача просрочена или нет?
			     $count_overdue_tasks++;
//			     echo 'Задача №'.$one_task.' просрочена<br />';
				/* Выбираем все изменения по задаче */
				$query_time = "SELECT
                                                                oal.created_on, oal.created_by_id, ou.company_id
                                                        FROM
                                                                `og_application_logs` as oal, og_users as ou
                                                        WHERE
                                                                oal.rel_object_id = ".$one_task."
                                                        AND
                                                                oal.action =  'edit'
                                                        AND
                                                                ou.id = oal.created_by_id
                                                ";
				$res_time = ssql($query_time);
				if(isset($res_time)) {
//				     var_dump($res_time);
				     /* Смотрим все компании, которые изменяли задачу */
				     foreach($res_time as $this_res_time) {
                                          $different_companies[] = $this_res_time["company_id"];
                                     }
				     /* Убираем все повторяющиеся компании */
				     $different_companies = array_unique($different_companies);

				     /* Ставим id компании в виде индексов и присваиваем значение = 0  */
                                     foreach($different_companies as $item_different_companies) {
                                          $index_company_id[$item_different_companies] = 0;
                                     }

				     // Смотрим все записи, которые были редактированы
				     for($i=1; $i<=count($res_time); $i++) {
//					var_dump($res_time[$i+1]["company_id"]);
//					echo '<br />';
//				          if($res_time[$i+1]["company_id"]== NULL) { // Если следующее редактирование не существует
					  if(!isset($res_time[$i+1]["company_id"])) {
					     /* Берем инфу, кому сейчас присвоена задача, зная кем была присвоена задача */
					     $sql = "
                                                      SELECT assigned_to_company_id
                                                      FROM
                                                           `og_project_tasks`
                                                      WHERE
                                                           `id` = ".$one_task."
                                                      AND
                                                           `assigned_by_id` = ".$res_time[$i]['created_by_id']."
                                                     ";
   					     $endexecutorcompany = ssql($sql);
					     $endexecutorcompany = $endexecutorcompany[1]["assigned_to_company_id"];
					     /* Конвертируем время редактирования в timestamp*/
					     $edit_task_on = $res_time[$i]["created_on"];
					     $datetime_edit_task = explode(' ', $edit_task_on);
					     $date_edit_task = explode('-', $datetime_edit_task[0]);
					     $time_edit_task = explode(':', $datetime_edit_task[1]);
					     $edittask = mktime($time_edit_task[0]+4, $time_edit_task[1], $time_edit_task[2], $date_edit_task[1], $date_edit_task[2], $date_edit_task[0]);

					     $idcomp = $res_time[$i]["company_id"]; // Отдельно выводим id компании
					     $ninesh_vremya = time(); // Настоящее время в timestamp
                                             $count_edits = count($res_time); // Количество редактирований задачи
					     if($count_edits>1) { // Если задача редактировалась >1 раза, то высчитываем в timestamp предыдущую дату и время редактирования задачи
					          $edit_task_on_prev = $res_time[$i-1]["created_on"];
                                                  $datetime_edit_task_prev = explode(' ', $edit_task_on_prev);
                                                  $date_edit_task_prev = explode('-', $datetime_edit_task_prev[0]);
                                                  $time_edit_task_prev = explode(':', $datetime_edit_task_prev[1]);
					          $edittask_prev = mktime($time_edit_task_prev[0]+4, $time_edit_task_prev[1], $time_edit_task_prev[2], $date_edit_task_prev[1], $date_edit_task_prev[2], $date_edit_task_prev[0]); // +4 т.к. фенге время
						  /* Вычисляем сколько времени от одного редактирования до предыдущего */
					          $index_company_id[$idcomp] += $edittask - $edittask_prev;
						  /* Вычисляем время обработки задачи того, на ком сейчас висит задача */
					          $index_company_id[$endexecutorcompany] += $ninesh_vremya - $edittask;
					     }
                                             else {
                                                  $index_company_id[$endexecutorcompany] += $ninesh_vremya - $edittask;
                                                  $index_company_id[$idcomp] += $edittask - $createtask;
//                                                  $index_company_id[$idcomp] += $edittask - $needtoclose; // Берем не от начала создания задачи, а от времени ее закрытия
                                             }
				          }
                                          else { // Если следующее редактирование существует

					     /* Конвертируем время редактирования в timestamp*/
                                     	     $edit_task_on = $res_time[$i]["created_on"];
					     $datetime_edit_task = explode(' ', $edit_task_on);
				             $date_edit_task = explode('-', $datetime_edit_task[0]);
					     $time_edit_task = explode(':', $datetime_edit_task[1]);
					     $edittask = mktime($time_edit_task[0]+4, $time_edit_task[1], $time_edit_task[2], $date_edit_task[1], $date_edit_task[2], $date_edit_task[0]); // +4 т.к. фенге время по Гринвичу
					     $idcomp = $res_time[$i]["company_id"];
					     /* Если это первое редактирование, то вычисляем не с начала создания задачи, а с момента, когда задача должна быть закрыта*/
					     if($i == 1) {
					          $index_company_id[$idcomp] += $edittask - $createtask;
//					          $index_company_id[$idcomp] += $edittask - $needtoclose;
					     }
					     else { // Если редактирование не первое
					          $edit_task_on_prev = $res_time[$i-1]["created_on"];
					          $datetime_edit_task_prev = explode(' ', $edit_task_on_prev);
					          $date_edit_task_prev = explode('-', $datetime_edit_task_prev[0]);
					          $time_edit_task_prev = explode(':', $datetime_edit_task_prev[1]);
					          $edittask_prev = mktime($time_edit_task_prev[0]+4, $time_edit_task_prev[1], $time_edit_task_prev[2], $date_edit_task_prev[1], $date_edit_task_prev[2], $date_edit_task_prev[0]); // +4 т.к. фенге время
					          $idcomp = $res_time[$i]["company_id"];
					          $index_company_id[$idcomp] += $edittask - $edittask_prev;
					     }
				          }
//					  $index_company_id["task"] = $one_task;
//					  var_dump($index_company_id);
					  foreach($index_company_id as $k => $v)   array_key_exists($k,$result_overdue) ? $result_overdue[$k] += $v : $result_overdue[$k] = $v;
//					  var_dump($result_overdue);
				     }
			        }
				else {
                                             $sql = "
                                                      SELECT created_on, assigned_to_company_id
                                                      FROM
                                                           `og_project_tasks`
                                                      WHERE
                                                           `id` = ".$one_task;
                                             $res_time = ssql($sql);
//					     var_dump($res_time);
                                             $endexecutorcompany = $res_time[1]["assigned_to_company_id"];
                                             /* Конвертируем время редактирования в timestamp*/
                                             $edit_task_on = $res_time[1]["created_on"];
                                             $datetime_edit_task = explode(' ', $edit_task_on);
                                             $date_edit_task = explode('-', $datetime_edit_task[0]);
                                             $time_edit_task = explode(':', $datetime_edit_task[1]);
                                             $edittask = mktime($time_edit_task[0]+4, $time_edit_task[1], $time_edit_task[2], $date_edit_task[1], $date_edit_task[2], $date_edit_task[0]);
//                                             $index_company_id[$endexecutorcompany] = $edittask - $createtask;
                                             $result_overdue[$endexecutorcompany] = time() - $needtoclose;
	    				     echo 'Здесь изменений не было '.$one_task.'<br />';
				}
				foreach($result_overdue as $k => $v)   array_key_exists($k,$result_overdue_all) ? $result_overdue_all[$k] += $v : $result_overdue_all[$k] = $v;
//				var_dump($result_overdue);
				$result_overdue = Array();
			}
			else {
//			     echo 'Задача №'.$one_task.' не просрочена. Создана: '.date("d-m-Y H:i:s", $createtask).' Должна быть закрыта: '.date("d-m-Y H:i:s", $needtoclose).'<br />';
			}
		}
		else {
                     if($closetask > $needtoclose) {
//		          echo 'Задача №'.$one_task.' просрочена<br />';
		     }
//		     else echo 'Задача №'.$one_task.' не просрочена. Создана: '.date("d-m-Y H:i:s", $createtask).' Должна быть закрыта: '.date("d-m-Y H:i:s", $needtoclose).' Закрыта: '.date("d-m-Y H:i:s", $closetask).'<br />';
		}
	    }
	}
//     }
//     $index_company_id["count_overdue_tasks"] = $count_overdue_tasks;
     return $result_overdue_all;
}

function give_me_report_terminal($starttime, $endtime, $project_task_id) {
	$tasks_close = project_task_count($project_task_id,$starttime,$endtime,"close","yes");
	if ($tasks_close){
		foreach($tasks_close as $item){
        		$array_tasks_close[] = $item["task_id"];
		}
	}

	$tasks_open = project_task_count($project_task_id,$starttime,$endtime,"open","yes");
	if ($tasks_open) {
		foreach($tasks_open as $item){
        		$array_tasks_open[] = $item["task_id"];
		}
	}
	else
	{	$array_tasks_open = array(); }
	
	if (!$tasks_open && !$tasks_close) return 0;
	
	$tasks_open = array_unique($array_tasks_open);
        $tasks_close = array_unique($array_tasks_close);
	// Пропускаем задачи через фильтр, отбираем только те, которые относятся к Инфоматам и терминалам ЭО
	$open_tasks_from_filter = number_tasks_from_filter($tasks_open);
	$close_tasks_from_filter = number_tasks_from_filter($tasks_close);
//	echo '<pre>';
	$open_count_tasks = count_tasks($open_tasks_from_filter);
	$close_count_tasks = count_tasks($close_tasks_from_filter);
	$current_time = time();

//	var_dump($open_count_tasks);
//	 var_dump($close_count_tasks);

	$info_about_all_tasks = Array();
	foreach($close_count_tasks as $info) {
		$close_tasks_info = Array();
		if($info["count"]>0) {
			$close_tasks_info["class_error_name"] = $info["class_error_name"];
//			$close_tasks_info["type_problem_name"] = $info["type_problem_name"];
			$close_tasks_info["close"] = $info["count"];
			$close_tasks_info["open"] = 0;
			$close_tasks_info["overdue_count"] = 0;
			$overdue_tasks = Array();
//			$overdue_companies = Array();
			foreach($info["tasks"] as $onetask) {
				$taskinfo = get_info_about_task($onetask, $current_time);
				if($taskinfo["tasklive"]> 345600) {
					$close_tasks_info["overdue_count"]++;
					$overdue_tasks[] = $onetask;
				}
				else {
					foreach($taskinfo["companiesworktime"] as $companies_time) {
         		                	if($companies_time["time"] > 172800) {
			                        	$overdue_tasks[] = $onetask;
//							$empty["name"] = $companies_time["name"];
//							$empty["time"] = $companies_time["time"]-86400;
//							$overdue_companies[] = $empty;
	        	                	}
        	        		}
				}
			}
			$close_tasks_info["overdue_tasks"] = array_unique($overdue_tasks);
			$close_tasks_info["overdue_count"] = count($close_tasks_info["overdue_tasks"]);

//			$close_tasks_info["overdue_companies"] = $overdue_companies;
		}
		else {
                	$close_tasks_info["class_error_name"] = $info["class_error_name"];
//	                $close_tasks_info["type_problem_name"] = $info["type_problem_name"];
			$close_tasks_info["open"] = 0;
			$close_tasks_info["close"] = 0;
			$close_tasks_info["overdue_count"] = 0;
			$close_tasks_info["overdue_tasks"] = Array();
		}
		$info_about_all_tasks[] = $close_tasks_info;
	}

//var_dump($info_about_all_tasks);

	for($i=0; $i<count($info_about_all_tasks); $i++) {
		foreach($open_count_tasks as $info) {
			if($info["class_error_name"] == $info_about_all_tasks[$i]["class_error_name"]) {
	        		if($info["count"]>0) {
					$info_about_all_tasks[$i]["open"] = $info["count"];
	        			foreach($info["tasks"] as $onetask) {
		                		$taskinfo = get_info_about_task($onetask, $current_time);
	        		        	if($taskinfo["tasklive"]> 345600) {
							$info_about_all_tasks[$i]["overdue_count"]++;
							$info_about_all_tasks[$i]["overdue_tasks"][] = $onetask;
	        	        		}
						else {
							foreach($taskinfo["companiesworktime"] as $companies_time) {
								if($companies_time["time"] > 172800) {
									$info_about_all_tasks[$i]["overdue_count"]++;
									$info_about_all_tasks[$i]["overdue_tasks"][] = $onetask;
//		                                                        $empty["name"] = $companies_time["name"];
  //              		                                        $empty["time"] = $companies_time["time"]-86400;
    //                            		                        $info_about_all_tasks[$i]["overdue_companies"][] = $empty;
								}
							}
						}
		        		}
        			}
			}
		}
	}
//	var_dump($info_about_all_tasks);

	return $info_about_all_tasks;
}

function give_me_overdue_time_from_all_info($info, $current_time) {
//	var_dump($info);
	$result = Array();
	$count_overdue_all = Array();
	$result["open"] = 0;
	$result["close"] = 0;
	$result["overdue"] = 0;
	if (!$info) return null;
	foreach($info as $inf) {
		if(is_array($inf["overdue_tasks"])) {
			foreach($inf["overdue_tasks"] as $item_task) {
				$timework = get_info_about_task($item_task, $current_time);
//				echo '<pre>';
//				echo $item_task.'<br />';
//				var_dump($timework);
				 foreach($timework["companiesworktime"] as $item_worktime) {
					if($item_worktime["time"] > 172800) {
						if(empty($count_overdue_all)) {
                        	                        $variable["name"] = $item_worktime["name"];
                                	                $variable["time"] = $item_worktime["time"] - 172800;
                                        	        $count_overdue_all[] = $variable;
                                        	}
	                                        else {
        	                                        $flag_new = true;
                	                                for($i=0; $i<count($count_overdue_all); $i++) {
                        	                                if($item_worktime["name"] == $count_overdue_all[$i]["name"]) {
                                	                                $count_overdue_all[$i]["time"] += $item_worktime["time"] - 172800;
//                                	                                $count_overdue_all[$i]["time"] += $item_worktime["time"];
                                        	                        $flag_new = false;
                                                	        }
	                                                }
        	                                        if($flag_new) {
                	                                        $variable["name"] =  $item_worktime["name"];
                        	                                $variable["time"] = $item_worktime["time"] - 172800;
                                	                        $count_overdue_all[] = $variable;
                                        	        }
	                                        }
					}
				}
			}
		}
		$result["open"] += $inf["open"];
		$result["close"] += $inf["close"];
		$result["overdue"] += $inf["overdue_count"];
	}
	$result["information"] = $count_overdue_all;

	return $result;
}
?>
