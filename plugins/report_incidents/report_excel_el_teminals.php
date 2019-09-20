<?php
include ("take_tasks_from_base.php");
require_once 'phpexcel/Classes/PHPExcel.php';
$post = $_REQUEST;
//echo '<pre>';
$project_task_id = 4907;
$rangedate = false;
if(isset($post["rangedate"])) {
	$rangedate = true;
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
}
$info = give_me_report_terminal($starttime, $endtime, $project_task_id);
$current_time = time();

$count_type_error = 0;
$counter = 0;
$title = "Отчет";
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

$active_sheet->getStyle('A1:G1')->getFont()->setBold(true);
$active_sheet->getStyle('A1:G1')->getFont()->setSize(13);
$active_sheet->getStyle('A1:G1')->applyFromArray(
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
$active_sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$active_sheet->getColumnDimension('A')->setWidth(30);
$active_sheet->getColumnDimension('B')->setWidth(20);
$active_sheet->getColumnDimension('C')->setWidth(20);
$active_sheet->getColumnDimension('D')->setWidth(20);
$active_sheet->getColumnDimension('E')->setWidth(55);
$active_sheet->getColumnDimension('F')->setWidth(55);
$active_sheet->getColumnDimension('G')->setWidth(55);

$active_sheet->setCellValue('A1', 'Тип проблемы');
$active_sheet->setCellValue('B1', 'Зафиксировано');
$active_sheet->setCellValue('C1', 'Открыто');
$active_sheet->setCellValue('D1', 'Закрыто');
$active_sheet->setCellValue('E1', 'Нарушен срок');
$active_sheet->setCellValue('F1', 'Задачи с нарушенным сроком');
$active_sheet->setCellValue('G1', 'По чьей вине нарушен срок');

$class_error_name = '';
$type_error_array = Array();
//$count_overdue_all = Array();

foreach($info as $inf) {
	/* Количество открытых задач*/
	$count_open_tasks = 0;
	$counter++;
	$schetchik_excel = $counter+1;
	$active_sheet->getRowDimension($schetchik_excel)->setRowHeight(-1);
	$tasks_excel = '';
	$overdue_company = '';
	$count_overdue_type_problem = Array();
	$test_array = Array();
	$test_array[] = $inf;
	$current_type_problem = give_me_overdue_time_from_all_info($test_array, $current_time);
	if(is_array($inf["overdue_tasks"])) {
                foreach($inf["overdue_tasks"] as $item_task) {
			$overdue_company .= PHP_EOL.$item_task.PHP_EOL;
                        $tasks_excel .= 'http://pm.citrt.net/index.php?c=task&a=view_task&id='.$item_task.PHP_EOL;
			$timework = get_info_about_task($item_task, $current_time);
			foreach($timework["companiesworktime"] as $item_worktime) {
//				echo 'Taskid = '.$item_task.' name org= '.$item_worktime["name"].' time work ='.$item_worktime["time"].'<br />';
				if($item_worktime["time"] > 172800) {
					$tasks_excel .= $item_worktime["name"].' просрочил '.round( (($item_worktime["time"] - 172800)/(60*60)), 2, PHP_ROUND_HALF_UP).' ч.'.PHP_EOL;
				}
			}
                }
        }

	if($class_error_name != $inf["class_error_name"]) {
		$count_type_error++;
		$class_error_name = $inf["class_error_name"];
//		if($class_error_name!='') {
		if($count_type_error>1) {
//			$type_error_array_inf = give_me_overdue_time_from_all_info($type_error_array, $current_time);
			$overdue_companies_class_error = '';
//			foreach($type_error_array_inf["information"] as $info_item) {
//                        	if(!empty($info_item))
//                                	$overdue_companies_class_error .= $info_item["name"].' просрочил '.ceil(($info_item["time"]/(60*60*24))).' дн.'.PHP_EOL;
//	                }
			$type_error_array = Array();

			$active_sheet->getStyle('A'.$schetchik_excel.':G'.$schetchik_excel)->getFont()->setSize(13);
			$active_sheet->getStyle('A'.$schetchik_excel.':G'.$schetchik_excel)->getFont()->setBold(true);
                	$active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	                $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        	        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setWrapText(true);
                	$active_sheet->getStyle('B'.$schetchik_excel.':E'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	                $active_sheet->getStyle('B'.$schetchik_excel.':E'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                	$active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	                $active_sheet->getStyle('F'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        	        $active_sheet->getStyle('F'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                	$active_sheet->getStyle('F'.$schetchik_excel)->getAlignment()->setWrapText(true);

	                $active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        	        $active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                	$active_sheet->getStyle('H'.$schetchik_excel)->getAlignment()->setWrapText(true);

			$active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setWrapText(true);


			$itogo_style = 'A'.$schetchik_excel.':G'.$schetchik_excel;
//			$color="ffffff";
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

/*			$active_sheet->setCellValue('A'.$schetchik_excel, 'Итого');
			$active_sheet->setCellValue('B'.$schetchik_excel, ($type_error_array_inf["open"]+$type_error_array_inf["close"]));
			$active_sheet->setCellValue('C'.$schetchik_excel, $type_error_array_inf["open"]);
			$active_sheet->setCellValue('D'.$schetchik_excel, $type_error_array_inf["close"]);
			$active_sheet->setCellValue('E'.$schetchik_excel, $type_error_array_inf["overdue"]);
			$active_sheet->setCellValue('G'.$schetchik_excel, $overdue_companies_class_error);
			$counter++;
			$schetchik_excel = $counter+1;*/

		}

		$active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                $active_sheet->getStyle('A'.$schetchik_excel)->getFont()->setBold(true);
                $active_sheet->getStyle('A'.$schetchik_excel)->getFont()->setSize(13);

                switch($count_type_error){
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

		$style_header = 'A'.$schetchik_excel.':G'.$schetchik_excel;
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
		$active_sheet->setCellValue('A'.$schetchik_excel, $class_error_name);
		$counter++;
                $schetchik_excel = $counter+1;
	}
	$type_error_array[] = $inf;

        $active_sheet->getStyle('A'.$schetchik_excel.':G'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $active_sheet->getStyle('A'.$schetchik_excel.':G'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$active_sheet->getStyle('A'.$schetchik_excel.':G'.$schetchik_excel)->getAlignment()->setWrapText(true);
/*        $active_sheet->getStyle('B'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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
                                                $active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);*/

	$active_sheet->getStyle('A'.$schetchik_excel.':G'.$schetchik_excel)->applyFromArray(
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


	$active_sheet->setCellValue('A'.$schetchik_excel, $inf["class_error_name"]);
	$active_sheet->setCellValue('B'.$schetchik_excel, ($inf["close"] + $inf["open"]));
	$active_sheet->setCellValue('C'.$schetchik_excel, $inf["open"]);
	$active_sheet->setCellValue('D'.$schetchik_excel, $inf["close"]);
	$active_sheet->setCellValue('E'.$schetchik_excel, $inf["overdue_count"]);
	$active_sheet->setCellValue('F'.$schetchik_excel, $tasks_excel);
	$overdue_companies = '';
//	if(!empty($current_type_problem["information"])) {
		foreach($current_type_problem["information"] as $info_item) {
			if(!empty($info_item))
				$overdue_companies .= $info_item["name"].' просрочил '.ceil(($info_item["time"]/(60*60*24))).' дн.'.PHP_EOL;
		}
/*	}
	else {
		$overdue_companies .='Тест';
	}*/
	$active_sheet->setCellValue('G'.$schetchik_excel, $overdue_companies);
}

// Добавляем в самый конец строчку ИТОГО - последний тип ошибок
/*	$schetchik_excel++;
	$active_sheet->getStyle('A'.$schetchik_excel.':G'.$schetchik_excel)->getFont()->setSize(13);

        $active_sheet->getStyle('A'.$schetchik_excel.':G'.$schetchik_excel)->getFont()->setBold(true);
        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setWrapText(true);

	$style_bottom = 'B'.$schetchik_excel.':G'.$schetchik_excel;
        $style_bottom_color = 'A'.$schetchik_excel.':G'.$schetchik_excel;

	$active_sheet->getStyle($style_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $active_sheet->getStyle($style_bottom)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $active_sheet->getStyle('D'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('D'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setWrapText(true);

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

//	$type_error_array_inf = give_me_overdue_time_from_all_info($type_error_array, $current_time);
        $overdue_companies_class_error = '';
/*        foreach($type_error_array_inf["information"] as $info_item) {
        	if(!empty($info_item))
                	$overdue_companies_class_error .= $info_item["name"].' просрочил '.ceil(($info_item["time"]/(60*60*24))).' дн.'.PHP_EOL;
                }*/

/*	$active_sheet->setCellValue('A'.$schetchik_excel, 'Итого');
	$active_sheet->setCellValue('B'.$schetchik_excel, ($type_error_array_inf["open"]+$type_error_array_inf["close"]));
        $active_sheet->setCellValue('C'.$schetchik_excel, $type_error_array_inf["open"]);
        $active_sheet->setCellValue('D'.$schetchik_excel, $type_error_array_inf["close"]);
        $active_sheet->setCellValue('E'.$schetchik_excel, $type_error_array_inf["overdue"]);
        $active_sheet->setCellValue('G'.$schetchik_excel, $overdue_companies_class_error);*/

// Добавляем в самый конец строчку ИТОГО, который считает по всем задачм просрочку
        $schetchik_excel++;
        $active_sheet->getStyle('A'.$schetchik_excel.':G'.$schetchik_excel)->getFont()->setSize(15);

        $active_sheet->getStyle('A'.$schetchik_excel.':G'.$schetchik_excel)->getFont()->setBold(true);
        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setWrapText(true);

        $style_bottom = 'B'.$schetchik_excel.':G'.$schetchik_excel;
        $style_bottom_color = 'A'.$schetchik_excel.':G'.$schetchik_excel;

        $active_sheet->getStyle($style_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $active_sheet->getStyle($style_bottom)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $active_sheet->getStyle('D'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('D'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $active_sheet->getStyle('G'.$schetchik_excel)->getAlignment()->setWrapText(true);

        $active_sheet->getStyle($style_bottom_color)->applyFromArray(
                                                                            array(
                                                                                'fill' => array(
                                                                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                'color' => array('rgb' => 'ffffff')
                                                                                ),
                                                                                'borders' => array(
                                                                                    'allborders'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                                                                )
                                                                            )
                                                                        );

	$all_statistics = give_me_overdue_time_from_all_info($info, $current_time);
        $overdue_companies_class_error = '';
        foreach($all_statistics["information"] as $info_item) {
                if(!empty($info_item))
                        $overdue_companies_class_error .= $info_item["name"].' просрочил '.ceil(($info_item["time"]/(60*60*24))).' дн.'.PHP_EOL;
                }

        $active_sheet->setCellValue('A'.$schetchik_excel, 'Итого по всем задачам');
        $active_sheet->setCellValue('B'.$schetchik_excel, ($all_statistics["open"]+$all_statistics["close"]));
        $active_sheet->setCellValue('C'.$schetchik_excel, $all_statistics["open"]);
        $active_sheet->setCellValue('D'.$schetchik_excel, $all_statistics["close"]);
        $active_sheet->setCellValue('E'.$schetchik_excel, $all_statistics["overdue"]);
        $active_sheet->setCellValue('G'.$schetchik_excel, $overdue_companies_class_error);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$filename = 'report_overdue_tasks_el_queue_'.date('d_m_y_H_i_s').'.xls';
$objWriter->save('autodetectexpiredtasks/'.$filename);
if($rangedate) {
	echo json_encode('<p class="report_link">Отчет можно скачать по <a href="http://pm.citrt.net/plugins/report_incidents/autodetectexpiredtasks/'.$filename.'">этой</a> ссылке!</p>');
}
else {
	$ToID = array('ruslan.m@tatar.ru', 'A.H@tatar.ru', 'Elena.Lukina@tatar.ru', 'Liliya.Shaihova@tatar.ru', 'Timur.Zaripov@tatar.ru', 'L.G@tatar.ru', 'Aygul.Komarova@tatar.ru');
//	$ToID = array('n.ryabinin@tatar.ru');
	$FromID = 'terminal@tatar.ru';
	$Subject = 'Ежемесячный отчет по задачам "Терминалы эл. очереди - обращения админов"';
	$Body = '<p>Статистика сформирована по задачам из СУП с '.$starttime.' по '.$endtime .'</p><p>Отчет можно посмотреть <a href="http://pm.citrt.net/plugins/report_incidents/autodetectexpiredtasks/'.$filename.'">здесь</a></p>';
        for($i=0; $i<count($ToID); $i++) {
                opengoo_insert_queued_email_without_feng($ToID[$i], $FromID, $Subject, $Body);
        }
}
//$objWriter->save('php://output');
?>
