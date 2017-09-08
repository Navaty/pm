<?php
set_time_limit(0);
//error_reporting(-1);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);

include ("take_tasks_from_base.php");
require_once 'phpexcel/Classes/PHPExcel.php';

function give_month_word($this_period) {
	switch($this_period) {
		case "1":
			$this_period = "январь";
		break;
        	case "2":
                	$this_period = "февраль";
	        break;
        	case "3":
                	$this_period = "март";
	        break;
        	case "4":
                	$this_period = "апрель";
	        break;
        	case "5":
                	$this_period = "май";
	        break;
        	case "6":
                	$this_period = "июнь";
	        break;
        	case "7":
                	$this_period = "июль";
	        break;
        	case "8":
                	$this_period = "август";
	        break;
        	case "9":
                	$this_period = "сентябрь";
	        break;
        	case "10":
                	$this_period = "октябрь";
	        break;
        	case "11":
                	$this_period = "ноябрь";
	        break;
        	case "12":
                	$this_period = "декабрь";
	        break;
		default:
			$this_period = "неопределено";
		break;
	}
	return $this_period;
}

$post = $_REQUEST;
$rangedate = false;
$current_time = time();
$source = null;
$prev_overdue_source = null;
$count_tasks_source = null;
$solved_source = null;
$overdue_source = null;
$schetchik_excel = null;


if($post['rangedate']=='true') {
	$rangedate = true;
}

$this_period = date("m", mktime(0, 0, 0, (date("m")-1), 1, date("Y")));
$this_period_word = give_month_word($this_period);
$prev_period_word = give_month_word($this_period-1);
//$title = "Отчет по задачам ГМУ за ".date("M", mktime(0, 0, 0, (date("m")-1), 1, date("Y")));
if($rangedate) {
	$title = "Отчет за период";
}
else {
	$title = "Отчет за ".$this_period_word;
}

$counter = 0;
$count_source = 0;
$day = date("d");
$month = date("m");
$year = date("Y");
$hour = date("H") + 1;
$minute = date("i") + 10;
$minuteMinus5 = $minute - 20;


$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$active_sheet = $objPHPExcel->getActiveSheet();
$active_sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$active_sheet->getPageSetup()->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

//Поля документа
$active_sheet->getPageMargins()->setTop(3);
$active_sheet->getPageMargins()->setRight(0.75);
$active_sheet->getPageMargins()->setLeft(0.75);
$active_sheet->getPageMargins()->setBottom(2);

//Название листа
$active_sheet->setTitle($title);

//Настройки шрифта
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

$active_sheet->getStyle('A1:H1')->getFont()->setBold(true);
/*$active_sheet->getStyle('B1')->getFont()->setBold(true);
$active_sheet->getStyle('C1')->getFont()->setBold(true);
$active_sheet->getStyle('D1')->getFont()->setBold(true);
$active_sheet->getStyle('E1')->getFont()->setBold(true);*/
$active_sheet->getStyle('A1:H1')->getFont()->setSize(13);
$active_sheet->getStyle('A1:H1')->applyFromArray(
                                                                            array(
                                                                                'fill' => array(
                                                                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                'color' => array('rgb' => 'ffffff')
                                                                                ),
                                                                                'borders' => array(
                                                                                    'allborders'  => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                                                                                )
                                                                            )
                                                                        );

$active_sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
/*$active_sheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$active_sheet->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$active_sheet->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$active_sheet->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);*/

$active_sheet->getColumnDimension('A')->setWidth(30);
$active_sheet->getColumnDimension('B')->setWidth(20);
$active_sheet->getColumnDimension('C')->setWidth(20);
$active_sheet->getColumnDimension('D')->setWidth(20);
$active_sheet->getColumnDimension('E')->setWidth(30);
$active_sheet->getColumnDimension('F')->setWidth(55);

$active_sheet->setCellValue('A1', 'Источник');
$active_sheet->setCellValue('B1', 'Зафиксировано');
$active_sheet->setCellValue('C1', 'Открыто');
$active_sheet->setCellValue('D1', 'Решено');
if($rangedate) {
	$this_period_word = 'выбранный период';
}
$active_sheet->setCellValue('E1', 'Нарушен срок за '.$this_period_word);
$active_sheet->setCellValue('F1', 'Задачи с нарушенным сроком за '.$this_period_word);

if(!$rangedate) {
	$active_sheet->getColumnDimension('G')->setWidth(30);
	$active_sheet->getColumnDimension('H')->setWidth(55);

	$active_sheet->setCellValue('G1', 'Открытые задачи за '.$prev_period_word);
	$active_sheet->setCellValue('H1', 'Задачи, открытые за месяц: '.$prev_period_word);
}


if($minuteMinus5 < 0) {
        $hourMinus1 = $hour - 1;
        $minuteMinus5 = 60 + $minuteMinus5;
        $query = "SELECT
                        ri.overdue_tasks_prev_month as overdue_tasks_prev_month, ri.count_tasks as count_tasks, ri.solved as solved, ri.overdue as overdue, ri.overdue_tasks as overdue_tasks, ricl.name as classifier, ris.name as source, ri.id as idreportcell
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
		  AND
			ri.show = 0
                  ORDER BY source";
}
else {
        $query = "SELECT
                         ri.overdue_tasks_prev_month as overdue_tasks_prev_month, ri.count_tasks as count_tasks, ri.solved as solved, ri.overdue as overdue, ri.overdue_tasks as overdue_tasks, ricl.name as classifier, ris.name as source, ri.id as idreportcell
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
                  AND
                        ri.show = 0
                  ORDER BY source";
}
//echo "<br/>-----$query---------<br/>";
//var_dump($query);
$res = ssql($query);
//echo "<br/>-----$res---------<br/>";
//var_dump($res);

$red = new PHPExcel_Style();
$red->applyFromArray(
    array('fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('rgb' => 'FF3333')
        ),
        'borders' => array(
            'bottom'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
            'right'   => array('style' => PHPExcel_Style_Border::BORDER_THIN)
        )
    ));

$color = 'ffffff';
$color_cell = 'ffffff';

if (isset($res)) {
foreach($res as $item) {

	$counter++;
	$schetchik_excel = $counter+1;

	$active_sheet->getRowDimension($schetchik_excel)->setRowHeight(-1);

        $classifier = htmlspecialchars_decode($item["classifier"]);
	$idreportcell = $item["idreportcell"];
        $count_tasks = $item["count_tasks"];
        $solved = $item["solved"];
        $overdue = $item["overdue"];
        $overdue_tasks = json_decode($item["overdue_tasks"], true);
	$overdue_tasks_prev_month = json_decode($item["overdue_tasks_prev_month"], true);
        $tasks_excel = '';
	$prev_tasks = '';
        if(is_array($overdue_tasks)) {
                foreach($overdue_tasks as $item_task) {
                        $tasks_excel .= 'http://pm.citrt.net/index.php?c=task&a=view_task&id='.$item_task.PHP_EOL;
                        $timework = get_info_about_lukina_task($item_task, $current_time);
                        foreach($timework["companiesworktime"] as $item_worktime) {
                                        $tasks_excel .= $item_worktime["name"].' работал над задачей '.round( (($item_worktime["time"])/(60*60)), 2, PHP_ROUND_HALF_UP).' ч.'.PHP_EOL;
                        }

                }
        }
        if(is_array($overdue_tasks_prev_month)) {
                foreach($overdue_tasks_prev_month as $item_prev_task) {
                        $prev_tasks .= 'http://pm.citrt.net/index.php?c=task&a=view_task&id='.$item_prev_task.PHP_EOL;
                }
        }

        if($source != $item["source"]) {
                $count_source++;
		if($source != '') {
			$active_sheet->getStyle('A'.$schetchik_excel.':H'.$schetchik_excel)->getFont()->setSize(13);

		        $active_sheet->getStyle('A'.$schetchik_excel.':H'.$schetchik_excel)->getFont()->setBold(true);
		        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setWrapText(true);

		        $active_sheet->getStyle('B'.$schetchik_excel.':E'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		        $active_sheet->getStyle('B'.$schetchik_excel.':E'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

//		        $active_sheet->getStyle('C'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//		        $active_sheet->getStyle('C'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

		        $active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		        $active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		        $active_sheet->getStyle('F'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		        $active_sheet->getStyle('F'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		        $active_sheet->getStyle('F'.$schetchik_excel)->getAlignment()->setWrapText(true);

                        $active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setWrapText(true);

			$itogo_style = '';
			if(!$rangedate) {
				$itogo_style = 'A'.$schetchik_excel.':H'.$schetchik_excel;
			}
			else {
				$itogo_style = 'A'.$schetchik_excel.':F'.$schetchik_excel;
			}

		        $active_sheet->getStyle($itogo_style)->applyFromArray(
                                                                            array(
                                                                                'fill' => array(
                                                                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                'color' => array('rgb' => $color)
                                                                                ),
                                                                                'borders' => array(
                                                                                    'allborders'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                                                                )
                                                                            )
                                                                        );

		        $active_sheet->setCellValue('A'.$schetchik_excel, 'Итого');
		        $active_sheet->setCellValue('B'.$schetchik_excel, $count_tasks_source);
			$active_sheet->setCellValue('C'.$schetchik_excel, ($count_tasks_source - $solved_source));
		        $active_sheet->setCellValue('D'.$schetchik_excel, $solved_source);
			$active_sheet->setCellValue('E'.$schetchik_excel, $overdue_source);
			if(!$rangedate) {
	                        $active_sheet->setCellValue('G'.$schetchik_excel, $prev_overdue_source);
			}
			$count_tasks_source = 0;
			$solved_source = 0;
			$overdue_source = 0;
			$counter++;
	                $schetchik_excel = $counter+1;
		}
                $source = htmlspecialchars_decode($item["source"]);

	        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$active_sheet->getStyle('A'.$schetchik_excel)->getFont()->setBold(true);
		$active_sheet->getStyle('A'.$schetchik_excel)->getFont()->setSize(13);

		switch($count_source){
                        case "1":
                                $color = 'FF9840';
				$color_cell = 'FFC48A';
                        break;
                        case "2":
                                $color = 'FFC040';
				$color_cell = 'FFDE86';
                        break;
                        case "3":
                                $color = '466FD5';
				$color_cell = 'A1B2D5';
                        break;
                        case "4":
                                $color = '1D7074';
				$color_cell = '22a2a9';
                        break;
                        case "5":
                                $color = '6C8AD5';
				$color_cell = '9DACD5';
                        break;
                        case "6":
                                $color = 'FFAB00';
				$color_cell = 'FFD573';
                        break;
                        case "7":
                                $color = '01939A';
				$color_cell = '697F9A';
                        break;
                        case "8":
                                $color = 'FE7276';
				$color_cell = 'FEBAC3';
                        break;
                        case "9":
                                $color = '123EAB';
				$color_cell = '6875AB';
                        break;
                        case "10":
                                $color = 'FF7600';
				$color_cell = 'FFBD85';
                        break;
                        default:
                                $color = 'ffffff';
				$color_cell = 'ffffff';
                        break;
                }
		$style_header = '';
		if($rangedate) {
			$style_header = 'A'.$schetchik_excel.':F'.$schetchik_excel;
		}
		else {
			$style_header = 'A'.$schetchik_excel.':H'.$schetchik_excel;
		}
		$active_sheet->getStyle($style_header)->applyFromArray(
									    array(
									        'fill' => array(
									        'type' => PHPExcel_Style_Fill::FILL_SOLID,
								                'color' => array('rgb' => $color)
										),
                                                                                'borders' => array(
                                                                                    'allborders'  => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                                                                             	)
									    )
									);
		$active_sheet->mergeCells($style_header);
		$active_sheet->setCellValue('A'.$schetchik_excel, $source);
		$counter++;
		$schetchik_excel = $counter+1;

	}
	$active_sheet->getStyle('A'.$schetchik_excel)->getFont()->setBold(true);
	$active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
	$active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setWrapText(true);

	$active_sheet->getStyle('B'.$schetchik_excel.':E'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$active_sheet->getStyle('B'.$schetchik_excel.':E'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);


/*	$active_sheet->getStyle('D'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
	$active_sheet->getStyle('D'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);*/

	$active_sheet->getStyle('F'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$active_sheet->getStyle('F'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$active_sheet->getStyle('F'.$schetchik_excel)->getAlignment()->setWrapText(true);

	$style_body = '';
	if(!$rangedate) {
		$active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

		$active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        	$active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        $active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setWrapText(true);
		$style_body = 'A'.$schetchik_excel.':H'.$schetchik_excel;
	}
	else {
		$style_body = 'A'.$schetchik_excel.':F'.$schetchik_excel;
	}

	$active_sheet->getStyle($style_body)->applyFromArray(
                                                                            array(
                                                                                'fill' => array(
                                                                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                'color' => array('rgb' => $color_cell)
                                                                                ),
										'borders' => array(
									            'allborders'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
									        )
                                                                            )
                                                                        );




	$active_sheet->setCellValue('A'.$schetchik_excel, $classifier);
	$active_sheet->setCellValue('B'.$schetchik_excel, $count_tasks);
	$active_sheet->setCellValue('C'.$schetchik_excel, ($count_tasks - $solved));
	$active_sheet->setCellValue('D'.$schetchik_excel, $solved);
	$active_sheet->setCellValue('E'.$schetchik_excel, $overdue);
	$active_sheet->setCellValue('F'.$schetchik_excel, $tasks_excel);
	if(!$rangedate) {
		$active_sheet->setCellValue('G'.$schetchik_excel, count($overdue_tasks_prev_month));
		$active_sheet->setCellValue('H'.$schetchik_excel, $prev_tasks);
	}
	$prev_overdue_source += count($overdue_tasks_prev_month);
	$count_tasks_source = $count_tasks_source + $count_tasks;
        $solved_source += $solved;
        $overdue_source += $overdue;
	$query_show_report = "UPDATE report_incidents SET `show`='1' WHERE id=".$idreportcell;
	usql($query_show_report);
 }
}
// Добавляем в самый конец строчку ИТОГО - последний классификатор
			$schetchik_excel++;
                        $active_sheet->getStyle('A'.$schetchik_excel.':H'.$schetchik_excel)->getFont()->setSize(13);

                        $active_sheet->getStyle('A'.$schetchik_excel.':H'.$schetchik_excel)->getFont()->setBold(true);
                        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setWrapText(true);

			$style_bottom = '';
			$style_bottom_color = '';
			if(!$rangedate) {
				$style_bottom = 'B'.$schetchik_excel.':H'.$schetchik_excel;
				$style_bottom_color = 'A'.$schetchik_excel.':H'.$schetchik_excel;
			}
			else {
				$style_bottom = 'B'.$schetchik_excel.':F'.$schetchik_excel;
				$style_bottom_color = 'A'.$schetchik_excel.':F'.$schetchik_excel;
			}

                        $active_sheet->getStyle($style_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $active_sheet->getStyle($style_bottom)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

                        $active_sheet->getStyle('D'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                        $active_sheet->getStyle('D'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setWrapText(true);

                        $active_sheet->getStyle($style_bottom_color)->applyFromArray(
                                                                            array(
                                                                                'fill' => array(
                                                                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                'color' => array('rgb' => $color)
                                                                                ),
                                                                                'borders' => array(
                                                                                    'allborders'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                                                                )
                                                                            )
                                                                        );

                        $active_sheet->setCellValue('A'.$schetchik_excel, 'Итого');
                        $active_sheet->setCellValue('B'.$schetchik_excel, $count_tasks_source);
			$active_sheet->setCellValue('C'.$schetchik_excel, ($count_tasks_source - $solved_source));
                        $active_sheet->setCellValue('D'.$schetchik_excel, $solved_source);
                        $active_sheet->setCellValue('E'.$schetchik_excel, $overdue_source);
			if(!$rangedate) {
				$active_sheet->setCellValue('G'.$schetchik_excel, $prev_overdue_source);
			}

//конец ИТОГО


$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$filename = 'report_tasks_gmu_'.date('d_m_y_H_i_s').'.xls';
if($post['rangedate']=='true') {
//	$objWriter->save('php://output');
	$objWriter->save('reports/'.$filename);
	echo json_encode('<p class="report_link">Отчет можно скачать по <a href="http://pm.citrt.net/plugins/report_incidents/reports/'.$filename.'">этой</a> ссылке!</p>');
}
else {
	$objWriter->save('reports/'.$filename);
	$ToID = array('n.ryabinin@tatar.ru', 'A.H@tatar.ru', 'Elena.Lukina@tatar.ru', 'Liliya.Shaihova@tatar.ru', 'Timur.Zaripov@tatar.ru', 'L.G@tatar.ru', 'Aygul.Komarova@tatar.ru');
//	$ToID = array('n.ryabinin@tatar.ru');
//	$ToID = null;
	$FromID = 'incidents.report@tatar.ru';
	$Subject = 'Ежемесячный отчет по задачам ГМУ';

	$monthMinus1 = $month -1;
	$starttime = '01.'.$monthMinus1.'.'.date("Y");
	$endtime = date("t", mktime(0, 0, 0, $monthMinus1, 1, date("Y"))).'.'.$monthMinus1.'.'.date("Y");

	$Body = '<p>Статистика сформирована по задачам из СУП с '.$starttime.' по '.$endtime .'</p><p>Отчет можно посмотреть <a href="http://pm.citrt.net/plugins/report_incidents/reports/'.$filename.'">здесь</a></p>';
	for($i=0; $i<count($ToID); $i++) {
        	opengoo_insert_queued_email_without_feng($ToID[$i], $FromID, $Subject, $Body);
	}
}

?>
