<?php
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
require_once 'phpexcel/Classes/PHPExcel.php';
//$verstka = '';
$projectid = '4054';
//$starttime = date("d.m.Y");
$starttime = "01.03.2014";
//$endtime = date("d.m.Y");
$endtime = "26.06.2014";
//echo '<pre>';
function project_child($ProjectID) {
  logger("test","error",__FUNCTION__);
    $temp = 0;
    for ($n=1;$n<10;$n++) {
        if ($n==9)     {
            $sql="SELECT id,name from `og_projects` where p9 = $ProjectID and p10 > 0";
            $result = ssql($sql);
            if(is_array($result)) {
                foreach($result as $sk => $sv)  {
                    $mas_child[$temp] = $sv;
                    $temp++;
                }
            }
        }  else  {
            $sql = "SELECT id,name from `og_projects` where p".$n." = $ProjectID and p".($n+1)." > 0 and p".($n+2)." = 0";
            $result = ssql($sql);
            if(is_array($result)) {
                foreach($result as $sk => $sv) {
                    $mas_child[$temp] = $sv;
                    $temp++;
                }
            }
        }
    }
    return $mas_child;
}

function rd($Date) {
$arr =explode(".",$Date);
$str = $arr[2]."-".$arr[1]."-".$arr[0];
return $str;
}

function project_task_count($ProjectID,$StartTime="",$EndTime="",$Type="all",$Total=false) {
  if($StartTime) {
    $sql_start = "AND start_date >= '".mes(rd($StartTime))." 00:00:00' ";
 }
  if($EndTime) {
    $sql_end = "AND start_date <= '".mes(rd($EndTime))." 23:59:59' ";
  }
  switch($Type) {
  case "open":
    $type_sql = "AND completed_on = '0000-00-00 00:00:00' AND completed_by_id = '0' ";
    break;
  case "close":
    $type_sql = "AND completed_on != '0000-00-00 00:00:00' AND completed_by_id != '0' ";
    break;
  default:
    break;
  }
  if($Total=="yes") {
    $result = "task_id";
  } else {
    $result = "COUNT(*) AS COUNT";
  }
  $sql = "
                                  SELECT $result
                                  FROM a_statistics_projects_and_tasks
                                  WHERE workspace_id = '".mes($ProjectID)."'
                                  AND archived_by_id ='0'
                                  AND trashed_by_id = '0'
                                  $type_sql
                                  $sql_start
                                  $sql_end
                                  ";
  $result = ssql($sql);
  if($Total=="yes") {
    //    echo $sql;
    return $result;
  } else {
    return $result[1]["COUNT"];
  }
}


function project_task_count2($ProjectID,$StartTime="",$EndTime="") {
  if($StartTime) {
    $sql_start = "AND start_date >= '".mes(rd($StartTime))." 00:00:00' ";
  }
  if($EndTime) {
    $sql_end = "AND start_date <= '".mes(rd($EndTime))." 23:59:59' ";
  }
  $sql = "
                                  SELECT COUNT(*) AS COUNT FROM og_project_tasks WHERE id in (
                                  SELECT object_id FROM og_workspace_objects WHERE workspace_id = '$ProjectID'  AND  object_manager = 'ProjectTasks'

                                  ) AND archived_by_id ='0' AND trashed_by_id = '0'         $sql_start        $sql_end

                                  ";
  $result = ssql($sql);
  return $result[1]["COUNT"];
}

global $i,$total,$total_open,$total_close,$tasks;

function tasks_per_project($ProjectID,$level='1',$StartTime="",$EndTime="") {
  global $i, $total,$total_open,$total_close,$tasks;
  $projects = project_child($ProjectID);
  if(is_array($projects)) {
    $level++;
    foreach($projects as $v) {
      $i++;
      if($i%2 ==1) {
        $htmlclass="class='row1'";
      } else {
        $htmlclass="class='row2'";
      }
      $html .= "\n<tr $htmlclass>";
      $html .= "\n\t<td align=>
                 <span title='".$v["id"]."'>
                  <div class='level".$level."'>
                   ".$v["name"]."
                  </div>
                </td>";

      $count = project_task_count($v["id"],$StartTime,$EndTime);
      $total = $total + $count;
      $html .= "\n\t<td align='center'>".$count."</td>";

      $count_close = project_task_count($v["id"],$StartTime,$EndTime,"close");
      $tasks_close = project_task_count($v["id"],$StartTime,$EndTime,"close","yes");
      $total_close = $total_close + $count_close;
      if(is_array($tasks_close)) {
        foreach($tasks_close as $sk=>$sv) {
          $tasks["close"][$sv["task_id"]] = 1;
        }
      }
      $html .= "\n\t<td align='center'>".$count_close."</td>";

      $count_open = project_task_count($v["id"],$StartTime,$EndTime,"open");
      $tasks_open = project_task_count($v["id"],$StartTime,$EndTime,"open","yes");
      $total_open = $total_open + $count_open;
      if(is_array($tasks_open)) {
        foreach($tasks_open as $sk=>$sv) {
          $tasks["open"][$sv["task_id"]] = 1;
        }
      }

      $html .= "\n\t<td align='center'>".$count_open."</td>";

      $html .= "</tr>";
      $html .= tasks_per_project($v["id"],$level,$StartTime,$EndTime);

    }
  }
  return $html;
}

if($starttime==$endtime && $starttime && $endtime) {
   $period = "на ".$starttime;
} elseif($starttime && $endtime) {
   $period = "с ".$starttime." по ".$endtime;
} elseif(!$starttime && !$endtime) {
    $period = "за все время работы";
}

switch($projectid) {
case "684":
  $line = "МИС РТ";
  break;
case "495":
  $line = "ПГМУ (Старые)";
  break;
case "2026":
  $line = "ПГМУ (Новые)";
  break;
case "1103":
  $line = "Э-Образование";
  break;
case "745":
  $line = "ДОУ";
  break;
case "916":
  $line = "Инциденты";
  break;
case "2553":
  $line = "Инциденты (Новый классификатор)";
  break;
case "1211":
  $line = "Обращения КЦ";
  break;
case "345":
  $line = "Э-Образование";
  break;
case "4054":
  $line = "Просроченные задачи";
  break;
default:
  $line = "ЕГИС ЗРТ";
  break;
}

$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$active_sheet = $objPHPExcel->getActiveSheet();

//Ориентация страницы и  размер листа
$active_sheet->getPageSetup()
		->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$active_sheet->getPageSetup()
			->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
//Поля документа
$active_sheet->getPageMargins()->setTop(1);
$active_sheet->getPageMargins()->setRight(0.75);
$active_sheet->getPageMargins()->setLeft(0.75);
$active_sheet->getPageMargins()->setBottom(1);
//Название листа
$active_sheet->setTitle($line);
//Шапа и футер
/*$active_sheet->getHeaderFooter()->setOddHeader("&CШапка нашего прайс-листа");
$active_sheet->getHeaderFooter()->setOddFooter('&L&B'.$active_sheet->getTitle().'&RСтраница &P из &N');*/
//Настройки шрифта
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);


//$projectid = "4054";
//if($projectid) {
	echo 'Это проверка даты'.$endtime;
	$tasks_per_project =  tasks_per_project($projectid,1,$starttime,$endtime);

		$active_sheet->getStyle('A1')->getFont()->setBold(true);
		$active_sheet->getStyle('B1')->getFont()->setBold(true);
		$active_sheet->getStyle('C1')->getFont()->setBold(true);
                $active_sheet->getStyle('D1')->getFont()->setBold(true);
		$active_sheet->getStyle('E1')->getFont()->setBold(true);
                $active_sheet->getStyle('F1')->getFont()->setBold(true);
		$active_sheet->getStyle('G1')->getFont()->setBold(true);
                $active_sheet->getStyle('H1')->getFont()->setBold(true);
		$active_sheet->getStyle('I1')->getFont()->setBold(true);


		$active_sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $active_sheet->getColumnDimension('A')->setWidth(5);
                $active_sheet->getColumnDimension('B')->setWidth(10);
                $active_sheet->getColumnDimension('C')->setWidth(15);
		$active_sheet->getColumnDimension('D')->setWidth(50);
                $active_sheet->getColumnDimension('E')->setWidth(80);
                $active_sheet->getColumnDimension('F')->setWidth(20);
                $active_sheet->getColumnDimension('G')->setWidth(50);
                $active_sheet->getColumnDimension('H')->setWidth(50);
                $active_sheet->getColumnDimension('I')->setWidth(50);

                $active_sheet->setCellValue('A1', '№');
                $active_sheet->setCellValue('B1', 'Дата');
		$active_sheet->setCellValue('C1', 'Дата заявки');
		$active_sheet->setCellValue('D1', 'ЛПУ');
                $active_sheet->setCellValue('E1', 'Информация о терминале');
                $active_sheet->setCellValue('F1', 'Номер в СУП');
                $active_sheet->setCellValue('G1', 'Кто нарушил');
		$active_sheet->setCellValue('H1', 'Причина');
		$active_sheet->setCellValue('I1', 'ИТОГО');
//		$verstka="<table border='1' cellspacing='0' cellpadding='0'><tr><td style='width: 5px;'>№</td><td style='width: 10px;'>Дата</td><td style='width: 15px;'>Дата заявки</td><td style='width: 50px;'>ЛПУ</td><td style='width: 80px;'>Информация о терминале</td><td style='width: 20px;'>Номер в СУП</td><td style='width: 10px;'>Кто нарушил</td><td style='width: 50px;'>Причина</td><td style='width: 10px;'>ИТОГО</td></tr>";
		$verstka = '<table border="1"><tr><td>№</td><td>Дата</td><td>Дата заявки</td><td>ЛПУ</td><td>Информация о терминале</td><td>Номер в СУП</td><td>Кто нарушил</td><td>Причина</td><td>ИТОГО</td></tr>';

	//var_dump($tasks["open"]);
//	$openclose = 0;
//	while($openclose<2) {
//		if($openclose==0) {
		        $massiv = $tasks["open"];
//			$massiv = $tasks["close"];
/*		        $status = "Открыта";
		}
		if($openclose==1) {
		        $massiv = $tasks["close"];
		        $status = "Закрыта";
		}*/
		foreach($massiv as $key => $value) {
                        $item = $key;
                        $query4 = "
                                        SELECT
                                                *
                                        FROM
                                                `og_object_properties`
                                        WHERE
                                                rel_object_id = ".$item."
                                        AND
                                                rel_object_manager = 'ProjectTasks'
                        ";
                        $lpu = '';
                        $terminal = '';
			$terminalhtml = '';
                        $klassificator = '';
                        $res4 = ssql($query4); //Смотрим доп. поля данной задачи
                        foreach($res4 as $item4) {
                                switch($item4["name"]) {
                                        case "ЛПУ":
                                                $lpu = $item4["value"];
                                        break;
					case "ЛПУ:":
                                                $lpu = $item4["value"];
                                        break;
					case "Терминал1":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал2":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал3":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал4":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал5":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал6":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал7":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал8":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал9":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал10":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал11":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал12":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал13":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал14":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Терминал15":
                                                $terminal .= $item4["value"].PHP_EOL.PHP_EOL;
						$terminalhtml .= $item4["value"].'<br /><br />';
                                        break;
					case "Классификатор":
                                                $klassificator = $item4["value"];
                                        break;
                                }
                        }
// Добавил к поиску поле assigned_to_user_id
                        $query3 = "SELECT
                                    created_on, text
                           FROM
                                    `og_project_tasks`
                           WHERE
                                    id = ".$item."
                        ";
                        $res3 = ssql($query3); // Запрашиваем данные
                        foreach($res3 as $item3) {
				if($klassificator == 'Терминалы ЭО и инфоматы') {
	       	                        $created_on = $item3["created_on"];
					$datetime = explode(' ', $created_on);
					$date = explode('-', $datetime[0]);
					$time = explode(':', $datetime[1]);
					$createtask = mktime($time[0]+4, $time[1], $time[2], $date[1], $date[2], $date[0]); // +4 т.к. фенге время по Гринвичу
					$day_of_week = date("w", $createtask);

					switch($day_of_week) {
						case "0": //Sunday
							$plus = 216000;
						break;
						case "5": //Friday
							$plus = 345600;
						break;
						case "6": //Saturday
							$plus = 259200;
						break;
						default:
							$plus = 172800;
						break;
					}
					$needtoclose = $createtask + $plus;
					if(mktime(8, 0, 0, 6, 26, 2014)>$needtoclose) {
//						var_dump($index_company_id);
//						$index_company_id = Array();

						// Смотрим какая компания сколько обрабатывала заявку
						$query_time = "SELECT
								oal.created_on, oal.created_by_id, ou.company_id
							FROM
								`og_application_logs` as oal, og_users as ou
							WHERE
								oal.rel_object_id = ".$item."
							AND
								oal.action =  'edit'
							AND
								ou.id = oal.created_by_id
						";
						$res_time = ssql($query_time);
						//var_dump($res_time);
						foreach($res_time as $this_res_time) {
							$different_companies[] = $this_res_time["company_id"];
						}
						$different_companies = array_unique($different_companies);
						foreach($different_companies as $item_different_companies) {
							$index_company_id[$item_different_companies] = 0;
						}
//						var_dump($res_time);
						for($i=1; $i<=count($res_time); $i++) {
//							var_dump($res_time[$i]);
							if($res_time[$i+1]["company_id"]== NULL) {
								$sql = "
									SELECT assigned_to_company_id
									FROM
										`og_project_tasks`
									WHERE
										`id` = ".$item."
									AND
										`assigned_by_id` = ".$res_time[$i]['created_by_id']."
								";
								//echo '<br />'.$item.'<br />index - '.$i.'<br />';
								$endexecutorcompany = ssql($sql);
								$endexecutorcompany = $endexecutorcompany[1]["assigned_to_company_id"];

								$edit_task_on = $res_time[$i]["created_on"];

								$datetime_edit_task = explode(' ', $edit_task_on);
								$date_edit_task = explode('-', $datetime_edit_task[0]);
								$time_edit_task = explode(':', $datetime_edit_task[1]);
								$edittask = mktime($time_edit_task[0]+4, $time_edit_task[1], $time_edit_task[2], $date_edit_task[1], $date_edit_task[2], $date_edit_task[0]);
								$idcomp = $res_time[$i]["company_id"];
//								echo 'Время редактирования последнее = '.$edit_task_on.'<br />';
								//echo 'Время редактирования последнее1 = '.date("Y-m-d H:i:s",$edittask).'<br />';
								//echo 'Время создания задачи = '.date("Y-m-d H:i:s",$createtask).'<br />';

								$ninesh_vremya = mktime(8, 0, 0, 6, 26, 2014);
								$count_edits = count($res_time);
								//var_dump($count_edits);
								if($count_edits>1) {
									//$idcomp = $endexecutorcompany;
									$edit_task_on_prev = $res_time[$i-1]["created_on"];
									$datetime_edit_task_prev = explode(' ', $edit_task_on_prev);
                                                                        $date_edit_task_prev = explode('-', $datetime_edit_task_prev[0]);
                                                                        $time_edit_task_prev = explode(':', $datetime_edit_task_prev[1]);
                                                                        $edittask_prev = mktime($time_edit_task_prev[0]+4, $time_edit_task_prev[1], $time_edit_task_prev[2], $date_edit_task_prev[1], $date_edit_task_prev[2], $date_edit_task_prev[0]); // +4 т.к. фенге время$

									$index_company_id[$idcomp] += $edittask - $edittask_prev;
echo 'Нынешнее время '.date("d.m.Y", $ninesh_vremya).'<br />';
									$index_company_id[$endexecutorcompany] += $ninesh_vremya - $edittask;
//									echo 'Компания = '.$idcomp.'<br /><br />';
//									if($idcomp==2) {
//										echo 'Тут был УИП <br />';
  //                                                                      	echo $ninesh_vremya.' - '.$edittask.' = '.($ninesh_vremya - $edittask).'<br /><br />';
//									}
								}
								else {
//									echo 'Сработала ветка 1';
									$index_company_id[$endexecutorcompany] += $ninesh_vremya - $edittask;

									$index_company_id[$idcomp] += $edittask - $createtask;
								}
							}
							else {
								$edit_task_on = $res_time[$i]["created_on"];
		                	                        $datetime_edit_task = explode(' ', $edit_task_on);
               			        	                $date_edit_task = explode('-', $datetime_edit_task[0]);
                               				        $time_edit_task = explode(':', $datetime_edit_task[1]);
	                        	                	$edittask = mktime($time_edit_task[0]+4, $time_edit_task[1], $time_edit_task[2], $date_edit_task[1], $date_edit_task[2], $date_edit_task[0]); // +4 т.к. фенге время по Гринвичу
								$idcomp = $res_time[$i]["company_id"];
									//var_dump($idcomp);
									//echo '<br />'.$edittask.' - '.$createtask.' = '.($edittask - $createtask).'  <br />';

								if($i == 1) {
//									echo 'index - '.$i.'<br />';
									$index_company_id[$idcomp] += $edittask - $createtask;
//									echo 'Время первого редактирования - начало создания задачи = '.$edittask.' - '.$createtask.' = '.($edittask - $createtask).'<br />';
								}
								else {
//									echo 'index - '.$i.'<br />';
									$edit_task_on_prev = $res_time[$i-1]["created_on"];
									$datetime_edit_task_prev = explode(' ', $edit_task_on_prev);
                                                                        $date_edit_task_prev = explode('-', $datetime_edit_task_prev[0]);
                                                                        $time_edit_task_prev = explode(':', $datetime_edit_task_prev[1]);
                                                                        $edittask_prev = mktime($time_edit_task_prev[0]+4, $time_edit_task_prev[1], $time_edit_task_prev[2], $date_edit_task_prev[1], $date_edit_task_prev[2], $date_edit_task_prev[0]); // +4 т.к. фенге время$

//									echo '<br />Время редактирования'.$edit_task_on.' ID компании '.$res_time[$i]["company_id"];
//									echo '     Предыдущее время редактирования'.$edit_task_on_prev.' ID компании '.$res_time[$i-1]["company_id"].'<br /><br />   Потрачено времени '.$edittask.' - '.$edittask_prev.' = '.($edittask - $edittask_prev).'<br />';

                                                                        $idcomp = $res_time[$i]["company_id"];
									$index_company_id[$idcomp] += $edittask - $edittask_prev;
								}
							}
						}

						$text = $item3["text"];
	                                        $schetchik++;
        	                                $schetchik_excel = $schetchik + 1;
						$active_sheet->getRowDimension($schetchik_excel)->setRowHeight(-1);
						$active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$active_sheet->getStyle('B'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$active_sheet->getStyle('B'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$active_sheet->getStyle('C'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$active_sheet->getStyle('C'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$active_sheet->getStyle('D'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$active_sheet->getStyle('D'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
						$active_sheet->getStyle('D'.$schetchik_excel)->getAlignment()->setWrapText(true);
						$active_sheet->getStyle('F'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$active_sheet->getStyle('F'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$active_sheet->getStyle('I'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setWrapText(true);
						$active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
                                                $active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setWrapText(true);
						$active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);

                	                        $active_sheet->setCellValue('A'.$schetchik_excel , $schetchik);
						$active_sheet->setCellValue('B'.$schetchik_excel , "26.06.2014");
						$active_sheet->setCellValue('C'.$schetchik_excel , $date[2].".".$date[1].".".substr($date[0], 2, 2));
						$active_sheet->setCellValue('D'.$schetchik_excel , $lpu);
						$active_sheet->setCellValue('E'.$schetchik_excel , $terminal);
						$active_sheet->getCell('F'.$schetchik_excel)->getHyperlink()->setUrl('http://pm.citrt.net/index.php?c=task&a=view_task&id='.$item);
						$active_sheet->getStyle('F'.$schetchik_excel)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
						$active_sheet->getStyle('F'.$schetchik_excel)->getFont()->getColor()->applyFromArray(array('rgb' => '0000FF'));
                        	                $active_sheet->setCellValue('F'.$schetchik_excel , $item);
						$active_sheet->setCellValue('H'.$schetchik_excel , $text);
//						var_dump($index_company_id);
//							echo '<br /><br /><a href="http://pm.citrt.net/index.php?c=task&a=view_task&id='.$item.'">Задача № '.$item.'</a><br />';
						$timeworkabouttaskhtml = '';
						$timeworkabouttask = '';
						$daysworkabouttask = '';
						$daysworkabouttaskhtml = '';
						$kompanii = array_keys($index_company_id);
//						var_dump($kompanii);
//						var_dump($index_company_id);
						foreach($kompanii as $item_kompanii) {
							$sql = "
								SELECT name
								FROM og_companies
								WHERE
									id = ".$item_kompanii."
							";
							$kompaniisql = ssql($sql);
//							echo '<pre>';
							//var_dump($kompaniisql);
							$timeworkabouttaskhtml .= 'Компания: '.$kompaniisql[1]["name"].'<br />Время: '.round($index_company_id[$item_kompanii]/3600, 2).' ч.<br />';
							//$addtohtmlreport = 'Компания: '.$kompaniisql[1]["name"].'<br />Время: '.round($index_company_id[$item_kompanii]/3600, 2).' ч.<br />';
							$timeworkabouttask .= 'Компания: '.$kompaniisql[1]["name"].PHP_EOL.'Время: '.round($index_company_id[$item_kompanii]/3600, 2).' ч.'.PHP_EOL.PHP_EOL;
							$hourstask = round($index_company_id[$item_kompanii]/3600, 2);
							$daystask = ceil($index_company_id[$item_kompanii]/(3600*24))-1;
							$declinationdaysword = '';
							if($daystask > 0) {
								$declinationdays = $daystask % 10;
								if(($declinationdays > 10) && ($declinationdays < 15)) {
									$declinationdaysword = 'дней';
								}
								else if($declinationdays == 1) {
									$declinationdaysword = 'день';
								}
								else if(($declinationdays > 1) && ($declinationdays < 5)) {
									$declinationdaysword = 'дня';
								}
								else {
									$declinationdaysword = 'дней';
								}
								$daysworkabouttask .= 'Компания: '.$kompaniisql[1]["name"].PHP_EOL.'Срок нарушения: '.$daystask.' '.$declinationdaysword.PHP_EOL.PHP_EOL;
								$daysworkabouttaskhtml .= 'Компания: '.$kompaniisql[1]["name"].'<br />Срок нарушения: '.$daystask.' '.$declinationdaysword.'<br />';
							}
						}
//						echo $addtohtmlreport;
						$active_sheet->setCellValue('G'.$schetchik_excel , $timeworkabouttask);
						$active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setWrapText(true);
						$active_sheet->setCellValue('I'.$schetchik_excel , $daysworkabouttask);
                                                $active_sheet->getStyle('I'.$schetchik_excel)->getAlignment()->setWrapText(true);
						$verstka .= '<tr><td>'.$schetchik.'</td><td>'.date("d.m.y").'</td><td>'.$date[2].'.'.$date[1].'.'.substr($date[0], 2, 2).'</td><td>'.$lpu.'</td><td>'.$terminalhtml.'</td><td><a href="http://pm.citrt.net/index.php?c=task&a=view_task&id='.$item.'">'.$item.'</a></td><td>'.$timeworkabouttaskhtml.'</td><td>'.$text.'</td><td>'.$daysworkabouttaskhtml.'</td></tr>';

						$index_company_id = Array();
						$endexecutorcompany = Array();
						$different_companies = Array();

					}
//					echo '<br />-------------------------------------------------------------<br />';
				}
			}
		}
//		$openclose++;
//	}

//}

//header("Content-Type:application/vnd.ms-excel");
//header("Content-Disposition:attachment;filename='report_tasks".date("dmY").".xls'");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->save('php://output');
$filename = 'report_overdue_tasks_terminal_'.date('d_m_y_H_i_s').'.xls';

//Сохранение файла
//$objWriter->save('autodetectexpiredtasks/'.$filename);

$verstka .= '</table>';
//$ToID = array('Andrey.C@tatar.ru', 'A.H@tatar.ru', 'Timur.Zaripov@tatar.ru', 'L.G@tatar.ru', 'Elena.Lukina@tatar.ru', 'Aygul.Komarova@tatar.ru', 'Yuliya.Starikova@tatar.ru', 'katkov@infomatika.ru', 'Irina.Kireeva@tatar.ru', 'Adeliya.Zhilkina@tatar.ru', 'Ibragimov.Ruslan@tatar.ru', 'Stanislav.Ignatev@tatar.ru');
$ToID = array('Andrey.C@tatar.ru');
$FromID = 'terminal@tatar.ru';
$Subject = 'Просроченные задачи по терминалам Минздрава';
$Body = '<p>Статистика формируется по задачам из СУП с 01.03.2014 г.</p><p>На '.date("d-m-Y").' просроченных <a href="http://pm.citrt.net/plugins/autodetectexpiredtasks/'.$filename.'">задач</a> - '.$schetchik.' шт.</p>';

if(count($schetchik)>0)
	$Body .= $verstka;

echo $verstka;
// Отправка писем
/*for($i=0; $i<count($ToID); $i++) {
	opengoo_insert_queued_email_without_feng($ToID[$i], $FromID, $Subject, $Body);
}*/
//exit();
?>
