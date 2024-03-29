<?php
require_javascript('og/tasks/TasksTopToolbar.js');
require_javascript('og/CalendarToolbar.js');
require_javascript('og/CalendarFunctions.js');
require_javascript('og/EventPopUp.js');

/*
	
	Copyright (c) Reece Pegues
	sitetheory.com

    Reece PHP Calendar is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or 
	any later version if you wish.

    You should have received a copy of the GNU General Public License
    along with this file; if not, write to the Free Software
    Foundation Inc, 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	
*/

$year = isset($_GET['year']) ? $_GET['year'] : (isset($_SESSION['year']) ? $_SESSION['year'] : date('Y'));
$month = isset($_GET['month']) ? $_GET['month'] : (isset($_SESSION['month']) ? $_SESSION['month'] : date('n'));
$day = isset($_GET['day']) ? $_GET['day'] : (isset($_SESSION['day']) ? $_SESSION['day'] : date('j'));

if (!is_numeric($month)) $month = date('n');
if (!is_numeric($year)) $year = date('Y');

$_SESSION['year'] = $year;
$_SESSION['month'] = $month;
$_SESSION['day'] = $day;

$user_filter = $userPreferences['user_filter'];
$status_filter = $userPreferences['status_filter'];

$max_events_to_show = user_config_option('displayed events amount');
if (!$max_events_to_show) $max_events_to_show = 3;

$user = Users::findById(array('id' => $user_filter));
if ($user == null) $user = logged_user(); 

$use_24_hours = user_config_option('time_format_use_24');
if($use_24_hours) $timeformat = 'G:i';
else $timeformat = 'g:i A';

global $cal_db;
// get actual current day info
$today = DateTimeValueLib::now();
$today->add('h', logged_user()->getTimezone());
$currentday = $today->format("j");
$currentmonth = $today->format("n");
$currentyear = $today->format("Y");

if(user_config_option("start_monday")) $firstday = (date("w", mktime(0, 0, 0, $month, 1, $year)) - 1) % 7;
else $firstday = (date("w", mktime(0, 0, 0, $month, 1, $year))) % 7;
$lastday = date("t", mktime(0, 0, 0, $month, 1, $year));

$users_array = array();
$companies_array = array();
foreach($users as $u)
	$users_array[] = $u->getArrayInfo();
foreach($companies as $company)
	$companies_array[] = $company->getArrayInfo();
?>

<div id="calHiddenFields">
	<input type="hidden" id="hfCalUsers" value="<?php echo clean(str_replace('"',"'", str_replace("'", "\'", json_encode($users_array)))) ?>"/>
	<input type="hidden" id="hfCalCompanies" value="<?php echo clean(str_replace('"',"'", str_replace("'", "\'", json_encode($companies_array)))) ?>"/>
	<input type="hidden" id="hfCalUserPreferences" value="<?php echo clean(str_replace('"',"'", str_replace("'", "\'", json_encode($userPreferences)))) ?>"/>
</div>

<script>
	og.ev_cell_dates = [];
	og.events_selected = 0;
	og.eventSelected(0);
	var ev_dropzone = new Ext.dd.DropZone('calendar', {ddGroup:'ev_dropzone'});
</script>

<div id="cal_main_div" class="calendar" style="position:relative;width:100%;height:100%;overflow:hidden">
<div id="calendarPanelTopToolbar" class="x-panel-tbar" style="width:100%;height:28px;display:block;background-color:#F0F0F0;"></div>
<div id="calendarPanelSecondTopToolbar" class="x-panel-tbar" style="width:100%;height:28px;display:block;background-color:#F0F0F0;"></div>

<table style="width:100%;height:100%;">
<tr>
<td>
	<table style="width:100%;height:100%;">
		<tr>
			<td id="calendarMonthTitle" class="coViewHeader" colspan=1 rowspan=1 style="padding: 0;">
				<div class="coViewTitle" style="width:100%;padding: 8px 12px 8px;">
					<table style="width:100%"><tr><td>				
						<?php echo cal_month_name($month)." ". $year .' - '. ($user_filter == -1 ? lang('all users') : lang('calendar of', clean($user->getDisplayName())));?>
					</td><td style="width:100px;padding:0 24px 0 0;">
					<?php if (config_option("show_feed_links")) { ?>
						<?php echo checkbox_field("include_subws", true, array("id" => "include_subws", "style" => "float:right;", "onclick" => "javascript:og.change_link_incws('ical_link', 'include_subws')", "title" => lang('check to include sub ws'))) ?>
					 	<?php echo label_tag(lang('subws'), "include_subws", false, array("style" => "float:right;font-size:60%;margin:0px 3px;vertical-align:top;", "title" => lang('check to include sub ws')), "") ?>
					 	<?php 
					 		$export_name = active_project() != null ? clean(active_project()->getName()) : clean($user->getDisplayName());
					 		$export_ws = active_project() != null ? active_project()->getId() : 0;
					 	 ?>
					 	<a class="iCalSubscribe" id="ical_link" style="float:right;" href="<?php echo ROOT_URL ."/index.php?c=feed&a=ical_export&n=$export_name&cal=$export_ws&t=".$user->getToken()."&isw=1" ?>" 
					 		title="<?php echo lang('copy this url in your calendar client software')?>"
					 		onclick="Ext.Msg.show({
									   	title: '<?php echo escape_single_quotes(lang('import events from third party software')) ?>',
									   	msg: '<?php echo escape_single_quotes(lang('copy this url in your calendar client software')) ."<br><br><br>"?>'+document.getElementById('ical_link').href,
							   			icon: Ext.MessageBox.INFO }); return false;"
							></a>
					<?php } ?>
					 </td></tr></table>
				</div>
				<div>
					<table id="calendar" border='0' cellspacing='0' cellpadding='0' width="100%" height="20px">
						<tr>
						<?php if(user_config_option("show_week_numbers")) { ?>
							<th width="20"><?php echo lang('week short') ?></th>
						<?php } ?>
						<?php  if(!user_config_option("start_monday")) { ?>
							<th width='15%'><?php echo lang('sunday short') ?></th>
						<?php } ?>
						<th width="14%"><?php echo  lang('monday short') ?></th>
						<th width="14%"><?php echo  lang('tuesday short') ?></th>
						<th width="14%"><?php echo  lang('wednesday short') ?></th>
						<th width="14%"><?php echo  lang('thursday short') ?></th>
						<th width="14%"><?php echo  lang('friday short') ?></th>
						<th width="15%"><?php echo  lang('saturday short') ?></th>
						<?php if(user_config_option("start_monday")) { ?>
							<th width="15%"><?php echo lang('sunday short') ?></th>
						<?php } ?>
						<th id="ie_scrollbar_adjust_th" style="display:none;width:15px;padding:0px;margin:0px"></th>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td class="coViewBody" style="padding:0px;width:100%;height:100%;" colspan=1>
				<div id="gridcontainer" style="position:relative; overflow-x:hidden; overflow-y:scroll;padding-bottom:0px;width:100%;height:100%;">
				
				<table id="calendar" cellspacing='0' cellpadding='0' width="100%" height="100%" style="border: 1px solid #777;">
				
				<tr id="guide_row" style="display:none">
					<?php if(user_config_option("show_week_numbers")) { ?>
						<th width="20"></th>
					<?php } ?>
					<?php if(!user_config_option("start_monday")) { ?>
						<th width='15%'></th>
					<?php } ?>
					<th width="14%"></th>
					<th width="14%"></th>
					<th width="14%"></th>
					<th width="14%"></th>
					<th width="14%"></th>
					<th width="15%"></th>
					<?php if(user_config_option("start_monday")) { ?>
						<th width='15%'></th>
					<?php } ?>
					<th id="ie_scrollbar_adjust" style="display:none;width:15px;padding:0px;margin:0px;"></th>
				</tr>
					<?php
					$date_start = new DateTimeValue(mktime(0,0,0,$month-1,$firstday,$year)); 
					$date_end = new DateTimeValue(mktime(0,0,0,$month+1,$lastday,$year)); 
					$milestones = ProjectMilestones::getRangeMilestonesByUser($date_start, $date_end, ($user_filter != -1 ? $user : null), $tags, active_project());
					$tasks = ProjectTasks::getRangeTasksByUser($date_start, $date_end, ($user_filter != -1 ? $user : null), $tags, active_project());
					$birthdays = Contacts::instance()->getRangeContactsByBirthday($date_start, $date_end);
					
					$result = array();
					if($milestones) {
						$result = array_merge($result, $milestones );
					}
					if($tasks) {
						foreach ($tasks as $task) {
							$result = array_merge($result, replicateRepetitiveTaskForCalendar($task, $date_start, $date_end));
						}
					}
					if($birthdays) {
						$result = array_merge($result, $birthdays );
					}
					
					// Loop to render the calendar
					for ($week_index = 0;; $week_index++) {
						$month_aux = $month;
						$year_aux = $year;
						$day_of_month = $week_index * 7 + 2 - $firstday;
						$weeknumber = date("W", mktime(0, 0, 0, $month, $day_of_month, $year));
					?>
						<tr>
						<?php if(user_config_option("show_week_numbers")) { ?>
							<td style="width:20px" class="weeknumber" valign="top"><?php echo $weeknumber?></td>
						<?php } ?>
					<?php

						for ($day_of_week = 0; $day_of_week < 7; $day_of_week++) {
							$i = $week_index * 7 + $day_of_week;
							$day_of_month = $i - $firstday + 1;
							// if weekends override do this
							if( !user_config_option("start_monday") AND ($day_of_week == 0 OR $day_of_week == 6) ){
								$daytype = "weekend";
							}elseif( user_config_option("start_monday") AND ($day_of_week == 5 OR $day_of_week == 6) AND $day_of_month <= $lastday AND $day_of_month >= 1){
								$daytype = "weekend";
							}elseif($day_of_month <= $lastday AND $day_of_month >= 1){
								$daytype = "weekday";
							}else{
								$daytype = "weekday_future";
							}

							$date_tmp = DateTimeValueLib::make(0, 0, 0, $month_aux, $day_of_month, $year_aux);
							$extra_style = '';
							// see what type of day it is
							if($currentyear == $date_tmp->getYear() && $currentmonth == $date_tmp->getMonth() && $currentday == $date_tmp->getDay()){
								$daytitle = 'todaylink';
								$daytype = "today";
							} else if($year == $year_aux && $month == $month_aux && $day == $day_of_month){
								$daytitle = 'selecteddaylink';
								$daytype = "selectedday";
							} else if($day_of_month > $lastday OR $day_of_month < 1){
								if ($daytype == "weekend")
									$daytitle = 'extraweekendlink';
								else
									$daytitle = 'extralink';
								$extra_style = 'opacity:0.5; filter: alpha(opacity = 50);';
							} else {
								$daytitle = 'daylink';
							}
							// writes the cell info (color changes) and day of the month in the cell.
							
					?>
							<td valign="top" class="<?php echo $daytype?>" style="<?php echo ($extra_style != '' ? 'background-color:#EEE;border-color:#BBB;border-style: dotted;' : '')?>">
					<?php
						
							if($day_of_month <= $lastday AND $day_of_month >= 1){ 
								$p = get_url('event', 'viewdate', array(
									'day' => $day_of_month,
									'month' => $month_aux,
									'year' => $year_aux,
									'view_type' => 'viewdate'
								));
								$t = get_url('event', 'add', array(
									'day' => $day_of_month,
									'month' => $month_aux,
									'year' => $year_aux
								));
								$w = $day_of_month;
								$dtv = DateTimeValueLib::make(0, 0, 0, $month_aux, $day_of_month, $year_aux);
							}elseif($day_of_month < 1){
								$p = get_url('event', 'viewdate', array(
									'day' => $day_of_month,
									'month' => $month_aux,
									'year' => $year_aux,
									'view_type' => 'viewdate'
								));
								$t = get_url('event', 'add', array(
									'day' => $day_of_month,
									'month' => $month_aux,
									'year' => $year_aux
								));
								$ld = idate('d', mktime(0, 0, 0, $month_aux, 0, $year_aux));//date("t", strtotime("last month",mktime(0,0,0,$month-1,1,$year)));
								$w = $ld + $day_of_month ;
								$dtv = DateTimeValueLib::make(0, 0, 0, $month_aux, $day_of_month, $year_aux);  
								
							} else {
								if($day_of_month == $lastday + 1){
									$month_aux++;
									if($month_aux == 13){
										$month_aux = 1;
										$year_aux++;
									}
								}
								$p = get_url('event', 'viewdate', array(
									'day' => $day_of_month - $lastday,
									'month' => $month_aux,
									'year' => $year_aux,
									'view_type' => 'viewdate'
								));
								$t = get_url('event', 'add', array(
									'day' => $day_of_month - $lastday,
									'month' => $month_aux,
									'year' => $year_aux
								));
								$w = $day_of_month - $lastday;
								$dtv = DateTimeValueLib::make(0, 0, 0, $month_aux, $w, $year_aux);
							}
							$start_value = $dtv->format(user_config_option('date_format'));
														
					?>	
						 		<div id="m<?php echo $dtv->getMonth() ?>_d<?php echo $dtv->getDay() ?>" style='z-index:0; min-height:90px; height:100%; cursor:pointer;<?php echo $extra_style ?>'
						 		<?php if (!logged_user()->isGuest()) { ?>
						 		onclick="showMonthEventPopup('<?php echo $dtv->getDay() ?>','<?php echo $dtv->getMonth()?>','<?php echo $dtv->getYear()?>','<?php echo $start_value ?>');"
						 		<?php } ?>
						 		>
						 			<div class='<?php echo $daytitle?>' style='text-align:right;'>
							 		<a class='internalLink' href="<?php echo $p ?>" onclick="og.disableEventPropagation(event);return true;"  style='color:#5B5B5B' ><?php echo $w?></a>				
					<?php
							// only display this link if the user has permission to add an event
							if(!active_project() || ProjectEvent::canAdd(logged_user(), active_project())){
								// if single digit, add a zero
								$dom = $day_of_month;
								if($dom < 10) $dom = "0" . $dom;
								// make sure user is allowed to edit the past
									
							}
					?>			
							
									</div>
					<?php
							
							// This loop writes the events for the day in the cell
							if (is_numeric($w)){ //if it is a day after the first of the month
								
								$result_evs = ProjectEvents::getDayProjectEvents($dtv, $tags, active_project(), $user_filter, $status_filter); 
								if (!is_array($result_evs)) $result_evs = array();
								
								if(count($result) + count($result_evs) < 1) { ?> 
									&nbsp; 				
								<?php
								} else {
									$count = 0;
									foreach($result_evs as $event){
										if($event instanceof ProjectEvent ){
											$count++;
											$subject =  clean($event->getSubject());
											$typeofevent = $event->getTypeId(); 
											$private = $event->getIsPrivate(); 
											$eventid = $event->getId();
											
											getEventLimits($event, $dtv, $event_start, $event_duration, $end_modified);
											
											$real_start = new DateTimeValue($event->getStart()->getTimestamp() + 3600 * logged_user()->getTimezone());
											$real_duration = new DateTimeValue($event->getDuration()->getTimestamp() + 3600 * logged_user()->getTimezone());
											
											$pre_tf = $real_start->getDay() == $real_duration->getDay() ? '' : 'D j, ';
											if (!$event->isRepetitive() && $real_start->getDay() != $event_start->getDay()) $subject = "... $subject";
											
											$dws = $event->getWorkspaces();
											$ws_color = 0;											
											if (count($dws) >= 1) $ws_color = $dws[0]->getColor();
											cal_get_ws_color($ws_color, $ws_style, $ws_class, $txt_color, $border_color);
											
											$id_suffix = "_$w";
										
											// make the event subjects links or not according to the variable $whole_day in gatekeeper.php
											if(!$private && $count <= $max_events_to_show){
												$tip_text = str_replace("\r", '', clean($event->getDescription()));
												$tip_text = str_replace("\n", '<br>', $tip_text);													
												if (strlen_utf($tip_text) > 200) $tip_text = substr_utf($tip_text, 0, strpos($tip_text, ' ', 200)) . ' ...';
							
									$bold = "bold";
									if ($event instanceof Contact || $event->getIsRead(logged_user()->getId())){
										$bold = "normal";
									}

									$ev_tags = $event->getTags();
									$eventTagString = '';
									if (is_array($ev_tags) && count($ev_tags)>0){													
										$eventTagString .= '<span class="ico-tags ogTasksIcon" style="padding-left: 18px; padding-top: 4px; padding-bottom: 2px; font-size: 10px; margin-left: 10px;">';
										$c= 0;
										foreach ($ev_tags as $t){
											$eventTagString .= $t;
											$c++;
											count($ev_tags)!=$c? $eventTagString .= ',':$eventTagString .= '</span>';														
										}
									}
								?>

												<div id="m_ev_div_<?php echo $event->getId() . $id_suffix?>" class="<?php echo "og-wsname-color-$ws_color" ?>" style="margin: 1px;padding-left:1px;padding-bottom:0px;<?php echo $extra_style ?>">
												<div style="border: 1px solid;border-color:<?php echo $border_color ?>;">
													<table style="width:100%;" class="<?php echo "og-wsname-color-$ws_color" ?>"><tr><td>
													<a href='<?php echo get_url('event', 'viewevent', array('id' => $event->getId(), 'user_id' => $user_filter)); ?>' class='internalLink' onclick="og.disableEventPropagation(event); return true;" <?php echo "style='color:$txt_color;'" ?>>
														<img src="<?php echo image_url('/16x16/calendar.png')?>" style="vertical-align: middle;border-width: 0px;">
														<span style="font-weight: <?php echo $bold ?>"><?php echo (strlen_utf($subject) < 15 ? $subject : substr_utf($subject, 0, 14).'...') . $eventTagString ?></span>															
													</a>
													</td><td align="right">
														<div align="right" style="padding-right:1px;">
														<input type="checkbox" style="width:13px;height:13px;vertical-align:top;margin-top:2px;border-color: <?php echo $border_color ?>;" id="sel_<?php echo $event->getId()?>" name="obj_selector" onclick="og.eventSelected(this.checked);og.disableEventPropagation(event)"></input>
														<?php
														if ($user_filter != -1) { 
															$invitations = $event->getInvitations();
															if ($invitations != null && is_array($invitations) && isset($invitations[$user_filter])) {
																$inv = $invitations[$user_filter];
																
																if ($inv->getInvitationState() == 0) { // Not answered
																	echo '<img src="' . image_url('/16x16/mail_mark_unread.png') . '"/>';
																} else if ($inv->getInvitationState() == 1) { // Assist = Yes
																	echo '<img src="' . image_url('/16x16/complete.png') . '"/>';
																} else if ($inv->getInvitationState() == 2) { // Assist = No
																	echo '<img src="' . image_url('/16x16/del.png') . '"/>';
																} else if ($inv->getInvitationState() == 3) { // Assist = Maybe
																	echo '<img src="' . image_url('/16x16/help.png') . '"/>';
																} else {
																};
															} // if
														} // if ?>
														</div>
													</td></tr></table>
											 	</div>
											 	</div>
										 		<script>
										 			<?php
										 			$tipbody = ($event->getTypeId() == 2 ? lang('CAL_FULL_DAY') : format_date($real_start, $pre_tf.$timeformat, 0) .' - '. format_date($real_duration, $pre_tf.$timeformat, 0)) . ($tip_text != '' ? '<br><br>' . $tip_text : '');
										 			?>
													addTip('m_ev_div_<?php echo $event->getId() . $id_suffix ?>', '<i>' + lang('event') + '</i> - ' + <?php echo json_encode(clean($event->getSubject())) ?>, <?php echo json_encode($tipbody);?>);
													<?php $is_repetitive = $event->isRepetitive() ? 'true' : 'false'; ?>
													<?php if (!logged_user()->isGuest()) { ?>
													og.createMonthlyViewDrag('m_ev_div_<?php echo $event->getId() . $id_suffix ?>', '<?php echo $event->getId()?>', <?php echo $is_repetitive ?>, 'event', '<?php echo $event_start->format('Y-m-d H:i:s') ?>'); // Drag
													<?php } ?>
												</script>
											 	
								<?php
											}
								?>
												
								<?php
										}
									}
									foreach($result as $event){
										if($event instanceof ProjectMilestone ){
											$milestone=$event;
											$due_date=$milestone->getDueDate();
											$now = mktime(0, 0, 0, $dtv->getMonth(), $dtv->getDay(), $dtv->getYear());
											if ($now == mktime(0, 0, 0, $due_date->getMonth(), $due_date->getDay(), $due_date->getYear())) {	
												$count++;
												if ($count <= $max_events_to_show){
													$color = 'FFC0B3'; 
													$subject = "&nbsp;" . clean($milestone->getName())." - <i>Milestone</i>";
													$cal_text = clean($milestone->getName());
													
													$tip_text = str_replace("\r", '', lang('assigned to') .': '. clean($milestone->getAssignedToName()) . (trim(clean($milestone->getDescription())) == '' ? '' : '<br><br>'. clean($milestone->getDescription())));
													$tip_text = str_replace("\n", '<br>', $tip_text);													
													if (strlen_utf($tip_text) > 200) $tip_text = substr_utf($tip_text, 0, strpos($tip_text, ' ', 200)) . ' ...';
													
								?>
													<div id="m_ms_div_<?php echo $milestone->getId()?>" class="event_block" style="border-left-color: #<?php echo $color?>;">
														<a href='<?php echo $milestone->getViewUrl()?>' class="internalLink" onclick="og.disableEventPropagation(event);return true;" >
															<img src="<?php echo image_url('/16x16/milestone.png')?>" style="vertical-align: middle;border-width: 0px;">
															<span><?php echo $cal_text ?></span>
														</a>
													</div>
													<script>
														addTip('m_ms_div_<?php echo $milestone->getId() ?>', '<i>' + lang('milestone') + '</i> - ' + <?php echo json_encode(clean($milestone->getTitle())) ?>, <?php echo json_encode($tip_text != '' ? $tip_text : '');?>);
														<?php if (!logged_user()->isGuest()) { ?>
														og.createMonthlyViewDrag('m_ms_div_<?php echo $milestone->getId() ?>', '<?php echo $milestone->getId()?>', false, 'milestone'); // Drag
														<?php } ?>
													</script>
								<?php
												}//if count
								?>
												
								<?php
											}
											
										}//endif milestone
										elseif($event instanceof ProjectTask){
											$task = $event;
											$start_date = $task->getStartDate();
											$due_date = $task->getDueDate();
											$end_of_task = false;
											$start_of_task = false;
											if ($due_date instanceof DateTimeValue)
												if ($dtv->getTimestamp() == mktime(0,0,0, $due_date->getMonth(), $due_date->getDay(), $due_date->getYear())) $end_of_task = true;
											if ($start_date instanceof DateTimeValue)
												if ($dtv->getTimestamp() == mktime(0,0,0, $start_date->getMonth(), $start_date->getDay(), $start_date->getYear())) $start_of_task = true;
											if ($start_of_task || $end_of_task) {
												if ($start_of_task && $end_of_task) {
													$tip_title = lang('task');
													$img_url = image_url('/16x16/tasks.png');
													$tip_pre = '';
												} else if ($end_of_task) {
													$tip_title = lang('end of task');
													$img_url = image_url('/16x16/task_end.png');
													$tip_pre = 'end_';
												} else {
													$tip_title = lang('start of task');
													$img_url = image_url('/16x16/task_start.png');
													$tip_pre = 'st_';
												}
												$count++;
												if ($count <= $max_events_to_show){
													$color = 'B1BFAC'; 
													$subject = clean($task->getTitle()).'- <i>Task</i>';
													$cal_text = clean($task->getTitle());
													
													$tip_text = str_replace("\r", '', lang('assigned to') .': '. clean($task->getAssignedToName()) . (trim(clean($task->getText())) == '' ? '' : '<br><br>'. clean($task->getText())));
													$tip_text = str_replace("\n", '<br>', $tip_text);													
													if (strlen_utf($tip_text) > 200) $tip_text = substr_utf($tip_text, 0, strpos($tip_text, ' ', 200)) . ' ...';
								?>
								
													<div id="m_ta_div_<?php echo $tip_pre.$task->getId()?>" class="event_block" style="border-left-color: #<?php echo $color?>;">
														<a href='<?php echo $task->getViewUrl()?>' class='internalLink' onclick="og.disableEventPropagation(event);return true;"  style="border-width:0px">
															<img src="<?php echo $img_url ?>" style="vertical-align: middle;">
														 	<span><?php echo $cal_text ?></span>
														</a>
													</div>
													<script>
														addTip('m_ta_div_<?php echo $tip_pre.$task->getId() ?>', '<i>' + '<?php echo $tip_title ?>' + '</i> - ' + <?php echo json_encode(clean($task->getTitle()))?>, <?php echo json_encode(trim($tip_text) != '' ? trim($tip_text) : '');?>);
														<?php if (!logged_user()->isGuest()) { ?>
														og.createMonthlyViewDrag('m_ta_div_<?php echo $tip_pre.$task->getId() ?>', '<?php echo $task->getId()?>', false, 'task'); // Drag
														<?php } ?>
													</script>
								<?php
												}//if count
								?>
													
								<?php
											}
										}//endif task
										elseif($event instanceof Contact){
											$contact = $event;
											$bday = $contact->getOBirthday();
											$now = mktime(0, 0, 0, $dtv->getMonth(), $dtv->getDay(), $dtv->getYear());
											if ($now == mktime(0, 0, 0, $bday->getMonth(), $bday->getDay(), $dtv->getYear())) {	
												$count++;
												if ($count <= $max_events_to_show){
													$color = 'B1BFAC';
													$subject = clean($contact->getDisplayName()).' - <i>'.lang('birthday').'</i>';
								?>
													<div id="m_bd_div_<?php echo $contact->getId()?>" class="event_block" style="border-left-color: #<?php echo $color?>;">
														<a href='<?php echo $contact->getViewUrl()?>' class='internalLink' onclick="og.disableEventPropagation(event);return true;"  style="border-width:0px">
															<img src="<?php echo image_url('/16x16/contacts.png')?>" style="vertical-align: middle;">
														 	<span><?php echo $contact->getDisplayName() ?></span>
														</a>
													</div>
													<script>
														addTip('m_bd_div_<?php echo $contact->getId() ?>', '<i>' + '<?php echo escape_single_quotes(lang('birthday')) ?>' + '</i> - ' + <?php echo json_encode(clean($contact->getDisplayName()))?>, '');
													</script>
								<?php
												}//if count
								?>
													
								<?php
											}
										}
									} // end foreach event writing loop
									if ($count > $max_events_to_show) {
								?>
									
										<div style="witdh:100%;text-align:center;font-size:9px" ><a href="<?php echo $p?>" class="internalLink"  onclick="og.disableEventPropagation(event);return true;"><?php echo ($count-$max_events_to_show) . ' ' . lang('more');?> </a></div>
								<?php
									}
								}
								?>	
								</div>
								<script>
									div_id = 'm<?php echo $dtv->getMonth() ?>_d<?php echo $dtv->getDay() ?>';
									og.ev_cell_dates[og.ev_cell_dates.length] = {key:div_id, day:<?php echo $dtv->getDay() ?>, month:<?php echo $dtv->getMonth()?>, year:<?php echo $dtv->getYear()?>}
									var ev_dropzone = new Ext.dd.DropZone(div_id, {ddGroup:'ev_dropzone'});
								</script>
								</td>
								<?php
							} //if is_numeric($w) 
						} // end weekly loop
						?>
						<td id="ie_scrollbar_adjust" style="display:none;width:15px;padding:0px;margin:0px"></td>
						</tr>
						<?php
						// If it's the last day, we're done
						if($day_of_month >= $lastday) {
							break;
						}
					} // end main loop
					
				?>
				</table>
				</div>
			</td>
			</tr>
		</table>
	</td>
</tr></table>
</div>

<script>
	// Top Toolbar	
	ogCalendarUserPreferences = Ext.util.JSON.decode(document.getElementById('hfCalUserPreferences').value);
	var ogCalTT = new og.CalendarTopToolbar({
		renderTo:'calendarPanelTopToolbar'
	});
	var ogCalSecTT = new og.CalendarSecondTopToolbar({
		usersHfId:'hfCalUsers',
		companiesHfId:'hfCalCompanies',
		renderTo: 'calendarPanelSecondTopToolbar'
	});
	
	// Mantain the actual values after refresh by clicking Calendar tab.
	var dtv = new Date('<?php echo $month.'/'.$day.'/'.$year ?>');
	og.calToolbarDateMenu.picker.setValue(dtv);

	Ext.QuickTips.init();

	function showMonthEventPopup(day, month, year, st_val) {
		
		og.EventPopUp.show(null, {day: day,
								month: month,
								year: year,
								hour: 9,
								minute: 0,
								durationhour: 1,
								durationmin: 0,
								start_value: st_val,
								start_time: '9:00',
								type_id:2, 
								view:'month', 
								title: lang('add event'),
								time_format: '<?php echo $timeformat ?>',
								hide_calendar_toolbar: 1
								}, '');
	}

	if (Ext.isIE) document.getElementById('ie_scrollbar_adjust').style.display = 'block';
	
	function resizeGridContainer(e, id) {
		maindiv = document.getElementById('cal_main_div');
		if (maindiv == null) {
			og.removeDomEventHandler(window, 'resize', id);
		} else {
			var tbarsh = Ext.get('calendarPanelSecondTopToolbar').getHeight() + Ext.get('calendarPanelTopToolbar').getHeight();
			var cmt = document.getElementById('calendarMonthTitle');
			var mainHeight = maindiv.offsetHeight;
			
			var divHeight = maindiv.offsetHeight - tbarsh - cmt.offsetHeight;
			document.getElementById('gridcontainer').style.height = divHeight + 'px';

			if (Ext.isGecko) {			
				childnodes = document.getElementById('gridcontainer').childNodes;
				for (i=0; i<childnodes.length; i++) {
					if (childnodes[i].id == 'calendar') {
						h = childnodes[i].offsetHeight;
						childnodes[i].style.height = (h-2)+'px';
					}
				}
			}
		}
	}
	resizeGridContainer();
	if (Ext.isIE) {
		og.addDomEventHandler(document.getElementById('cal_main_div'), 'resize', resizeGridContainer);
	} else {
		og.addDomEventHandler(window, 'resize', resizeGridContainer);
	}
</script>