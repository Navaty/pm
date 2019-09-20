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


if(!logged_user()->isCompanyAdmin(owner_company())) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		} // if
		$tool = AdministrationTools::getByName('test_mail_settings');
		if(!($tool instanceof AdministrationTool)) {
			flash_error(lang('administration tool dnx', 'test_mail_settings'));
			$this->redirectTo('administration', 'tools');
		} // if

		$test_mail_data = array_var($_POST, 'test_mail');

		tpl_assign('tool', $tool);
		tpl_assign('test_mail_data', $test_mail_data);

		if(is_array($test_mail_data)) {
			try {
				$recepient = trim(array_var($test_mail_data, 'recepient'));
				$message = trim(array_var($test_mail_data, 'message'));

				$errors = array();

				if($recepient == '') {
					$errors[] = lang('test mail recipient required');
				} else {
					if(!is_valid_email($recepient)) {
						$errors[] = lang('test mail recipient invalid format');
					} // if
				} // if

				if($message == '') {
					$errors[] = lang('test mail message required');
				} // if

				if(count($errors)) {
					throw new FormSubmissionErrors($errors);
				} // if
				$to = array($recepient);
				$success = Notifier::sendEmail($to, logged_user()->getEmail(), lang('test mail message subject'), $message);
				if($success) {
					flash_success(lang('success test mail settings'));
				} else {
					flash_error(lang('error test mail settings'));
				} // if
				ajx_current("back");
			} catch(Exception $e) {
				flash_error($e->getMessage());
				ajx_current("empty");
			} // try
		} // if
	} // tool_test_email
