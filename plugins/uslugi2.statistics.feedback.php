<?
include_once "statusage.php"; //by almaz - usage control

$serviceid = 2040;
$tasks = opengoo_list_objects_by_projectid($serviceid,"task");
$header = "<h2>Статистика обращений по капитальному ремонту</h2>";
$appeal_no = "<b>Обращение №42423</b><br/>";
$appeal = "Премьер-министр Владимир Путин впервые вступился за бывшего министра финансов Алексея Кудрина. По словам главы правительства, Кудрин продолжит \"работать в команде\". Алексей Кудрин был отправлен в отставку после заявлений о том, что он не согласен с некоторыми пунктами бюджетной политики Дмитрия Медведева.";
$appeals = "<p>
	      $appeal_no
	      $appeal
	    </p>";

echo "<pre>";
foreach($tasks as $k=>$v) {
  $object_id =  $v[object_id];
  //echo ":";
  $taskinfo = opengoo_get_task_info($v[object_id]);
  $district = ""; $settlement = ""; $street = ""; $home = ""; $part = "";

  if(is_array($taskinfo["properties"])) {
    foreach($taskinfo["properties"] as $pk=>$pv) {
      $fname = $pv["name"];
      $fvalue = mb_strtoupper($pv["value"],'UTF-8');
      if($fname=="_Район") {
	$district = $fvalue;
      }
      if($fname=="_Населенный пункт") {
	$settlement = $fvalue;
      }
      if($fname=="_Улица") {
	$street = $fvalue;
      }
      if($fname=="_Дом") {
	$home = $fvalue;
      }
      if($fname=="_Корпус") {
	$part = $fvalue;
	$home = $home ." (Корпус ".$part.")";
      }
      //  echo "\n";
    }
  }
      if($district && $settlement && $street && $home) {
	$stt[$district][$settlement][$street][$home][$object_id]["taskinfo"] = $taskinfo[info][text];
	$stt[$district][$settlement][$street][$home][$object_id]["taskdate"] = $taskinfo[info][assigned_on];
      }

  //  echo "\n\n";
}
//print_r($stt);
echo "</pre>";
ksort($stt);
?>

<script>
$(function() {
    $( ".level0" ).accordion({ collapsible: true, active: false, autoHeight: false });
    $( ".level1" ).accordion({ collapsible: true, active: false, autoHeight: false });
    $( ".level2" ).accordion({ collapsible: true, active: false, autoHeight: false });
    $( ".level3" ).accordion({ collapsible: true, active: false, autoHeight: false });
    $( ".level4" ).accordion({ collapsible: true, active: false, autoHeight: false });
  });
</script>

<?
function counttasks($array) {
  $count = 0; 
  if (is_array($array)) { 
    foreach($array as $id=>$sub) { 
      if (!is_array($sub)) { $count++; } 
      else { $count = ($count + counttasks($sub)); } 
    } 
    return $count; 
  } 
  return FALSE; 
  }
function date2string($Date) {
  $Y = date("Y",strtotime($Date));
  $M = date("m",strtotime($Date));
  $D = date("d",strtotime($Date));
  $month["01"] = "января";
  $month["02"] = "февраля";
  $month["03"] = "марта";
  $month["04"] = "апреля";
  $month["05"] = "мая";
  $month["06"] = "июня";
  $month["07"] = "июля";
  $month["08"] = "августа";
  $month["09"] = "сентября";
  $month["10"] = "октября";
  $month["11"] = "ноября";
  $month["12"] = "декабря";
  return $D." ".$month[$M]." ".$Y;
  }
$filetype=$_REQUEST["filetype"];
if($filetype=="xls") {
  $xls = "";
}
if(is_array($stt)) {
  foreach($stt as $district=>$settlements) {
    $mt++;
    $count_stt = count($stt);
    //if($mt==$count_stt) break;
    $html .= "<div class=\"level0\" id=\"visits\" fb='М.Район'>\n";
    $html .= "\t<h3><a href=\"#\" onfocus=\"this.blur()\">$district</a><b>".(counttasks($stt[$district])/2)."</b></h3>\n";
    $html .= "\t<div>\n";
    $xls .= $district."\n";
    if(is_array($settlements)) {
      foreach($settlements as $settlement=>$streets) {
	$html .= "\t\t<div class=\"level1\" fb='Н.пункт'>\n";
	$html .= "\t\t\t<h3><a href=\"#\">$settlement</a><b>".(counttasks($stt[$district][$settlement])/2)."</b></h3>\n";
	$html .= "\t\t\t<div>\n";
	$xls .= "\t".$settlement."\n";
	if(is_array($streets)) {
	  foreach($streets as $street=>$homes) {
	    $html .= "\t\t\t\t<div class=\"level2\" fb='Улица'>\n";
	    $html .= "\t\t\t\t\t<h3><a href=\"#\">Улица $street</a><b>".(counttasks($stt[$district][$settlement][$street])/2)."</b></h3>\n";
	    $html .= "\t\t\t\t\t<div>\n";
	    $xls .= "\t\t".$street."\n";
	    if(is_array($homes)) {
	      foreach($homes as $home=>$tasks) {
		$html .= "\t\t\t\t\t\t<div class=\"level3\" bz='Дом'>\n";
		$html .= "\t\t\t\t\t\t\t<h3><a href=\"#\">Дом $home</a><b>".(counttasks($stt[$district][$settlement][$street][$home])/2)."</b></h3>\n";
		$html .= "\t\t\t\t\t\t\t<div>\n";
		$xls .= "\t\t\t".$home."\n";
		$html .= "\t\t\t\t\t\t\t\t<div class='level4'>\n";

		if(is_array($tasks)) {
		  foreach($tasks as $taskid=>$taskinfo) {
		    $taskdate = date2string($taskinfo[taskdate]);
		    $html .= "\t\t\t\t\t\t\t\t\t<h3><a href='#'>Обращение №".$taskid." (от ".$taskdate.")</a></h3>\n";
		    $html .= "\t\t\t\t\t\t\t\t\t<div>";
		    $html .= nl2br($taskinfo[taskinfo])."\n";
		    $html .= "\t\t\t\t\t\t\t\t\t</div e_appeal m0>\n";
		  }
		}
                $html .= "\t\t\t\t\t\t\t\t</div e_l4 m1>\n";
		$html .= "\t\t\t\t\t\t\t</div e_l4_w m2>\n";
		$html .= "\t\t\t\t\t\t</div e_l3 m3>\n";
		
	      }
	    }
	    $html .= "\t\t\t\t\t</div e_l3_w m4>\n";
	    $html .= "\t\t\t\t</div e_l2 m5>\n";
	  }
	}
	$html .= "\t\t\t</div e_l2_w m6>\n";
	$html .= "\t\t</div e_l1 m7>\n";
      }
    }
    $html .= "\t</div m8>\n";
    $html .= "</div m9>\n";
  }
}

if($filetype=="xls") {
  echo "<textarea>$xls</textarea>";
}
//echo $html;
$statistics2 = "
$header
<table width=\"960\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
  <tr>
    <td id=\"feedback\" style=\"width: 610px; padding-right:20px\" valign=\"top\">
      <div class=\"as_table_header\">
	Муниципальный район/Городской округ<b>Кол-во обращений</b>
      </div>
      $html
    </td>
    <td id=\"faq\" style=\"background-color: #F8F8F8;width: 330px;text-align: center;\">
      <div class=\"simple-buttons\">
	<p style=\"width: 200px; text-align: center;\"
	   onclick=\"javascript: window.location.href = '/feedback/new-form?serviceid=2040&amp;themeid=2314';\"><ins><input type=\"button\" value=\"\">Задать вопрос</ins></p>
      </div>
    </td>
    
  </tr>
  
</table>";

$statistics = "
$header
<div class=\"service-block zags\">
  <div class=\"t\">
    <div class=\"b\">
      <div class=\"uform\">
        <div class=\"service-logo\">
          <p style=\"margin-top:0\"><a href=\"#\"  onclick=\"javascript: window.location.href = '/feedback/new-form?serviceid=2040&amp;themeid=2314';\"><img src=\"/design/images/feedback/faq.jpg\" width=\"212\" height=\"90\" alt=\"Часто задаваемые вопросы\" /></a></p>
	</div>
	
	<div class=\"data\">
          <h1>Обратная связь</h1>
          <h2>Статистика обращений по капитальному ремонту</h2>

	  <div class=\"as_table_header\">
	    Муниципальный район/Городской округ<b>Кол-во обращений</b>
	  </div>
	  $html
	  <div class=\"simple-buttons\" style=\"width:400px;text-align: center;\">
	    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<p>
	      <ins>
		<input type=\"submit\" name=\"action_send\" value=\"\" onclick=\"javascript: window.location.href = '/feedback/new-form?serviceid=2040&amp;themeid=2314';\">Подать новое обращение
	      </ins>
	    </p>
	  </div>
	</div>
      </div>
    </div>
  </div>
</div>

";

echo $statistics;
?>
