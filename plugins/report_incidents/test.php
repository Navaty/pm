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
echo $info
