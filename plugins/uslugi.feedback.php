<?php
include_once "statusage.php"; //by almaz - usage control

$form_title_class = "fieldname";
$form_data_class = "fielddata";
$form_input_fields_class = "inputs_hide";
$form_input_fields_hide = "inputs_hide faqs";
$post = "style=\"padding-left: 25px;\"";
$field_style = "style=\"width: 170px;padding-left: 5px; padding-right: 10px;\"";
$hint = " class='f-hint' ";
//print_r($_REQUEST);
$warning = "
	    <fieldset style=\"padding:10px;\">
<legend>&nbsp;&nbsp;Уважаемые посетители Портала государственных и муниципальных услуг Республики Татарстан!&nbsp;</legend>
<!-- Чтобы получить оперативный и квалифицированный ответ на Ваш вопрос, обязательно заполните все поля формы. 
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
--><p><b>Задать вопрос службе технической поддержки</b> Вы можете через <a href='/feedback/form'>форму обратной связи</a> </p>
<p>Чтобы получить оперативный и квалифицированный ответ на Ваш вопрос, обязательно заполните все поля формы. Лаконично и грамотно сформулируйте текст Вашего обращения.</p>
<p>Прежде чем отправить обращение через Интернет-приемную, рекомендуем Вам просмотреть раздел \"Часто задаваемые вопросы\". Возможно, Вы сразу найдете информацию на интересующую Вас тему.</p>
	</fieldset>
";

include ("db.inc.php");
include ("functions.php");
include ("js/feedback.js");
include ("fieldtypes.php");

$action_send = $_REQUEST["action_send"];
$data = $_REQUEST["data"];
$addons = $_REQUEST["addons"];
$serviceid = $_REQUEST["serviceid"];
$themeid = $_REQUEST["themeid"];

function build_fields($Data) {
  $html .= "<br/><table width='' cellpadding='1' cellspacing='0' border='0'>";
  foreach($Data["0"]["field"] as $mk=>$field) {
    $name = $field["name"];
    $fieldtype = $field["fieldtype"];  
    $fieldinput = $field["fieldinput"];
    $fieldname = $field["fieldname"];
    $html .= "<tr>";
    if($fieldinput || $fieldtype) { $colspan = "";}    else { $colspan = "colspan=2";    }
    if($field["isrequired"]) { $required = "<span class='red'>*</span>";} else { $required = "";}
    $html .= "<td $field_style2 style='width: 180px; padding-left: 5px;' $colspan>".$field["name"].": $required</td>";
    if($fieldinput && !$fieldtype) {
      $html .= "<td style='padding-left:5px;'>&nbsp;".$fieldinput."</td>";
    }
    if($fieldtype) {
      if(!$fieldname) { 
	if($name) { $fieldname = $name;} else {$fieldname = $mk;}
      }
      $html .= "<td>".feedback_fieldtype("addons[".$fieldname."]",$fieldname,$fieldtype)."</td>";
    }
    $html .= "</tr>";
  }
  $html .= "</table><br/>";
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
  $assigned2id = 217;
  $assignedbyid = 106;
  //$subscriptions[user_id][] = 204; // Альбина Ф.
  $subscriptions[user_id][] = 325; // Марина Суркова
  $subscriptions[user_id][] = 316; // Надежда Назарова
  $subscriptions[user_id][] = 197; // Ольга Копачинская
  $subscriptions[user_id][] = 284; // Эльмира Гиниятуллина
  //  $subscriptions[user_id][] = 163;
  //$subscriptions[user_id][] = 181;
  //$subscriptions[user_id][] = 184;
  //$subscriptions[user_id][] = 182;
  //$subscriptions[user_id][] = 183;
  //  $subscriptions[user_id][] = 18;
  //$subscriptions[user_id][] = 180;

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
   $taskid = "error";
  //$taskid = opengoo_webservice_insert_task($projectid[Услуга],$title,$assigned2id,$assignedbyid,$appeal,$data,$subscriptions);
  //$newprojectid = opengoo_insert_workspace_objects($taskid,$projectid["Тема_обращения"],$assignedbyid);
}

if(!$taskid_errors && !$taskid) {

$themes_level1_arr = opengoo_list_subprojects(2025);
$themes = "<tr>
               <td $field_style>Тема обращения: <span class='red'>*</span></td>
               <td>
                   <select id=\"themeid\" name=\"projectid[Тема_обращения]\" style='width:350px'>
                   <option value=''>Нажмите для выбора</option>";
foreach($themes_level1_arr as $k=>$v) {
    $themes_level2_arr = opengoo_list_subprojects($v["id"]);
    if(is_array($themes_level2_arr)) {
        $themes .= "\t<optgroup label='".$v["name"]."'>\n";
        if($themes_level2_arr) {
            foreach($themes_level2_arr as $k2=>$v2) {
	      if($v2["id"]==$projectid[Тема_обращения]) {
                $theme_selected = "selected='selected'";
              } else {
                $theme_selected = "";
              }

                $themes .= "\t\t<option value='".$v2["id"]."' title='".$v2["name"]."' class='theme' $theme_selected>".$v2["name"]."</option>\n";
            }
        }
        $themes .= "\t</optgroup>\n";
    }
}
$themes .= "\t</select>\n\t</td></tr>";



$services_level1_arr = opengoo_list_subprojects(2026);
$services = "<tr>
              <td $field_style>Услуги: <span class='red'>*</span></td>
              <td>
               <select id=\"serviceid\" name=\"projectid[Услуга]\" onchange=\"show_feedback(this.value);\" style='width:350px;'>
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
	      $inputdata .= "\t<div id='inputfields-".$v2["id"]."' class='".$form_input_fields_class."' style='$style'>\n";
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
		    $faqhtml .= "<div style='padding-left: 20px;'>
                                  <fieldset style='padding:10px;'>
                                  <legend style='font-weight: bold;'>&nbsp;&nbsp;".$fv["title"]."&nbsp;</legend>
                                  ".$fv["text"]."</fieldset>
                                  <br/>
                                 </div>";
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
$services .= "\t</select>\n\t</td></tr>";


?>
<script>
function show_feedback(fieldID) {
    $('.inputs_hide').hide();
    $('#inputfields-' + fieldID).show('slow');
    $('#faq-' + fieldID).show('slow');
}

function goto_faq() { 
    //перейти на раздел ЧаВo
    $("#feedback").hide("slide",function() {
              $("#faq").css("width","990px");
              $(".faqs").show("slow");
              $("#allfaq").hide(); // скрываем кнопку перейти на раздел чаво
              $("#newfeedback").show(); //показываем кнопку хочу задать вопрос
    })
}

function goto_feedback() {
    $("#feedback").show("slow",function() {
      $("#feedback").css("width","660px");
      $("#faq").css("width","330px");
      $("#allfaq").show();
      $(".faqs").hide();    
      $(".inputs_hide").hide();
      })
}
</script>

<?
$appeal_description = 
   "
<!--    <fieldset style=\"padding: 20px;\">
      <legend>&nbsp;Описание обращения&nbsp;</legend>
      <table cellpadding=\"0\">
	".$errors_html."
	".$themes."
	".$services."
	<tr>
	  <td colspan=\"2\">".$inputdata."</td>
	</tr>
	<tr>
	  <td valign=\"top\" ".$field_style.">Сообщение: <span class='red'>*</span></td>
	  <td><textarea cols=\"40\" name=\"appeal\" rows=\"8\">".$appeal."</textarea></td>
	</tr>
      </table>
    </fieldset> -->
    ";
$appeal_contacts = 
    "
<!--    <fieldset style=\"padding:20px;\">
      <legend>&nbsp;Контактные данные&nbsp;</legend>
      <table width=\"\" border=\"0\" cellpadding=\"0\">
	".$errors_html."
	<tr>
	  <td ".$field_style.">Фамилия: <span class=\"red\">*</span></td><td><input name=\"data[Фамилия]\" type=\"text\" class=\"text\" size=\"30\" value=\"".$data[Фамилия]."\"/>
	</tr>
	<tr>
	  <td ".$field_style.">Имя: <span class=\"red\">*</span></td><td><input name=\"data[Имя]\" type=\"text\" class=\"text\" size=\"30\" value=\"".$data[Имя]."\"/></td>
	</tr>
	<tr>
	  <td ".$field_style.">Отчество: </td><td><input name=\"data[Отчество]\" type=\"text\" class=\"text\" size=\"30\" value=\"".$data[Отчество]."\"/></td>
	</tr>
	<tr>
	  <td ".$field_style.">Почтовый адрес</td>
	  <td></td>
	</tr>
	<tr>
	  <td ".$post.">Индекс:</td><td><input name=\"data[Почтовый_адрес_Индекс]\" type=\"text\" class=\"text\" value=\"".$data[Почтовый_адрес_Индекс]."\"/></td>
	</tr>
	<tr>
	  <td ".$post.">Район: <span class=\"red\">*</span></td><td><input class=\"s-medium\" name=\"data[Почтовый_адрес_Район]\" type=\"text\" class=\"s-medium\" size=\"30\" value=\"".$data[Почтовый_адрес_Район]."\"/></td>
	</tr>
	<tr>
	  <td ".$post.">Город / Поселение: <span class=\"red\">*</span></td><td><input name=\"data[Почтовый_адрес_Поселение]\" type=\"text\" class=\"text\" size=\"30\" value=\"".$data[Почтовый_адрес_Поселение]."\"/></td>
	</tr>
	<tr>
	  <td ".$post.">Улица: <span class=\"red\">*</span></td><td><input name=\"data[Почтовый_адрес_Улица]\" type=\"text\" class=\"text\" size=\"30\" value=\"".$data[Почтовый_адрес_Улица]."\"/></td>
	</tr>
	<tr>
	  <td ".$post.">Корпус: </td><td><input name=\"data[Почтовый_адрес_Корпус]\" type=\"text\" class=\"text\" size=\"30\" value=\"".$data[Почтовый_адрес_Корпус]."\"/></td>
	</tr>
	<tr>
	  <td ".$post.">Дом: </td><td><input name=\"data[Почтовый_адрес_Дом]\" type=\"text\" class=\"text\" size=\"30\" value=\"".$data[Почтовый_адрес_Дом]."\"/></td>
	</tr>
	<tr>
	  <td ".$post.">Квартира: </td><td><input name=\"data[Почтовый_адрес_Квартира]\" type=\"text\" class=\"text\" size=\"30\" value=\"".$data[Почтовый_адрес_Квартира]."\"/></td>
	</tr>
	<tr>
	  <td ".$field_style.">Контактый телефон: <span class=\"red\">*</span></td><td><input name=\"data[Контактный_телефон]\" type=\"text\" size=\"30\" value=\"".$data[Контактный_телефон]."\"/>
	    <br/><span ".hint.">Укажите полный номер мобильного телефона или городской номер с указанием кода города</span></td>
	</tr>
	<tr>
	  <td ".$field_style.">Место работы или учебы: </td><td><input name=\"data[Место_работы_или_учебы]\" type=\"text\" class=\"text\" size=\"30\" value=\"".$data[Место_работы_или_учебы]."\"/></td>
	</tr>
	<tr>
	  <td ".$field_style.">E-mail:</td><td><input name=\"data[Email]\" type=\"text\" class=\"email\" size=\"30\" value=\"".$data[Email]."\"/></td>
	</tr>
      </table>
    </fieldset> -->
    ";
$faqs = 
    "
    <h3>Часто задаваемые вопросы</h3>
    <div id=\"allfaq\"><a href=\"#\" onclick=\"goto_faq()\">перейти в раздел \"Вопросы и Ответы\"</a></div>
    ".$faqhtml."
   <div id=\"newfeedback\" class=\"inputs_hide\" style=\"display:none;\"><input type=\"button\" onclick=\"goto_feedback();\" value=\"Хочу задать новый вопрос\"/></div>
    ";
?>

<form action="" method="POST">
  <input type="hidden"                            name="mainprojectname"       value="Услуга" />
  <input type="hidden" id="region_id"             name="region_id"             value="12" />
  <input type="hidden" id="address_region_code"   name="address_region_code"   value="1600000000000" />
  <input type="hidden" id="address_area_code"     name="address_area_code"     value="" /> 
  <input type="hidden" id="address_location_code" name="address_location_code" value="" />
  <input type="hidden" id="address_street_code"   name="address_street_code"   value="" /> 
  
  <table width="960" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td id="feedback" style="width: 630px;padding: 5px;" valign="top">
	<?=$feedback;?>
	<?=$warning;?>
	<?=$appeal_description;?>
        <?=$appeal_contacts;?>
<!--   <div style="width: 600px; text-align: center;" align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="action_send" value="Отправить"/></div> -->
      </td>
      <td id="faq" style="background-color: #F8F8F8;display: width: 390px;text-align: left;padding: 5px;">
	<?=$faqs;?>
      </td>
    
    </tr>
  
  </table>
  
</form>
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

?>
