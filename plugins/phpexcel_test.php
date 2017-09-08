<?php
include_once "statusage.php"; //by almaz - usage control
//$time_start = microtime(1);
include ("db.inc.php");
include ("functions.php");
require_once 'phpexcel/Classes/PHPExcel.php';

//$projectid = $_REQUEST["projectid"];

/*function script_monitoring() {
  global $db,$time_start;
  $time_end = microtime(1);
  $diff = $time_end - $time_start;
  $memory = memory_get_peak_usage();
	echo $diff.'<br />';
	echo $memory;
}*/


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

/*$starttime = $_REQUEST["starttime"];
$endtime = $_REQUEST["endtime"];*/

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

$post = $_REQUEST;
$projectid = $post["projectid"];
$starttime = $post["starttime"];
$endtime = $post["endtime"];

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
case "11111111":
  $line = "ЕГИС ЗРТ Информация об ЛПУ";
  break;
case "4051":
  $line = "Электронная очередь";
  break;
case "12345678":
  $line = "СМЭВ";
  break;
case "123456789"
  $line = "Минздрав - Граждане";
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
//Настройки шрифта
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
//$projectid = "4054";
if($projectid) {

	if($projectid!='345') { // При электронном образовании падает, если включено форматирование
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
	}

	if($projectid == '12345678') {
		$tasks_per_project =  tasks_per_project('2553',1,$starttime,$endtime); // Сделано для того, чтобы не путать класификатор выбранный в opengoo.statistics.php
	}
	else if($projectid == '11111111') {
		$tasks_per_project =  tasks_per_project('4054',1,$starttime,$endtime);
	}
        else if($projectid == '123456789') {
                $tasks_per_project =  tasks_per_project('2553',1,$starttime,$endtime);
        }
	else {
 		$tasks_per_project =  tasks_per_project($projectid,1,$starttime,$endtime);
	}

	switch($projectid) {
	case "1103":
                $active_sheet->getColumnDimension('A')->setWidth(15);
                $active_sheet->getColumnDimension('B')->setWidth(10);
                $active_sheet->getColumnDimension('C')->setWidth(40);
                $active_sheet->getColumnDimension('D')->setWidth(120);
                $active_sheet->getColumnDimension('E')->setWidth(20);
                $active_sheet->getColumnDimension('F')->setWidth(50);
                $active_sheet->getColumnDimension('G')->setWidth(40);
                $active_sheet->getColumnDimension('H')->setWidth(200);
                $active_sheet->getColumnDimension('I')->setWidth(200);
                $active_sheet->getColumnDimension('J')->setWidth(10);
		$active_sheet->getColumnDimension('K')->setWidth(50);

                $active_sheet->setCellValue('A1', 'Порядковый №');
                $active_sheet->setCellValue('B1', '№ в фенге');
		$active_sheet->setCellValue('C1', 'Район');
                $active_sheet->setCellValue('D1', 'Организация');
                $active_sheet->setCellValue('E1', 'Дата обращения');
                $active_sheet->setCellValue('F1', 'Фамилия Имя Отчество');
                $active_sheet->setCellValue('G1', 'Контактный телефон');
                $active_sheet->setCellValue('H1', 'Суть обращения');
                $active_sheet->setCellValue('I1', 'Ход решения');
                $active_sheet->setCellValue('J1', 'Статус');
		$active_sheet->setCellValue('K1', 'Исполнитель');
	break;
	case "12345678":
                $active_sheet->getStyle('A1')->getFont()->setBold(true);
                $active_sheet->getStyle('B1')->getFont()->setBold(true);
                $active_sheet->getStyle('C1')->getFont()->setBold(true);
                $active_sheet->getStyle('D1')->getFont()->setBold(true);
                $active_sheet->getStyle('E1')->getFont()->setBold(true);
                $active_sheet->getStyle('F1')->getFont()->setBold(true);
		$active_sheet->getStyle('G1')->getFont()->setBold(true);
		$active_sheet->getStyle('H1')->getFont()->setBold(true);

/*                $active_sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);*/

		$active_sheet->getColumnDimension('A')->setWidth(5);
		$active_sheet->getColumnDimension('B')->setWidth(10);
		$active_sheet->getColumnDimension('C')->setWidth(50);
                $active_sheet->getColumnDimension('D')->setWidth(20);
                $active_sheet->getColumnDimension('E')->setWidth(20);
		$active_sheet->getColumnDimension('F')->setWidth(50);
		$active_sheet->getColumnDimension('G')->setWidth(20);
		$active_sheet->getColumnDimension('H')->setWidth(10);

                $active_sheet->setCellValue('A1', '№');
                $active_sheet->setCellValue('B1', '№ в СУП');
                $active_sheet->setCellValue('C1', 'Район и наименование муниципального учреждения');
                $active_sheet->setCellValue('D1', 'Ведомство');
                $active_sheet->setCellValue('E1', 'Дата инцидента');
		$active_sheet->setCellValue('F1', 'Суть обращения');
		$active_sheet->setCellValue('G1', 'Классификатор');
		$active_sheet->setCellValue('H1', 'Статус');

	break;
        case "123456789": // Брем из папки "Все обращения" и фильтруем только Сфера "Минздрав" Услуга "Запись к врачу РТ"
                $active_sheet->getStyle('A1')->getFont()->setBold(true);
                $active_sheet->getStyle('B1')->getFont()->setBold(true);
                $active_sheet->getStyle('C1')->getFont()->setBold(true);
                $active_sheet->getStyle('D1')->getFont()->setBold(true);
                $active_sheet->getStyle('E1')->getFont()->setBold(true);
                $active_sheet->getStyle('F1')->getFont()->setBold(true);
                $active_sheet->getStyle('G1')->getFont()->setBold(true);
                $active_sheet->getStyle('H1')->getFont()->setBold(true);
                $active_sheet->getStyle('I1')->getFont()->setBold(true);
                $active_sheet->getStyle('J1')->getFont()->setBold(true);
                $active_sheet->getStyle('K1')->getFont()->setBold(true);
                $active_sheet->getStyle('L1')->getFont()->setBold(true);

                $active_sheet->getColumnDimension('A')->setWidth(15);
                $active_sheet->getColumnDimension('B')->setWidth(10);
                $active_sheet->getColumnDimension('C')->setWidth(50);
                $active_sheet->getColumnDimension('D')->setWidth(20);
                $active_sheet->getColumnDimension('E')->setWidth(50);
                $active_sheet->getColumnDimension('F')->setWidth(40);
                $active_sheet->getColumnDimension('G')->setWidth(50);
                $active_sheet->getColumnDimension('H')->setWidth(20);
                $active_sheet->getColumnDimension('I')->setWidth(50);
                $active_sheet->getColumnDimension('K')->setWidth(10);
                $active_sheet->getColumnDimension('K')->setWidth(30);
                $active_sheet->getColumnDimension('L')->setWidth(40);

                $active_sheet->setCellValue('A1', 'Порядковый №');
                $active_sheet->setCellValue('B1', '№ в фенге');
                $active_sheet->setCellValue('C1', 'Район/Город');
                $active_sheet->setCellValue('D1', 'Дата обращения');
                $active_sheet->setCellValue('E1', 'Фамилия Имя Отчество');
                $active_sheet->setCellValue('F1', 'Контактный телефон');
                $active_sheet->setCellValue('G1', 'Суть обращения');
                $active_sheet->setCellValue('H1', 'Дата инцидента');
                $active_sheet->setCellValue('I1', 'Ход решения');
                $active_sheet->setCellValue('J1', 'Статус');
                $active_sheet->setCellValue('K1', 'Исполнитель');
                $active_sheet->setCellValue('L1', 'Классификатор');
        break;
	case "11111111":
                $active_sheet->getStyle('A1')->getFont()->setBold(true);
                $active_sheet->getStyle('B1')->getFont()->setBold(true);
                $active_sheet->getStyle('C1')->getFont()->setBold(true);
                $active_sheet->getStyle('D1')->getFont()->setBold(true);
                $active_sheet->getStyle('E1')->getFont()->setBold(true);
                $active_sheet->getStyle('F1')->getFont()->setBold(true);
                $active_sheet->getStyle('G1')->getFont()->setBold(true);
                $active_sheet->getStyle('H1')->getFont()->setBold(true);
                $active_sheet->getStyle('I1')->getFont()->setBold(true);
                $active_sheet->getStyle('J1')->getFont()->setBold(true);
                $active_sheet->getStyle('K1')->getFont()->setBold(true);
                $active_sheet->getStyle('L1')->getFont()->setBold(true);
                $active_sheet->getStyle('M1')->getFont()->setBold(true);
                $active_sheet->getStyle('N1')->getFont()->setBold(true);
                $active_sheet->getStyle('O1')->getFont()->setBold(true);

                $active_sheet->getColumnDimension('A')->setWidth(15);
                $active_sheet->getColumnDimension('B')->setWidth(10);
                $active_sheet->getColumnDimension('C')->setWidth(50);
                $active_sheet->getColumnDimension('D')->setWidth(20);
                $active_sheet->getColumnDimension('E')->setWidth(50);
                $active_sheet->getColumnDimension('F')->setWidth(40);
                $active_sheet->getColumnDimension('G')->setWidth(50);
		$active_sheet->getColumnDimension('H')->setWidth(50);
		$active_sheet->getColumnDimension('I')->setWidth(50);
		$active_sheet->getColumnDimension('J')->setWidth(50);
		$active_sheet->getColumnDimension('K')->setWidth(50);
                $active_sheet->getColumnDimension('M')->setWidth(50);
                $active_sheet->getColumnDimension('L')->setWidth(10);
                $active_sheet->getColumnDimension('N')->setWidth(30);
                $active_sheet->getColumnDimension('O')->setWidth(40);

                $active_sheet->setCellValue('A1', 'Порядковый №');
                $active_sheet->setCellValue('B1', '№ в фенге');
                $active_sheet->setCellValue('C1', 'Организация');
                $active_sheet->setCellValue('D1', 'Дата обращения');
                $active_sheet->setCellValue('E1', 'Фамилия Имя Отчество');
                $active_sheet->setCellValue('F1', 'Контактный телефон');
                $active_sheet->setCellValue('G1', 'Суть обращения');
                $active_sheet->setCellValue('H1', 'Информация о других администраторах ЛПУ');
                $active_sheet->setCellValue('I1', 'Какая система установлена кроме ЕГИС ЭЗ РТ');
                $active_sheet->setCellValue('J1', 'Сколько комутаторов на балансе ЛПУ');
                $active_sheet->setCellValue('K1', 'База прикрепления');
                $active_sheet->setCellValue('L1', 'Ход решения');
                $active_sheet->setCellValue('M1', 'Статус');
                $active_sheet->setCellValue('N1', 'Исполнитель');
                $active_sheet->setCellValue('O1', 'Классификатор');

	break;
	default:
		$active_sheet->getStyle('A1')->getFont()->setBold(true);
                $active_sheet->getStyle('B1')->getFont()->setBold(true);
                $active_sheet->getStyle('C1')->getFont()->setBold(true);
                $active_sheet->getStyle('D1')->getFont()->setBold(true);
                $active_sheet->getStyle('E1')->getFont()->setBold(true);
                $active_sheet->getStyle('F1')->getFont()->setBold(true);
                $active_sheet->getStyle('G1')->getFont()->setBold(true);
                $active_sheet->getStyle('H1')->getFont()->setBold(true);
                $active_sheet->getStyle('I1')->getFont()->setBold(true);
		$active_sheet->getStyle('J1')->getFont()->setBold(true);
		$active_sheet->getStyle('K1')->getFont()->setBold(true);

/*                $active_sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);*/

		$active_sheet->getColumnDimension('A')->setWidth(15);
		$active_sheet->getColumnDimension('B')->setWidth(10);
		$active_sheet->getColumnDimension('C')->setWidth(50);
		$active_sheet->getColumnDimension('D')->setWidth(20);
		$active_sheet->getColumnDimension('E')->setWidth(50);
		$active_sheet->getColumnDimension('F')->setWidth(40);
		$active_sheet->getColumnDimension('G')->setWidth(50);
		$active_sheet->getColumnDimension('H')->setWidth(50);
		$active_sheet->getColumnDimension('I')->setWidth(10);
                $active_sheet->getColumnDimension('J')->setWidth(30);
		$active_sheet->getColumnDimension('K')->setWidth(40);

		$active_sheet->setCellValue('A1', 'Порядковый №');
		$active_sheet->setCellValue('B1', '№ в фенге');
		$active_sheet->setCellValue('C1', 'Организация');
		$active_sheet->setCellValue('D1', 'Дата обращения');
		$active_sheet->setCellValue('E1', 'Фамилия Имя Отчество');
		$active_sheet->setCellValue('F1', 'Контактный телефон');
		$active_sheet->setCellValue('G1', 'Суть обращения');
		$active_sheet->setCellValue('H1', 'Ход решения');
		$active_sheet->setCellValue('I1', 'Статус');
                $active_sheet->setCellValue('J1', 'Исполнитель');
		$active_sheet->setCellValue('K1', 'Классификатор');
	break;
	}

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
//		foreach($massiv as $key => $value) {
		$key_massiv = array_keys($massiv); //ID задач приходят в виде ключа, переделываем их как значения
		$thiscount = count($key_massiv);
		for($this_i = 0; $this_i<$thiscount; $this_i++) {
//			var_dump($massiv[$this_i]);
			$item = $key_massiv[$this_i];
//                        $item = $key;
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
			$usluga = '';
			$distr_nmu = '';
			$vedomstvo = '';
			$data_incidenta = '';
			$klassificator = '';
			$other_admin = '';
			$other_system = '';
			$count_commut = '';
			$base_commut = '';
			$date_incident = '';
			$time_incident = '';
			$sphera = '';
                        $res4 = ssql($query4); //Смотрим доп. поля данной задачи
//                        foreach($res4 as $item4) {
			$countres4 = count($res4);
			for($res4_i = 0; $res4_i < $countres4; $res4_i++) {
//				echo '<pre>';
//				var_dump($res4[$res4_i]);
				$item4 = $res4[$res4_i];
				switch($item4["name"]) {
//                                switch($item4["name"]) {
					case "Район_и_наименование_муниципального_учреждения":
						$distr_nmu = $item4["value"];
					break;
                                        case "Дата_инцидента":
                                                $date_incident = $item4["value"];
                                        break;
                                        case "Время_инцидента":
                                                $time_incident = $item4["value"];
                                        break;
					case "Ведомство":
						$vedomstvo = $item4["value"];
					break;
					case "Дата_инцидента":
						$data_incidenta = $item4["value"];
					break;
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
					case "Услуга":
						$usluga = $item4["value"];
					break;
					case "Классификатор":
						$klassificator = $item4["value"];
                                        break;
					case "Сфера":
						$sphera = $item4["value"];
					break;
					case "Другие_администраторы_ЛПУ":
                                                $other_admin = $item4["value"];
                                        break;
					case "Система_кроме_ЕГИС":
                                                $other_system = $item4["value"];
                                        break;
					case "Количество_кммутаторов_на_балансе_ЛПУ":
                                                $count_commut = $item4["value"];
                                        break;
					case "База_прикрепления":
                                                $base_commut = $item4["value"];
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
//					if(($projectid!='11111111')||($projectid!='12345678')||(($projectid=='12345678')&&($usluga == 'СМЭВ'))||(($projectid=='11111111')&&($klassificator == 'Информация об ЛПУ'))) {
			        		switch($projectid) {
						        case "1103":
//				if($projectid=="1103") {
			                	                $schetchik++;
        			                	        $schetchik_excel = $schetchik + 1;
                	        		        	$active_sheet->setCellValue('A'.$schetchik_excel , $schetchik);
	                	                		$active_sheet->setCellValue('B'.$schetchik_excel , $item);

								$active_sheet->setCellValue('C'.$schetchik_excel , $district);
								$active_sheet->setCellValue('D'.$schetchik_excel , $organization);
			                                        $active_sheet->setCellValue('E'.$schetchik_excel , $created_on);
                        			                $active_sheet->setCellValue('F'.$schetchik_excel , $fio.$firstname.' '.$name.' '.$fathername);
			                                        $active_sheet->setCellValue('G'.$schetchik_excel , $phone);
                        			                $active_sheet->setCellValue('H'.$schetchik_excel , $text);
			                                        $active_sheet->setCellValue('I'.$schetchik_excel , $comment);
                        			                $active_sheet->setCellValue('J'.$schetchik_excel , $status);
								$active_sheet->setCellValue('K'.$schetchik_excel , $display_name);
							break;
							case "12345678":
								if($usluga == 'СМЭВ') {
				                	                $schetchik++;
        				                	        $schetchik_excel = $schetchik + 1;
                		        		        	$active_sheet->setCellValue('A'.$schetchik_excel , $schetchik);
	                		                		$active_sheet->setCellValue('B'.$schetchik_excel , $item);

									$active_sheet->getRowDimension($schetchik_excel)->setRowHeight(-1);
                                        	        		$active_sheet->getStyle('C'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
									$active_sheet->getStyle('F'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);

									$active_sheet->getCell('B'.$schetchik_excel)->getHyperlink()->setUrl('http://pm.citrt.net/index.php?c=task&a=view_task&id='.$item);
		                                                	$active_sheet->getStyle('B'.$schetchik_excel)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			                                                $active_sheet->getStyle('B'.$schetchik_excel)->getFont()->getColor()->applyFromArray(array('rgb' => '0000FF'));

									$active_sheet->setCellValue('C'.$schetchik_excel , $distr_nmu);
									$active_sheet->setCellValue('D'.$schetchik_excel , $vedomstvo);
									$active_sheet->setCellValue('E'.$schetchik_excel , $data_incidenta);
									$active_sheet->setCellValue('F'.$schetchik_excel , $text);
									$active_sheet->setCellValue('G'.$schetchik_excel , $klassificator);
									$active_sheet->setCellValue('H'.$schetchik_excel , $status);
								}
							break;
                                                        case "123456789":
                                                                if($sphera == 'Минздрав') {
                                                                        if($usluga == 'Запись на прием к врачу РТ') {
                                                                                $schetchik++;
                                                                                $schetchik_excel = $schetchik + 1;
                                                                                $active_sheet->setCellValue('A'.$schetchik_excel , $schetchik);
                                                                                $active_sheet->setCellValue('B'.$schetchik_excel , $item);
                                                                                $active_sheet->getRowDimension($schetchik_excel)->setRowHeight(-1);
                                                                                $active_sheet->getStyle('C'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
                                                                                $active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
                                                                                $active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
                                                                                $active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);

                                                                                $active_sheet->getCell('B'.$schetchik_excel)->getHyperlink()->setUrl('http://pm.citrt.net/index.php?c=task&a=view_task&id='.$item);
                                                                                $active_sheet->getStyle('B'.$schetchik_excel)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
                                                                                $active_sheet->getStyle('B'.$schetchik_excel)->getFont()->getColor()->applyFromArray(array('rgb' => '0000FF'));
                                                                                $active_sheet->setCellValue('C'.$schetchik_excel , $district);
                                                                                $active_sheet->setCellValue('D'.$schetchik_excel , $created_on);
                                                                                $active_sheet->setCellValue('E'.$schetchik_excel , $fio.$firstname.' '.$name.' '.$fathername);
                                                                                $active_sheet->setCellValue('F'.$schetchik_excel , $phone);
                                                                                $active_sheet->setCellValue('G'.$schetchik_excel , $text);
                                                                                $active_sheet->setCellValue('H'.$schetchik_excel , $date_incident.' '.$time_incident);
                                                                                $active_sheet->setCellValue('I'.$schetchik_excel , $comment);
                                                                                $active_sheet->setCellValue('J'.$schetchik_excel , $status);
                                                                                $active_sheet->setCellValue('K'.$schetchik_excel , $display_name);
                                                                                $active_sheet->setCellValue('L'.$schetchik_excel , $klassificator);
                                                                        }
                                                                }
                                                        break;
							case "11111111":
								if($klassificator == 'Информация об ЛПУ') {
				                	                $schetchik++;
        	        			        	        $schetchik_excel = $schetchik + 1;
                	                				$active_sheet->setCellValue('A'.$schetchik_excel , $schetchik);
				                	                $active_sheet->setCellValue('B'.$schetchik_excel , $item);

									$active_sheet->getRowDimension($schetchik_excel)->setRowHeight(-1);
        	                                                        $active_sheet->getStyle('C'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
									$active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
									$active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
									$active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
									$active_sheet->getStyle('I'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
									$active_sheet->getStyle('J'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
									$active_sheet->getStyle('K'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
									$active_sheet->getStyle('L'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);

									$active_sheet->getCell('B'.$schetchik_excel)->getHyperlink()->setUrl('http://pm.citrt.net/index.php?c=task&a=view_task&id='.$item);
			                                                $active_sheet->getStyle('B'.$schetchik_excel)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
                			                                $active_sheet->getStyle('B'.$schetchik_excel)->getFont()->getColor()->applyFromArray(array('rgb' => '0000FF'));

        					                        $active_sheet->setCellValue('C'.$schetchik_excel , $organization);
				                	                $active_sheet->setCellValue('D'.$schetchik_excel , $created_on);
                        					        $active_sheet->setCellValue('E'.$schetchik_excel , $fio.$firstname.' '.$name.' '.$fathername);
			        	                        	$active_sheet->setCellValue('F'.$schetchik_excel , $phone);
	                				                $active_sheet->setCellValue('G'.$schetchik_excel , $text);
									$active_sheet->setCellValue('H'.$schetchik_excel , $other_admin);
                                                        	        $active_sheet->setCellValue('I'.$schetchik_excel , $other_system);
                                                                	$active_sheet->setCellValue('J'.$schetchik_excel , $count_commut);
	                                                                $active_sheet->setCellValue('K'.$schetchik_excel , $base_commut);
				        	                        $active_sheet->setCellValue('L'.$schetchik_excel , $comment);
                					                $active_sheet->setCellValue('M'.$schetchik_excel , $status);
				                                        $active_sheet->setCellValue('N'.$schetchik_excel , $display_name);
									$active_sheet->setCellValue('O'.$schetchik_excel , $klassificator);
								}
							break;
//				}
//				else {
							default:
			                	                $schetchik++;
        			                	        $schetchik_excel = $schetchik + 1;
                	        		        	$active_sheet->setCellValue('A'.$schetchik_excel , $schetchik);
	                	                		$active_sheet->setCellValue('B'.$schetchik_excel , $item);

								$active_sheet->getRowDimension($schetchik_excel)->setRowHeight(-1);
                                                                $active_sheet->getStyle('C'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
								$active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
								$active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
								$active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);

								$active_sheet->getCell('B'.$schetchik_excel)->getHyperlink()->setUrl('http://pm.citrt.net/index.php?c=task&a=view_task&id='.$item);
		                                                $active_sheet->getStyle('B'.$schetchik_excel)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
                		                                $active_sheet->getStyle('B'.$schetchik_excel)->getFont()->getColor()->applyFromArray(array('rgb' => '0000FF'));


        				                        $active_sheet->setCellValue('C'.$schetchik_excel , $organization);
			                	                $active_sheet->setCellValue('D'.$schetchik_excel , $created_on);
                        				        $active_sheet->setCellValue('E'.$schetchik_excel , $fio.$firstname.' '.$name.' '.$fathername);
			                                	$active_sheet->setCellValue('F'.$schetchik_excel , $phone);
	                			                $active_sheet->setCellValue('G'.$schetchik_excel , $text);
			        	                        $active_sheet->setCellValue('H'.$schetchik_excel , $comment);
                				                $active_sheet->setCellValue('I'.$schetchik_excel , $status);
			                                        $active_sheet->setCellValue('J'.$schetchik_excel , $display_name);
								$active_sheet->setCellValue('K'.$schetchik_excel , $klassificator);
							break;
						}
//					}
			}
		}
		$openclose++;
	}

}

header("Content-Type:application/vnd.ms-excel");
//header("Content-Disposition:attachment;filename='report_tasks.xls'");
header("Content-Disposition:attachment;filename='".$line." ".date("d-m-Y").".xls'");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
//script_monitoring();
//exit();
?>
