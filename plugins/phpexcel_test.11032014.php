<?php
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
require_once 'phpexcel/Classes/PHPExcel.php';

//$projectid = $_REQUEST["projectid"];
$projectid = '2553';

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

//$starttime = $_REQUEST["starttime"];
$starttime = '01.01.2013';
//$endtime = $_REQUEST["endtime"];
$endtime = '28.02.2014';

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

//$post = $_REQUEST;
//$projectid = $post["projectid"];
$projectid = '2553';
//$starttime = $post["starttime"];
//$endtime = $post["endtime"];

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
  $line = "ЕГИС ЗРТ";
  break;
case "2553":
  $line = "Инциденты";
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
$objPHPExcel->getDefaultStyle()->getFont()->setSize(8);



//$projectid = "4054";
if($projectid) {
	$tasks_per_project =  tasks_per_project($projectid,1,$starttime,$endtime);

//	if($projectid=="1103") {
                $active_sheet->getColumnDimension('A')->setWidth(15);
                $active_sheet->getColumnDimension('B')->setWidth(10);
                $active_sheet->getColumnDimension('C')->setWidth(10);
                $active_sheet->getColumnDimension('D')->setWidth(20);
                $active_sheet->getColumnDimension('E')->setWidth(20);
                $active_sheet->getColumnDimension('F')->setWidth(20);
                $active_sheet->getColumnDimension('G')->setWidth(60);
                $active_sheet->getColumnDimension('H')->setWidth(10);
                $active_sheet->getColumnDimension('I')->setWidth(20);
                $active_sheet->getColumnDimension('J')->setWidth(200);
		$active_sheet->getColumnDimension('K')->setWidth(200);

                $active_sheet->setCellValue('A1', 'Порядковый №');
                $active_sheet->setCellValue('B1', '№ в фенге');
		$active_sheet->setCellValue('C1', 'Сфера');
                $active_sheet->setCellValue('D1', 'Услуга');
                $active_sheet->setCellValue('E1', 'Классификатор');
                $active_sheet->setCellValue('F1', 'Район/Город');
                $active_sheet->setCellValue('G1', 'Место приема');
                $active_sheet->setCellValue('H1', 'Дата инцидента');
                $active_sheet->setCellValue('I1', 'Источник');
                $active_sheet->setCellValue('J1', 'Описание');
		$active_sheet->setCellValue('K1', 'Последний комментарий');
//	}

	//var_dump($tasks["open"]);
	$openclose = 0;
	while($openclose<2) {
		if($openclose==0) {
		        $massiv = $tasks["open"];
		        $status = "Открыта";
		}
		if($openclose==1) {
		        $massiv = $tasks["close"];
		        $status = "Закрыта";
		}
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
                        $firstname = '';
                        $name = '';
                        $phone = '';
                        $fathername = '';
                        $district = '';
                        $organization = '';
                        $fio='';
			$sphere='';
			$usluga='';
			$klassificator='';
			$place='';
			$dateinc='';
			$source='';
                        $res4 = ssql($query4); //Смотрим доп. поля данной задачи
                        foreach($res4 as $item4) {
                                switch($item4["name"]) {
                                        case "Фамилия":
                                                $firstname = $item4["value"];
                                        break;
                                        case "Имя":
                                                $name = $item4["value"];
                                        break;
                                        case "Контактный_телефон":
                                                $phone = $item4["value"];
                                        break;
                                        case "Контактный телефон":
                                                $phone = $item4["value"];
                                                                                break;
                                        case "Контактный_номер":
                                                $phone = $item4["value"];
                                        break;
                                        case "Отчество":
                                                $fathername = $item4["value"];
                                        break;
                                        case "Район":
                                                $district = $item4["value"];
                                        break;
                                        case "Район/Город":
                                                $district = $item4["value"];
                                        break;
                                        case "ФИО_пользователя":
                                                $fio = $item4["value"];
                                        break;
					case "ФИО":
                                                $fio = $item4["value"];
                                        break;
                                        case "ЛПУ":
                                                $organization = $item4["value"];
                                        break;
					case "Организация":
                                                $organization = $item4["value"];
                                        break;
                                        case "Сфера":
                                                $sphere = $item4["value"];
                                        break;
                                        case "Услуга":
                                                $usluga = $item4["value"];
                                        break;
                                        case "Классификатор":
                                                $klassificator = $item4["value"];
                                        break;
/*                                        case "Район/Город":
                                                $organization = $item4["value"];
                                        break;*/
                                        case "Место_приема":
                                                $place = $item4["value"];
                                        break;
                                        case "Дата_инцидента":
                                                $dateinc = $item4["value"];
                                        break;
                                        case "Источник":
                                                $source = $item4["value"];
                                        break;
                                }
                        }
                        $query5 = "
                                SELECT
                                        text
                                FROM
                                        `og_comments`
                                WHERE
                                        rel_object_id = ".$item."
                                AND
                                        rel_object_manager = 'ProjectTasks'
                        ";

                        $res5 = ssql($query5); //Смотрим все комментарии данной задачи
                        $count = count($res5);
                        $comment = $res5[$count]["text"]; // Берем последний комментарий к задаче
// Добавил к поиску поле assigned_to_user_id
                        $query3 = "SELECT
                                    text, created_on, assigned_to_user_id
                           FROM
                                    `og_project_tasks`
                           WHERE
                                    id = ".$item."
                        ";

                        $res3 = ssql($query3); // Запрашиваем данные
                        foreach($res3 as $item3) {
	                                $text = $item3["text"];
        	                        $created_on = $item3["created_on"];
					/*
						Смотрю id исполнителя задачи и ищу его display_name
					*/
                                        $idexecutor = $item3["assigned_to_user_id"];
                                        $display_name_query = "SELECT display_name FROM `og_users` WHERE id='".$idexecutor."'";
                                        $display_name_res = ssql($display_name_query);
                                        foreach($display_name_res as $display_name_item) {
                                                $display_name = $display_name_item["display_name"];
                                        }
//				if(($sphere == 'Минздрав')&&($usluga == 'Запись на прием к врачу РТ')&&(($klassificator == 'Невозможно записаться/перезаписаться на прием')||($klassificator == 'Различные ошибки, возникающие на портале'))&&($source == 'Портал(uslugi.tatarstan.ru)')) {
				if(($sphere == 'Минздрав')&&(($usluga == 'Запись на прием к врачу РТ')||($usluga == 'Запись на прием к врачу Нижнекамск/Камские Поляны'))) {
                	                $schetchik++;
                        	        $schetchik_excel = $schetchik + 1;
                                	$active_sheet->setCellValue('A'.$schetchik_excel , $schetchik);
	                                $active_sheet->setCellValue('B'.$schetchik_excel , $item);


//				if($projectid=="1103") {
					$active_sheet->setCellValue('C'.$schetchik_excel , $sphere);
					$active_sheet->setCellValue('D'.$schetchik_excel , $usluga);
                                        $active_sheet->setCellValue('E'.$schetchik_excel , $klassificator);
                                        $active_sheet->setCellValue('F'.$schetchik_excel , $district);
                                        $active_sheet->setCellValue('G'.$schetchik_excel , $place);
                                        $active_sheet->setCellValue('H'.$schetchik_excel , $dateinc);
                                        $active_sheet->setCellValue('I'.$schetchik_excel , $source);
                                        $active_sheet->setCellValue('J'.$schetchik_excel , $text);
					$active_sheet->setCellValue('K'.$schetchik_excel , $comment);
/*				}
				else {
        	                        $active_sheet->setCellValue('C'.$schetchik_excel , $organization);
                	                $active_sheet->setCellValue('D'.$schetchik_excel , $created_on);
                        	        $active_sheet->setCellValue('E'.$schetchik_excel , $fio.$firstname.' '.$name.' '.$fathername);
                                	$active_sheet->setCellValue('F'.$schetchik_excel , $phone);
	                                $active_sheet->setCellValue('G'.$schetchik_excel , $text);
        	                        $active_sheet->setCellValue('H'.$schetchik_excel , $comment);
                	                $active_sheet->setCellValue('I'.$schetchik_excel , $status);
                                        $active_sheet->setCellValue('J'.$schetchik_excel , $display_name);
				}*/
				}
			}
		}
		$openclose++;
	}

}

header("Content-Type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename='report_tasks.xls'");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

//exit();
?>
