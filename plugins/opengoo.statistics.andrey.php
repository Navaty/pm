<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
//require_once 'phpexcel/Classes/PHPExcel.php';
$projectid = $_REQUEST["projectid"];

//$objPHPExcel = new PHPExcel();

//$objPHPExcel->setActiveSheetIndex(0);
//$active_sheet = $objPHPExcel->getActiveSheet();

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


$starttime = $_REQUEST["starttime"];
$endtime = $_REQUEST["endtime"];
echo '<pre>';
var_dump($starttime);
var_dump($endtime);

function rd($Date) {
$arr =explode(".",$Date);
$str = $arr[2]."-".$arr[1]."-".$arr[0];
return $str;
}

// a_statistics_projects_and_tasks

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

?>
<html>
<head>
<script type="text/javascript" src="http://cc.citrt.net/oktell/js/jquery.min.js"></script> 
<script type="text/javascript" src="http://cc.citrt.net/oktell/js/jquery-ui.custom.min.js"></script> 
<script type="text/javascript" src="http://cc.citrt.net/oktell/js/jquery.ui.datepicker-ru.js"></script> 
<script type="text/javascript" src="http://cc.citrt.net/oktell/js/jquery-ui-timepicker-addon.js"></script> 
<script type="text/javascript" src="http://cc.citrt.net/oktell/js/javascripts.js"></script> 
<script type="text/javascript" src="http://cc.citrt.net/oktell/js/heatcolor.js"></script> 
 <link href="http://cc.citrt.net/oktell/css/default/jquery-ui-1.8.7.custom.css" type="text/css" rel="stylesheet"/> 
<style>
.row2 { background-color: #E6E6E6;}
.level1 {   font-size: 15px; font-weight: bold;}
.level2 {   font-size: 14px; padding-left: 10px; font-weight: bold;}
.level3 {   font-size: 13px; padding-left: 30px;}
.level4 {   font-size: 12px; padding-left: 50px;}
.level5 {   font-size: 11px; padding-left: 70px;}

h1, h2,h3,h4,h5 {
    margin: 0px;
}

        table tr td {
                border: 1px solid black;
                vertical-align:  top;
        }

</style>
<script>
$("#data").tablesorter();
function sortwithcolor( column ) {
  $("#data > tbody > tr").heatcolor(
			    function() { return $("td:nth-child(" + column + ")", this).text(); }
			    );
};
$("th").click(function() {
    $(this).siblings().css("background-color","#cccccc").end().css("background-color","#dd0000");
    sortwithcolor( $(this).parent().children().index( this ) + 1 );
  });
sortwithcolor(8);
</script>
</head>
<body>
<?#print_r($_REQUEST);?>
<form action="http://pm.citrt.net/plugins/opengoo.statistics.andrey.php" method="POST">
  <table cellpadding="5">
    <tr>
      <td>Выбрать дату с</td>
      <td><input type="text" name="starttime" value="<?=$starttime;?>" class="datepicker"/></td>
      <td>по</td>
      <td><input type="text" name="endtime" value="<?=$endtime;?>"  class="datepicker"/></td>
    </tr>
    <tr>
      <td colspan="1"></td>
      <td>
<select name="projectid" id="projectid">
    <option value=4054 <? if($projectid=="4054"){ echo "selected";}?>>СТИС 60 ЕГИС ЗРТ</option>
  <option value=684 <? if($projectid=="684"){ echo "selected";}?>>Жалобы МИС РТ</option>
    <option value=1103 <? if($projectid=="1103"){ echo "selected";}?>>Э-образование КЦ</option>
    <option value=495 <? if($projectid=="495"){ echo "selected";}?>>ПГМУ (Старые)</option>
    <option value=2026 <? if($projectid=="2026"){ echo "selected";}?>>ПГМУ (Новые)</option>
    <option value=745 <? if($projectid=="745"){ echo "selected";}?>>ДОУ</option>
    <option value=916 <? if($projectid=="916"){ echo "selected";}?>>Инциденты</option>
    <option value=2553 <? if($projectid=="2553"){ echo "selected";}?>>Инциденты (Новый классификатор)</option>
    <option value=1211 <? if($projectid=="1211"){ echo "selected";}?>>Обращения КЦ</option>
    <option value=345 <? if($projectid=="345"){ echo "selected";}?>>Э-Образование</option>
    </select>
    </td>
      <td><input type="submit" value="Сформировать отчет"/>
/*<?php
echo '<a href="phpexcel_test.php?projectid='.$projectid.'&starttime='.$starttime.'&endtime='.$endtime.'">';
?>*/
</td>
    </tr>
</table>
<br/><hr/>
</form>
<button id="tasks">Получить задачи в формате Excel</button>

<script>
$('#tasks').click(function(){
	if(($("input[name*='starttime']").val()!='')&&($("input[name*='endtime']").val()!='')) {
		location.href = 'phpexcel_test.php?projectid=' + $('#projectid').val()  + '&starttime=' + $("input[name*='starttime']").val() + '&endtime=' + $("input[name*='endtime']").val();
	}
	else { alert('Введите период!');
});
</script>

<?
if($starttime==$endtime && $starttime && $endtime) {
   $period = "на ".$starttime;
} elseif($starttime && $endtime) {
   $period = "с ".$starttime." по ".$endtime;
} elseif(!$starttime && !$endtime) {
    $period = "за все время работы";
}

//echo opengoo_get_projectname_by_taskID(opengoo_get_projectparentid_by_projectID(684));
// $projectname2 = opengoo_get_projectname_by_taskID(1898);
// $projectname1 = opengoo_get_projectname_by_projectID(opengoo_get_projectparentid_by_projectID(opengoo_get_projectid_by_taskID(1898)));
//echo $projectname = $projectname1 ." / ". $projectname2;

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
default:
  $line = "МИС РТ";
  break;
}
?>
<b>Статистика сформирована по линии <?=$line;?>  <?=$period;?><br/>
время формирования статистики - <?=date("m.d.y")." ".date("H:i:s");?>
</b>
<br/><br/>
<?
echo "<table border='1' cellpadding='5' cellspacing='0' id=data>";
echo "<thead>";
echo "<tr class='row2'>
     <th>Направление</th>
     <th>Всего<br/>обращений</th>
     <th>Завершенные<br/>задачи</th>
     <th>Открытые<br/>задачи</th>
     </tr>";
if(!$projectid) { $projectid = 684;}
$tasks_per_project =  tasks_per_project($projectid,1,$starttime,$endtime);
echo '<pre>';
//var_dump($tasks["open"]);
//var_dump(count($tasks["close"]));

//var_dump($tasks);
echo "<tr>
       <th>Итого (уникальных задач)</th>
       <th> $DDtotal ".(count($tasks["open"])+count($tasks["close"]))."</th>
       <th> $DDtotal_close ".count($tasks["close"])."</th>
       <th> $DDtotal_open ".count($tasks["open"])."</th>
      </tr>";
echo "</thead><tbody>";

//echo $tasks_per_project;
echo "</tbody><tfoot>";
echo "<tr>
       <th>Итого (уникальных задач)</th>
       <th> $DDtotal ".(count($tasks["open"])+count($tasks["close"]))."</th>
       <th> $DDtotal_close ".count($tasks["close"])."</th>
       <th> $DDtotal_open ".count($tasks["open"])."</th>
      </tr>";
echo "</tfoot></table><pre>";

/*
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

$active_sheet->getColumnDimension('A')->setWidth(10);
$active_sheet->getColumnDimension('B')->setWidth(10);
$active_sheet->getColumnDimension('C')->setWidth(150);
$active_sheet->getColumnDimension('D')->setWidth(50);
$active_sheet->getColumnDimension('E')->setWidth(100);
$active_sheet->getColumnDimension('F')->setWidth(70);
$active_sheet->getColumnDimension('G')->setWidth(300);
$active_sheet->getColumnDimension('H')->setWidth(300);
$active_sheet->getColumnDimension('I')->setWidth(50);*/


echo '<table cellpadding="0" cellspacing="0" border="0">';
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
//                        var_dump($item);
                        $firstname = '';
                        $name = '';
                        $phone = '';
                        $fathername = '';
                        $district = '';
                        $organization = '';
                        $fio='';
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
                                        case "ЛПУ":
                                                $organization = $item4["value"];
                                        break;
                                }
                        }
                        //      var_dump($res4);

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
                //      echo count($res5);
                        $count = count($res5);
                        $comment = $res5[$count]["text"]; // Берем последний комментарий к задаче

                        $query3 = "SELECT
                                    text, created_on
                           FROM
                                    `og_project_tasks`
                           WHERE
                                    id = ".$item."
                        ";

                        $res3 = ssql($query3); // Запрашиваем данные
                        foreach($res3 as $item3) {
                                $text = $item3["text"];
                                $created_on = $item3["created_on"];
                                $schetchik++;

/*                              if($fio='') {
                                        $fio = $firstname.' '.$name.' '.$fathername;
                                }*/

//                                echo '<tr><td>'.$schetchik.'</td><td>'.$item.'</td><td>'.$district.'</td><td>'.$organization.'</td><td>'.$created_on.'</td><td>'.$fio.$firstname.' '.$name.' '.$fathername.'</td><td>'.$phone.'</td><td>'.$text.'</td><td>'.$comment.'</td></tr>';
                                echo '<tr><td>'.$schetchik.'</td><td>'.$item.'</td><td>'.$organization.'</td><td>'.$created_on.'</td><td>'.$fio.$firstname.' '.$name.' '.$fathername.'</td><td>'.$phone.'</td><td>'.$text.'</td><td>'.$comment.'</td><td>'.$status.'</td></tr>';
/*				$schetchik_excel = $schetchik + 1;
				$active_sheet->setCellValue('A'.$schetchik_excel , $schetchik);
				$active_sheet->setCellValue('B'.$schetchik_excel , $item);
				$active_sheet->setCellValue('C'.$schetchik_excel , $organization);
				$active_sheet->setCellValue('D'.$schetchik_excel , $created_on);
				$active_sheet->setCellValue('E'.$schetchik_excel , $fio.$firstname.' '.$name.' '.$fathername);
				$active_sheet->setCellValue('F'.$schetchik_excel , $phone);
				$active_sheet->setCellValue('G'.$schetchik_excel , $text);
				$active_sheet->setCellValue('H'.$schetchik_excel , $comment);
				$active_sheet->setCellValue('I'.$schetchik_excel , $status);*/
                        }


}
$openclose++;
echo '';
}
echo '</table>';
mysql_close($con);
//print_r($tasks);

/*header("Content-Type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename='tasks.xls'");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');*/

exit();
?>
</body>
</html>

