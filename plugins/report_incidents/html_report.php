<?php
include ("../db.inc.php");
include ("../functions.php");
set_time_limit(0);

$day = date("d");
$month = date("m");
$year = date("Y");
$hour = date("H");
$minute = date("i");
$minuteMinus5 = $minute - 20;

//echo '<pre>';
if($minuteMinus5 < 0) {
	$hourMinus1 = $hour - 1;
	$minuteMinus5 = 60 + $minuteMinus5;
	$query = "SELECT
			ri.count_tasks as count_tasks, ri.solved as solved, ri.overdue as overdue, ri.overdue_tasks as overdue_tasks, ricl.name as classifier, ris.name as source
		  FROM
			`report_incidents` as ri, `report_incidents_classifier` as ricl, `report_incidents_sources` as ris
		  WHERE
			MONTH(`date`) = ".$month."
		  AND
			DAY(`date`) = ".$day."
		  AND
			YEAR(`date`) = ".$year."
		  AND
			HOUR(`date`)>= ".$hourMinus1."
		  AND
			HOUR(`date`) <= ".$hour."
		  AND
			(MINUTE(`date`) <= ".$minute."
				OR
			MINUTE(`date`) >= ".$minuteMinus5.")
		  AND
			ricl.id = ri.classifier_id
		  AND
			ris.id = ri.source_id
		  ORDER BY source";
}
else {
	$query = "SELECT
			 ri.count_tasks as count_tasks, ri.solved as solved, ri.overdue as overdue, ri.overdue_tasks as overdue_tasks, ricl.name as classifier, ris.name as source
		  FROM
			`report_incidents` as ri, `report_incidents_classifier` as ricl, `report_incidents_sources` as ris
		  WHERE
			MONTH(`date`) = ".$month."
		  AND
			DAY(`date`) = ".$day."
		  AND
			YEAR(`date`) = ".$year."
		  AND
			HOUR(`date`)= ".$hour."
		  AND
			MINUTE(`date`) >= ".$minuteMinus5."
		  AND
			MINUTE(`date`) <= ".$minute."
                  AND
                        ricl.id = ri.classifier_id
                  AND
                        ris.id = ri.source_id
		  ORDER BY source";
}
$res = ssql($query);
//var_dump($res);
$verstka = '<table border="1" style="width: 900px;">';
$verstka .= '<tr><th style="width: 5px;"></th><th style="width: 200px;">Классификатор</th><th style="width: 120px;">Зафиксировано</th><th style="width: 70px;">Решено</th><th style="width: 120px;">Нарушен срок</th><th style="width: 200px;">Задачи с нарушенным сроком</th></tr>';
$source = '';
$count_source = 0;
foreach($res as $item) {
	$classifier = $item["classifier"];
	$count_tasks = $item["count_tasks"];
	$solved = $item["solved"];
	$overdue = $item["overdue"];
	$overdue_tasks = json_decode($item["overdue_tasks"], true);
	$tasks_html = '';
	if(is_array($overdue_tasks)) {
		foreach($overdue_tasks as $item_task) {
			$tasks_html .= '<a href="http://pm.citrt.net/index.php?c=task&a=view_task&id='.$item_task.'">'.$item_task.'</a> ';
		}
	}
	if($source != $item["source"]) {
		$source = $item["source"];
		$count_source++;
		switch($count_source){
			case "1":
//				$color = '#FFB473';
				$color = '#FF9840';
			break;
                        case "2":
				$color = '#FFC040';
//				$color = '#FF9840';
                        break;
                        case "3":
//				$color = '#FFD173';
				$color = '#466FD5';
                        break;
                        case "4":
				$color = '#1D7074';
                        break;
                        case "5":
				$color = '#6C8AD5';
                        break;
                        case "6":
				$color = '#FFAB00';
                        break;
                        case "7":
				$color = '#01939A';
                        break;
                        case "8":
				$color = '#FE7276';
                        break;
                        case "9":
				$color = '#123EAB';
                        break;
                        case "10":
				$color = '#FF7600';
                        break;
                        default:
				$color = '#ffffff';
                        break;
		}
		$verstka .= '<tr class="source_style"><td colspan="6" style="padding: 5px; background: '.$color.'">'.$source.'</td></tr>';
	}
	$verstka .= '<tr class="classifier_style"><td style="background: '.$color.'"></td><td style="padding: 5px; text-align: left;">'.$classifier.'</td><td style="text-align: center; padding: 5px;">'.$count_tasks.'</td><td style="text-align: center; padding: 5px;">'.$solved.'</td><td style="text-align: center; padding: 5px;">'.$overdue.'</td><td style="text-align: center; padding: 5px;">'.$tasks_html.'</td></tr>';
}
$verstka .= '</table>';
//$ToID = array('Andrey.C@tatar.ru', 'A.H@tatar.ru', 'Elena.Lukina@tatar.ru');
$ToID = array('Andrey.C@tatar.ru');
$FromID = 'terminal@tatar.ru';
$Subject = 'Ежемесячный отчет по задачам ГМУ';

$monthMinus1 = $month -1;
$starttime = '01.'.$monthMinus1.'.'.date("Y");
$endtime = date("t", mktime(0, 0, 0, $monthMinus1, 1, date("Y"))).'.'.$monthMinus1.'.'.date("Y");

$Body = '<p>Статистика изменилась</p><p>Статистика сформирована по задачам из СУП с '.$starttime.' по '.$endtime .'</p>';
$Body .= $verstka;
for($i=0; $i<count($ToID); $i++) {
        opengoo_insert_queued_email_without_feng($ToID[$i], $FromID, $Subject, $Body);
}

//echo $Body;
?>
