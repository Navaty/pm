<?php
include_once "statusage.php"; //by almaz - usage control
set_time_limit(0);

include ("connect_db_func.php");
require_once 'phpexcel/Classes/PHPExcel.php';

$title = "Отчет по задачам ГМУ за ".date("M", mktime(0, 0, 0, (date("m")-1), 1, date("Y")));

$counter = 0;
$count_source = 0;
$day = date("d");
$month = date("m");
$year = date("Y");
$hour = date("H");
$minute = date("i");
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

$active_sheet->getStyle('A1')->getFont()->setBold(true);
$active_sheet->getStyle('B1')->getFont()->setBold(true);
$active_sheet->getStyle('C1')->getFont()->setBold(true);
$active_sheet->getStyle('D1')->getFont()->setBold(true);
$active_sheet->getStyle('E1')->getFont()->setBold(true);

$active_sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$active_sheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$active_sheet->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$active_sheet->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$active_sheet->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$active_sheet->getColumnDimension('A')->setWidth(30);
$active_sheet->getColumnDimension('B')->setWidth(20);
$active_sheet->getColumnDimension('C')->setWidth(20);
$active_sheet->getColumnDimension('D')->setWidth(20);
$active_sheet->getColumnDimension('E')->setWidth(55);

$active_sheet->setCellValue('A1', 'Источник');
$active_sheet->setCellValue('B1', 'Зафиксировано');
$active_sheet->setCellValue('C1', 'Решено');
$active_sheet->setCellValue('D1', 'Наружен срок');
$active_sheet->setCellValue('E1', 'Задачи с нарушенным сроком');

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
//echo '<pre>';
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

foreach($res as $item) {

	$counter++;
	$schetchik_excel = $counter+1;

	$active_sheet->getRowDimension($schetchik_excel)->setRowHeight(-1);

        $classifier = $item["classifier"];
        $count_tasks = $item["count_tasks"];
        $solved = $item["solved"];
        $overdue = $item["overdue"];
        $overdue_tasks = json_decode($item["overdue_tasks"], true);
        $tasks_excel = '';
        if(is_array($overdue_tasks)) {
                foreach($overdue_tasks as $item_task) {
                        $tasks_excel .= 'http://pm.citrt.net/index.php?c=task&a=view_task&id='.$item_task.PHP_EOL;
                }
        }
        if($source != $item["source"]) {
                $source = $item["source"];
                $count_source++;
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
				$color_cell = '4C6F74';
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
		$active_sheet->getStyle('A'.$schetchik_excel)->applyFromArray(
									    array(
									        'fill' => array(
									        'type' => PHPExcel_Style_Fill::FILL_SOLID,
								                'color' => array('rgb' => $color)
									        )
									    )
									);
		$active_sheet->mergeCells('A'.$schetchik_excel.':E'.$schetchik_excel);
		$active_sheet->setCellValue('A'.$schetchik_excel, $source);
		$counter++;
		$schetchik_excel = $counter+1;

	}

	$active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
	$active_sheet->getStyle('A'.$schetchik_excel)->getAlignment()->setWrapText(true);

	$active_sheet->getStyle('B'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$active_sheet->getStyle('B'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

	$active_sheet->getStyle('C'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$active_sheet->getStyle('C'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

	$active_sheet->getStyle('D'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
	$active_sheet->getStyle('D'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$active_sheet->getStyle('E'.$schetchik_excel)->getAlignment()->setWrapText(true);

	$active_sheet->getStyle('A'.$schetchik_excel.':E'.$schetchik_excel)->applyFromArray(
                                                                            array(
                                                                                'fill' => array(
                                                                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                'color' => array('rgb' => $color_cell)
                                                                                )
                                                                            )
                                                                        );




	$active_sheet->setCellValue('A'.$schetchik_excel, $classifier);
	$active_sheet->setCellValue('B'.$schetchik_excel, $count_tasks);
	$active_sheet->setCellValue('C'.$schetchik_excel, $solved);
	$active_sheet->setCellValue('D'.$schetchik_excel, $overdue);
	$active_sheet->setCellValue('E'.$schetchik_excel, $tasks_excel);
}
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$filename = 'report_tasks_gmu_'.date('d_m_y_H_i_s').'.xls';
$objWriter->save('report_incidents/reports/'.$filename);
//$objWriter->save('php://output');

?>
