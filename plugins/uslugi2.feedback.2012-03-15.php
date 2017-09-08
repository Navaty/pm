<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
include ("fieldtypes.php");
include ("module_msed.php");


$stat = $_REQUEST["stat"];
if($stat) {
    include("uslugi2.statistics.feedback.php");
  //include("uslugi2.statistics.test.feedback.php");
} else {
include ("js/feedback2.2011-11-14.js");

$randid = rand();
?><script language="JavaScript" src="/javascript/lib/suggest-text-field.js?<?=$randid;?>" type="text/javascript"></script>
<script language="JavaScript" src="/javascript/lib/views/partials/payer-information.js?<?=$randid;?>" type="text/javascript"></script>
<?
$form_title_class = "fieldname";
$form_data_class = "fielddata";
$form_input_fields_class = "inputs_hide";
$form_input_fields_hide = "inputs_hide faqs";
$post = "style=\"padding-left: 25px;\"";
$field_style = "style=\"width: 50px;padding-left: 5px; padding-right: 10px;\"";
$hint = " class='f-hint' ";
//print_r($_REQUEST);
$warning = "
	    <div class='data' style='font-size: 14px;'>
<legend>&nbsp;&nbsp;Уважаемые посетители Портала государственных и муниципальных услуг Республики Татарстан!&nbsp;</legend>
Чтобы получить оперативный и квалифицированный ответ на Ваш вопрос, обязательно заполните все поля формы. 
Лаконично и грамотно сформулируйте текст Вашего обращения.
<p>
<b>В случае если Ваш вопрос относится к компетенции Портала государственных и муниципальных услуг</b>, то Ваше обращение будет рассмотрено в течение 3-х рабочих дней с момент его регистрации.
<p>
<b>Если Ваш вопрос относится к компетенции Правительства Республики Татарстан</b>, то Ваше обращение будет рассмотрено в течение 30 дней с момента его регистрации.
<p/>
<p>
Прежде чем отправить обращение через Интернет-приемную, рекомендуем Вам просмотреть раздел \"Часто задаваемые вопросы\". Возможно, Вы сразу найдете информацию на интересующую Вас тему.
</p><p>
<b><font color=red>ВНИМАНИЕ!</font></b> В случае, если в письменном обращении не указаны фамилия гражданина, направившего обращение, и почтовый адрес, по которому должен быть направлен ответ, ответ на обращение не дается. (<a href='http://prav.tatar.ru/rus/fz59.htm'>Федеральный закон РФ от 2 мая 2006г. № 59-ФЗ О порядке рассмотрения обращений граждан Российской Федерации</a>)</p>
	</div>
";

$action_send = $_REQUEST["action_send"];
$data = $_REQUEST["data"];
$addons = $_REQUEST["addons"];
$serviceid = $_REQUEST["serviceid"];
$themeid = $_REQUEST["themeid"];

function build_fields($Data) {
  $html .= "<p>";
  foreach($Data["0"]["field"] as $mk=>$field) {
    $name = $field["name"];
    $fieldtype = $field["fieldtype"];  
    $fieldinput = $field["fieldinput"];
    $fieldname = $field["fieldname"];
    $html .= "<div class='h'>";
    if($fieldinput || $fieldtype) { $colspan = "";}    else { $colspan = "style='width:90%;'";    }
    if($field["isrequired"]) { $required = "<ins class='rq'>*</ins>";} else { $required = "";}
    $html .= "<label for='' $colspan><span>".$field["name"].": $required</span></label>";
    if($fieldinput && !$fieldtype) {
      $html .= $fieldinput;
    }
    if($fieldtype) {
      if(!$fieldname) { 
	if($name) { $fieldname = $name;} else {$fieldname = $mk;}
      }
      $html .= "\t\t".feedback_fieldtype("addons[".$fieldname."]",$fieldname,$fieldtype)."\n";
    }
    $html .= "</div>";
  }
  $html .= "</p>";
  return $html;
}


$projectid = $_REQUEST["projectid"];
if($serviceid) {
  $projectid["Услуга"] = $serviceid;
}

if($themeid) {
  $projectid["Тема_обращения"] = $themeid;
}

$mainprojectname = $_REQUEST["mainprojectname"];

$appeal = $_REQUEST["appeal"];



if(!$data[Фамилия]             && $action_send) {  $error[] = "Введите Фамилия!"; }
if(!$data[Имя]                 && $action_send) {  $error[] = "Введите Имя!"; }
if(!$data[Почтовый_адрес_Район]               && $action_send) {  $error[] = "Введите Район!"; }
if(!$data[Почтовый_адрес_Поселение]           && $action_send) {  $error[] = "Введите Поселение!"; }
if(!$data[Почтовый_адрес_Улица]               && $action_send) {  $error[] = "Введите Улица!"; }
if(!$data[Контактный_телефон]  && $action_send) {  $error[] = "Введите Контакты-телефон!"; }
if(!$projectid[Услуга]         && $action_send) {  $error[] = "Введите Услугу!"; }
if(!$projectid[Тема_обращения] && $action_send) {  $error[] = "Введите Тема обрщения!";}
if(!$appeal                    && $action_send) {  $error[] = "Введите описание обращения!";}

#print_r($error);
if(is_array($error)) {
  foreach($error as $v) {
    //    $errors_val .= $v." <br/>";
  }
  $errors_html = "<tr><td colspan='2'><font color='red'>
     <p>$ALMAZerrors_val Пожалуйста, заполните обязательные поля*</font>
      </td></tr>";
} 
if($action_send && !$error) {
  $assigned2id = 25;
  $assignedbyid = 106;
  $subscriptions[user_id][] = 25;
  $subscriptions[user_id][] = 163;
  $subscriptions[user_id][] = 181;
  $subscriptions[user_id][] = 184;
  $subscriptions[user_id][] = 182;
  $subscriptions[user_id][] = 183;
  $subscriptions[user_id][] = 18;
  $subscriptions[user_id][] = 177;
  $subscriptions[user_id][] = 25;
  $subscriptions[user_id][] = 180;

  $projectname = opengoo_get_projectname_by_projectID($projectid[$mainprojectname]);
  $title = $projectname." : ".$data[Фамилия]." ".$data[Имя]." ".$data[Отчество];
  $data[Тема_обращения] = opengoo_get_projectname_by_projectID($projectid[Тема_обращения]);
  $data[Услуга] = $projectname;
  if(is_array($addons)) {
    foreach($addons as $k=>$v) {
      $name = "_".$k;
      $data[$name] = $v;
    }
  }
  $taskid = opengoo_webservice_insert_task($projectid[Услуга],$title,$assigned2id,$assignedbyid,$appeal,$data,$subscriptions);
  $newprojectid = opengoo_insert_workspace_objects($taskid,$projectid["Тема_обращения"],$assignedbyid);
}

if(!$taskid_errors && !$taskid && !$stat) {

  //$themes_level1_arr = opengoo_list_subprojects(2362);
$themes_level1_arr = msed_get_category();
//print_r($themes_level1_arr[data]);
$themes = "<div class='h' id='theme_2' style='display: none;'>
               <label for='themeid'><span>Тема обращения:<ins class='rq'>*</ins></span></label>
               <select id='themeid' name=\"projectid[Тема_обращения]\" class='s-large'>
                   <option value=''>Нажмите для выбора</option>";

foreach($themes_level1_arr["data"] as $k=>$v) {
  //    $themes_level2_arr = opengoo_list_subprojects($v["id"]);
  $themes_level2_arr = msed_get_category($k);
    if(is_array($themes_level2_arr[data])) {
        $themes .= "\t<optgroup label='".$v."'>\n";
        if($themes_level2_arr) {
            foreach($themes_level2_arr[data] as $k2=>$v2) {
	      if($k2==$projectid[Тема_обращения]) {
                $theme_selected = "selected='selected'";
              } else {
                $theme_selected = "";
              }

                $themes .= "\t\t<option value='".$k2."' title='".$v2."' class='theme' $theme_selected>".$v2."</option>\n";
            }
        }
        $themes .= "\t</optgroup>\n";
    }
}
$themes .= "\t</select>\n\t</div>";


$appeal_types = "
<div class='h'>
 <label for='appeal_type'>
  <span>Вид обращения:<ins class='rq'>*</ins></span>
 </label>
 <select name='appeal_type' id='appeal_type'>
 <option value=''>Нажмите для выбора</option>
 <option value='2359'>Благодарность</option>
 <option value='2360'>Жалоба</option>
 <option value='2361'>Заявление</option>
 </select>";


$appeal_types_arr = opengoo_list_subprojects(2025);
foreach($appeal_types_arr as $k=>$v) {
  $appeal_types .= "\t<option value='".$v['id']."'>".$v['name']."</option>\n";
}

$appeal_types .= "</div>";


$theme_types = "
    <div class='h' style='margin:5px 0'>
     <label for='theme_type'>
      <span>Тип:<ins class='rq'>*</ins></span>
     </label>
     <div id='theme_type' class='buttonset'>
      <input type='radio' id='theme_type_1' name='theme_type'  onclick='feedback_show_theme(1);'/><label for='theme_type_1'>По электронной услуге</label>&nbsp;&nbsp;<input type='radio' id='theme_type_2' name='theme_type'  onclick='feedback_show_theme(2);'/><label for='theme_type_2'>По жизненной ситуации</label></div>
 </div>
<style>
#theme_type label{
background:none;
float:none
}

#theme_type input{
width:auto;
margin-right:5px;
border:0;
}
</style>
";

$services_level1_arr = opengoo_list_subprojects(2026);
$services = "<div class='h' id='theme_1' style='display: none;'>
              <label for='serviceid'>
               <span>Услуги:<ins class='rq'>*</ins></span></label>
               <select id='serviceid' name=\"projectid[Услуга]\" onchange=\"show_feedback(this.value);\" class='s-large'>
                <option value=''>Нажмите для выбора</option>";
foreach($services_level1_arr as $k=>$v) {
    $services_level2_arr = opengoo_list_subprojects($v["id"]);
    if(is_array($services_level2_arr)) {
	$services .= "\t<optgroup label='".$v["name"]."'>\n";
	if($services_level2_arr) {
	    foreach($services_level2_arr as $k2=>$v2) {
		  if($v2["id"] == $projectid[Услуга]) {
		    $style = "";
		  } else {
		    $style = "display:none;";
		  }
	      $inputdata .= "\t<div id='inputfields-".$v2["id"]."' class='".$form_input_fields_class."' style='$style'>";
	      if($v2["id"]==$projectid[Услуга]) { 
		$service_selected = "selected='selected'";
	      } else { 
		$service_selected = "";
	      }
		$services .= "\t\t<option value='".$v2["id"]."' title='".$v2["name"]."' $service_selected>".$v2["name"]."</option>\n";

		$faqdata = opengoo_get_project_notes($v2["id"]);
		if(is_array($faqdata)) {
		  $faqhtml .= "<div id='faq-".$v2["id"]."' class='".$form_input_fields_hide."' style='display:none;'>
                                <h3 style='font-weight: bold'>Услуга: ".$v2["name"]."</h3>";
		  foreach($faqdata as $vk=>$fv) {
		    $faqhtml .= "
		                  <div class='alone-rpt' style='padding-bottom:10px; background-color: transparent !important'>
                                  <div class='alone-top' style=' background-color: transparent !important; color: black;'><hr/></div>
                                  <h2 style='color: black'>".$fv["title"]."</h2>
				  <div style='color: black;'>
                                  ".$fv["text"]."
				  </div>
                                  </div>
                                  <div class='alone-bottom' style=' background-color: transparent !important'><hr/></div><br/>";

		  }
		  $faqhtml .= "</div>";
		}
	        $xmldata = opengoo_get_project_xml($v2["id"]);
	        $inputarr = xml2array($xmldata);
	        if(is_array($inputarr["xml"]["fields"])) {
		    $inputfields .= build_fields($inputarr["xml"]["fields"]);
		} else {
		    $inputfields = "";
		}

	        $inputdata .= $inputfields;
        	$inputdata .= "</div>\n"; 
	    }
	}
	$services .= "\t</optgroup>\n";
    }
}
$services .= "\t</select>\n\t</div>";

$appeal_description = 
   "
   <div class='alone-rpt'>
    <div class='alone-top'><hr/></div>
    <h2>Описание обращения</h2>
      <table cellpadding=\"0\">
	".$errors_html."
      </table>
        ".$appeal_types."
        ".$theme_types."

	".$themes."
	".$services."

	<div class='h'>
	  ".$inputdata."
	</div>

	<div class='h'>
	  <label for='message'>
           <span>Сообщение:<ins class='rq'>*</ins></span>
          </label>
	  <textarea id='message'  rows='8'  style=\"font:14px Tahoma;padding: 3px;width:330px;\"  name='appeal'>".$appeal."</textarea>
	</div>
    </div>
    <div class='alone-bottom'><hr/></div>
    <br/>
    ";

$appeal_contacts = 
    "
       <div class='alone-rpt'>
    <div class='alone-top'><hr/></div>
      <h2>Контактные данные</h2>
      <table width=\"\" border=\"0\" cellpadding=\"0\">
	".$errors_html."
      </table>

	<div class='h'>
	  <label for='surname'><span>Фамилия:<ins class='rq'>*</ins></span></label>
          <input id='surname' name='data[Фамилия]' type='text' class='s-medium' value='".$data[Фамилия]."'/>
	</div>

	<div class='h'>
	  <label for='name'><span>Имя:<ins class='rq'>*</ins></span></label>
          <input id='name' name='data[Имя]' type='text' class='s-medium' value='".$data[Имя]."'/>
	</div>

	<div class='h'>
	  <label for='fathername'><span>Отчество:</span></label>
          <input id='fathername' name='data[Отчество]' type='text' class='s-medium' value='".$data[Отчество]."'/>
	</div>

	<div class='h'>
	  <label for='phone'><span>Контактый телефон:<ins class='rq'>*</ins></span></label>
          <input id='phone' name='data[Контактный_телефон]' type='text' class='s-medium' value='".$data[Контактный_телефон]."'/>
          <p class='f-hint'>Укажите полный номер мобильного телефона <br/>или городской номер с указанием кода города<br/></p>
	</div>

	<div class='h'>
	  <label for='job'><span>Место работы или учебы:</span></label>
	  <input id='job' name='data[Место_работы_или_учебы]' type='text' class='s-medium' value='".$data[Место_работы_или_учебы]."'/>
	</div>

	<div class='h'>
	  <label for='email'><span>E-mail:</span></label>
	  <input id='email' name='data[Email]' type='text' class='s-medium' value='".$data[Email]."'/>
	</div>

	<p><strong><em>Почтовый адрес</em></strong></p>

	<div class='h'>
	  <label for='index'><span>Индекс:</span></label>
          <input id='index' name='data[Почтовый_адрес_Индекс]' type='text' class='s-medium' value='".$data[Почтовый_адрес_Индекс]."'/>
	</div>

	<div class='h'>
	  <label for='district'><span>Район:<ins class='rq'>*</ins></span></label>
          <input id='district' name='data[Почтовый_адрес_Район]' type='text' class='s-medium'  value='".$data[Почтовый_адрес_Район]."'/>
	</div>

	<div class='h'>
	  <label for='city'><span>Город / Поселение:<ins class='rq'>*</ins></span></label>
          <input id='city' name='data[Почтовый_адрес_Поселение]' type='text' class='s-medium' value='".$data[Почтовый_адрес_Поселение]."'/>
	</div>

	<div class='h'>
	  <label for='street'><span>Улица:<ins class='rq'>*</ins></span></label>
          <input id='street' name='data[Почтовый_адрес_Улица]' type='text' class='s-medium' value='".$data[Почтовый_адрес_Улица]."'/>
	</div>

	<div class='h'>
	  <label for='home-index'><span>Корпус:</span></label>
          <input id='home-index' name='data[Почтовый_адрес_Корпус]' type='text' class='s-medium' value='".$data[Почтовый_адрес_Корпус]."'/>
	</div>

	<div class='h'>
	  <label for='house'><span>Дом:</span></label>
          <input id='house' name='data[Почтовый_адрес_Дом]' type='text' class='s-medium' value='".$data[Почтовый_адрес_Дом]."'/>
	</div>

	<div class='h'>
	  <label for='apartment'><span>Квартира:</span></label>
          <input id='apartment' name='data[Почтовый_адрес_Квартира]' type='text' class='s-medium' value='".$data[Почтовый_адрес_Квартира]."'/>
	</div>

    </div>
    <div class='alone-bottom'><hr/></div>
    ";

?>
<div class="service-block zags">
  <div class="t">
    <div class="b">
      <div class="uform">
	<div class="service-logo">
	  <p style="margin-top:0">
	    <div id="faq">
	      <div id="allfaq_button">
		<a href="#" onclick="goto_faq();">
		  <img src="/design/images/feedback/faq.jpg" width="212" height="90" alt="Часто задаваемые вопросы" />
		</a>
	      </div>
	      <div id="allfaq_content">
		<?=$faqhtml;?>
	      </div>

	      <div id="newfeedback" class="extra-submit-text clearfix" style="display:none;">
		<p class="forward">
		  <ins><input type="button" onclick="goto_feedback();" value=""/>Хочу задать новый вопрос</ins>
		</p>
	      </div>
	      
	      <br/>
	      <div id="stat">
		<a href='/feedback/new-form?&stat=true'><img src='/design/images/feedback/stat.jpg'/></a>
	      </div>
	    </div>
	  </p>

	</div>
	<div id="feedback" class='data'>
	  <form action="" method="POST">
	    <input type="hidden"                            name="mainprojectname"       value="Услуга" />
	    <input type="hidden" id="region_id"             name="region_id"             value="12" />
	    <input type="hidden" id="address_region_code"   name="address_region_code"   value="1600000000000" />
	    <input type="hidden" id="address_area_code"     name="address_area_code"     value="" /> 
	    <input type="hidden" id="address_location_code" name="address_location_code" value="" />
	    <input type="hidden" id="address_street_code"   name="address_street_code"   value="" /> 
	    
	    <?=$feedback;?>	
	    
	    <?=$warning;?>
	    <?=$appeal_description;?>
	    <?=$appeal_contacts;?>
	    
	    <div class="extra-submit-text clearfix">
	      <p class="back" id="backStageButton" onclick="javascript:history.back()">
		<ins><input type="button" value=""/>Назад</ins>
	      </p>
	      <p class="next-stage">
		<ins><input type="submit" value="" name="action_send">Отправить</ins>
	      </p>
	    </div>
	    
	  </form>
	</div>
      </div>
    </div>
  </div>
</div>
	<?
	  }

if($taskid) {
  $answer = "
<p>Ваше обращение принято</p>
<p>Номер Вашего обращания: <b>".$taskid."</b> 
<br/>
(c помощью него сможете узнать статус рассмотрения обращения у оператора технической поддержки<!-- или через <a href=\"\">форму проверки статуса</a>-->)</p>
<!--<p>Срок рассмотрения обращения: 3 рабочих дня.</p>-->
<p>Техническая поддержка портала: +7(843) 5-114-115 (круглосуточно)</p>
<!--<p>Через 60 секунд Вы вернетесь в главное меню портала!</p>-->

";
  echo $answer;
}

#echo "<pre>";
#  print_r($_REQUEST);

//echo $ip=$_SERVER['REMOTE_ADDR'];
//print_r($_SERVER);
}
?>