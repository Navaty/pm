<?php

function getEventLimits($event, $date, &$event_start, &$event_duration, &$end_modified) {
	$end_modified = false;
	$event_start = new DateTimeValue($event->getStart()->getTimestamp() + 3600 * logged_user()->getTimezone());
	$event_duration = new DateTimeValue($event->getDuration()->getTimestamp() + 3600 * logged_user()->getTimezone());
	
	$tomorrow = new DateTimeValue($date->getTimestamp());
	$tomorrow->add('d', 1);
	if ($event_duration->getTimestamp() > $tomorrow->getTimestamp()) {
		$event_duration = new DateTimeValue($tomorrow->getTimestamp());
		$end_modified = true;
	}
	if ($event_start->getTimestamp() < $date->getTimestamp()) {
		if (!$event->isRepetitive())
			$event_start = new DateTimeValue($date->getTimestamp());
		else {
			$event_start->setDay($date->getDay());
			$event_start->setMonth($date->getMonth());
			$event_start->setYear($date->getYear());
		}
	}
}

function cal_get_ws_color($ws_color, &$ws_style, &$ws_class, &$txt_color, &$border_color){
	$txt_color = '#fff';
	if ($ws_color>0 && $ws_color<=12){
		$ws_style = "";
		$ws_class = "og-wsname-color-$ws_color";
	} else if ($ws_color>12) {
		$ws_style = "";
		$txt_color = '#003562';
		$ws_class = "og-wsname-color-$ws_color";
	}else {
		$ws_style = "color: #000000;background-color: #C5C7C1;border-color: #C5C7C1;";
		$txt_color = '#000000';
		$ws_class = "";	
	}
	
	if ($ws_color == 0) $border_color = '#AAAAAA';
	else {
		switch ($ws_color % 12) {
			case 1: $border_color = '#5A6986'; break;
			case 2: $border_color = '#206CE1'; break;
			case 3: $border_color = '#0000CC'; break;
			case 4: $border_color = '#5229A3'; break;
			case 5: $border_color = '#854F61'; break;
			case 6: $border_color = '#CC0000'; break;
			case 7: $border_color = '#EC7000'; break;
			case 8: $border_color = '#B36D00'; break;
			case 9: $border_color = '#AB8B00'; break;
			case 10: $border_color = '#636330'; break;
			case 11: $border_color = '#64992C'; break;
			case 0: $border_color = '#006633'; break;
			default: $border_color = '#333333'; break;
		}
		if ($ws_color <= 12) $border_color = darkerHtmlColor($border_color, 25);
	}
	
}

function cal_month_name($month){
	$month = ($month - 1) % 12 + 1;
	switch($month) {
		case 1:  return lang('CAL_JANUARY');
		case 2:  return lang('CAL_FEBRUARY');
		case 3:  return lang('CAL_MARCH');
		case 4:  return lang('CAL_APRIL');
		case 5:  return lang('CAL_MAY');
		case 6:  return lang('CAL_JUNE');
		case 7:  return lang('CAL_JULY');
		case 8:  return lang('CAL_AUGUST');
		case 9:  return lang('CAL_SEPTEMBER');
		case 10: return lang('CAL_OCTOBER');
		case 11: return lang('CAL_NOVEMBER');
		case 12: return lang('CAL_DECEMBER');
	}
}

function cal_month_short($month) {
	$month = ($month - 1) % 12 + 1;
	switch($month) {
		case 1:  return utf8_substr(lang('CAL_JANUARY'),0,3);
		case 2:  return utf8_substr(lang('CAL_FEBRUARY'),0,3);
		case 3:  return utf8_substr(lang('CAL_MARCH'),0,3);
		case 4:  return utf8_substr(lang('CAL_APRIL'),0,3);
		case 5:  return utf8_substr(lang('CAL_MAY'),0,3);
		case 6:  return utf8_substr(lang('CAL_JUNE'),0,3);
		case 7:  return utf8_substr(lang('CAL_JULY'),0,3);
		case 8:  return utf8_substr(lang('CAL_AUGUST'),0,3);
		case 9:  return utf8_substr(lang('CAL_SEPTEMBER'),0,3);
		case 10: return utf8_substr(lang('CAL_OCTOBER'),0,3);
		case 11: return utf8_substr(lang('CAL_NOVEMBER'),0,3);
		case 12: return utf8_substr(lang('CAL_DECEMBER'),0,3);
	}
}

function forwardRepDate(ProjectTask $task, $min_date) {
	if ($task->isRepetitive()) {
		if (($task->getRepeatBy() == 'start_date' && !$task->getStartDate() instanceof DateTimeValue) ||
			($task->getRepeatBy() == 'due_date' && !$task->getDueDate() instanceof DateTimeValue) ||
			$task->getRepeatBy() != 'due_date' && $task->getRepeatBy() != 'start_date' ||
			!$min_date instanceof DateTimeValue) {
				return array('date' => $min_date, 'count' => 0); //This should not happen...
		}
		$date = new DateTimeValue($task->getRepeatBy() == 'start_date' ? $task->getStartDate()->getTimestamp() : $task->getDueDate()->getTimestamp());
		$count = 0;
		if($date->getTimestamp() >= $min_date->getTimestamp()) {
			return array('date' => $date, 'count' => $count);
		}
		
		while ($date->getTimestamp() < $min_date->getTimestamp()) {
			if ($task->getRepeatD() > 0) { 
				$date = $date->add('d', $task->getRepeatD());
			} else if ($task->getRepeatM() > 0) { 
				$date = $date->add('M', $task->getRepeatM());
			} else if ($task->getRepeatY() > 0) { 
				$date = $date->add('y', $task->getRepeatY());
			}
			$count++;
		}
		return array('date' => $date, 'count' => $count);
	} else return array('date' => $min_date, 'count' => 0);
}

function replicateRepetitiveTaskForCalendar(ProjectTask $task, $from_date, $to_date) {
	$new_task_array = array($task);
	
	if ($task->isRepetitive()) {
		$res = forwardRepDate($task, $from_date);
		$ref_date = $res['date'];
		$top_repeat_num = $task->getRepeatNum() - $res['count'];

		$last_repeat = $task->getRepeatEnd() instanceof DateTimeValue ? new DateTimeValue($task->getRepeatEnd()->getTimestamp()) : null;
		if (($task->getRepeatNum() > 0 && $top_repeat_num <= 0) || ($last_repeat instanceof DateTimeValue && $last_repeat->getTimestamp() < $ref_date->getTimestamp())) {
			return array();
		}
		
		$num_repetitions = 0;
		while ($ref_date->getTimestamp() < $to_date->getTimestamp()) {
			if ($task->getRepeatBy() == 'start_date' && !($task->getStartDate() instanceof DateTimeValue)) return $new_task_array;
			if ($task->getRepeatBy() == 'due_date' && !($task->getDueDate() instanceof DateTimeValue)) return $new_task_array;
			
			//$ref_date = new DateTimeValue( $task->getRepeatBy() == 'start_date' ? $task->getStartDate()->getTimestamp() : $task->getDueDate()->getTimestamp() );
			if ($task->getRepeatBy() == 'start_date') $task->setStartDate(new DateTimeValue($ref_date->getTimestamp()));
			else if ($task->getRepeatBy() == 'due_date') $task->setDueDate(new DateTimeValue($ref_date->getTimestamp()));
			
			$info = array(
				'title' => $task->getTitle(),
				'text' => $task->getText(),
				'due_date' => $task->getDueDate() instanceof DateTimeValue ? new DateTimeValue($task->getDueDate()->getTimestamp()) : null,
				'start_date' => $task->getStartDate() instanceof DateTimeValue ? new DateTimeValue($task->getStartDate()->getTimestamp()) : null,
				'assigned_to_company_id' => $task->getAssignedToCompanyId(),
				'assigned_to_user_id' => $task->getAssignedToUserId(),
				'priority' => $task->getPriority(),
				'state' => $task->getState(),
				'milestone_id' => $task->getMilestoneId(),
				'repeat_by' => $task->getRepeatBy(),
				'repeat_d' => $task->getRepeatD(),
				'repeat_m' => $task->getRepeatM(),
				'repeat_y' => $task->getRepeatY(),
			);
			$new_task = new ProjectTask();
			$new_task->setFromAttributes($info);
			$new_task->setId($task->getId());
			$new_task->setNew(false);

			$new_due_date = null;
			$new_st_date = null;
			if ($task->getStartDate() instanceof DateTimeValue ) {
				$new_st_date = new DateTimeValue($task->getStartDate()->getTimestamp());
			} 
			if ($task->getDueDate() instanceof DateTimeValue ) {
				$new_due_date = new DateTimeValue($task->getDueDate()->getTimestamp());
			}
			if ($task->getRepeatD() > 0) { 
				if ($new_st_date instanceof DateTimeValue)
					$new_st_date = $new_st_date->add('d', $task->getRepeatD());
				if ($new_due_date instanceof DateTimeValue)
					$new_due_date = $new_due_date->add('d', $task->getRepeatD());
				$ref_date->add('d', $task->getRepeatD());
			}
			else if ($task->getRepeatM() > 0) {
				if ($new_st_date instanceof DateTimeValue)
					$new_st_date = $new_st_date->add('M', $task->getRepeatM());
				if ($new_due_date instanceof DateTimeValue)
					$new_due_date = $new_due_date->add('M', $task->getRepeatM());
				$ref_date->add('M', $task->getRepeatM());
			}
			else if ($task->getRepeatY() > 0) {
				if ($new_st_date instanceof DateTimeValue)
					$new_st_date = $new_st_date->add('y', $task->getRepeatY());
				if ($new_due_date instanceof DateTimeValue)
					$new_due_date = $new_due_date->add('y', $task->getRepeatY());
				$ref_date->add('y', $task->getRepeatY());
			}
			if ($new_st_date instanceof DateTimeValue) $new_task->setStartDate($new_st_date);
			if ($new_due_date instanceof DateTimeValue) $new_task->setDueDate($new_due_date);
			
			$num_repetitions++;
			if ($top_repeat_num > 0 && $top_repeat_num == $num_repetitions) break;
			if ($last_repeat instanceof DateTimeValue && $last_repeat->getTimestamp() < $ref_date->getTimestamp()) break;

			$new_task_array[] = $new_task;
			$task = $new_task;
		}
	}
	return $new_task_array;
}
?>