<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");
//include ("module_opengoo.php");
$choose_title = "Необходимо выбрать";

$actionname = $_REQUEST["actionname"];

function get_spheres_by_places($PlaceID) {
  $sql = "
     SELECT ipp.SphereID AS ID , op.name AS name
     FROM  сс_incidents_projects_places as ipp,
     og_projects AS op
     WHERE ipp.PlaceID = '$PlaceID' 
     AND ipp.IsDeleted = '0' 
     AND ipp.DeleteDate IS NULL 
     AND ipp.SphereID = op.ID
     order by name asc";
  $res = ssql($sql);
  if(is_array($res)) {
    foreach($res as $k=>$v) {
     $spheres[$v["ID"]] = opengoo_get_project_name($v["ID"],false);
    }
  }
  return $spheres;
}

function get_services_by_sphere($PlaceID,$SphereID) {
  $sql = "SELECT ipp.ServiceID AS ID , op.name as name
FROM  сс_incidents_projects_places AS ipp,
og_projects AS op
WHERE ipp.PlaceID = '$PlaceID' 
AND ipp.ServiceID = op.ID
AND ipp.SphereID = '$SphereID' 
AND ipp.IsDeleted = '0' 
AND ipp.DeleteDate IS NULL 
order by name";
  $res = ssql($sql);
  if(is_array($res)) {
    foreach($res as $k=>$v) {
     $services[$v["ID"]] = opengoo_get_project_name($v["ID"],false);
    }
  }
  return $services;
}

function get_classificators_by_sphere_service($PlaceID,$SphereID,$ServiceID) {
  $sql = "SELECT ipp.IncidentID AS ID, op.name AS name
FROM  сс_incidents_projects_places AS ipp,
og_projects AS op 
WHERE ipp.PlaceID = '$PlaceID' 
AND op.ID = ipp.IncidentID
AND ipp.SphereID = '$SphereID' 
AND ipp.ServiceID = '$ServiceID' 
AND ipp.IsDeleted = '0' 
AND ipp.DeleteDate IS NULL 
order by name";

  $res = ssql($sql);
  if(is_array($res)) {
    foreach($res as $k=>$v) {
     $incidents[$v["ID"]] = opengoo_get_project_name($v["ID"],false);
    }
  }
  return $incidents;
}
global $globalfields;
function make_fieldtypes() {
  global $globalfields;
  $fieldtypes = opengoo_list_subprojects(2568);
  foreach($fieldtypes as $k=>$v) {
    $fieldxml = opengoo_get_projectdescription_by_projectID($v["id"]);
    $fieldarr = xml2array($fieldxml);
    $globalfields[$fieldarr["xml"]["id"]]["name"] = $fieldarr["xml"]["name"];
    $globalfields[$fieldarr["xml"]["id"]]["comment"] = $fieldarr["xml"]["comment"];
    if($fieldarr["xml"]["type"]) {
      $globalfields[$fieldarr["xml"]["id"]]["type"] = $fieldarr["xml"]["type"];
    }
    if($globalfields[$fieldarr["xml"]["id"]]["type"]=="radio") {
      $values=opengoo_list_subprojects($v["id"]);
      foreach($values as $kk=>$vv) {
	$valuexml = opengoo_get_projectdescription_by_projectID($vv["id"]);
	$valuearr = xml2array($valuexml);
	$globalfields[$fieldarr["xml"]["id"]]["values"][] = $valuearr["xml"]["name"];
      }
    }
  }
}

make_fieldtypes();
function get_fieldtype($FieldID,$type="name") {
  global $globalfields;
  return $globalfields[$FieldID][$type];
}

function get_fields_by_service($ServiceID) {
  $xml = opengoo_get_projectdescription_by_projectID($ServiceID);
  $xmlarr = xml2array($xml);
  $fields = $xmlarr["xml"]["fieldtypes"];
  if($fields) {
    $fields_arr = explode(",",$fields);
  }
  if(is_array($fields_arr)) {
    return $fields_arr;
  } else {
    return false;
  }
}
function get_fields_by_incident($IncidentID) {
  $xml = opengoo_get_projectdescription_by_projectID($IncidentID);
  $xmlarr = xml2array($xml);
  $fields = $xmlarr["xml"]["fieldtypes"];
  if($fields) {
    $fields_arr = explode(",",$fields);
  }
  if(is_array($fields_arr)) {
    return $fields_arr;
  } else {
    return false;
  }
}

function make_form_select_options($Data) {
  foreach($Data as $k=>$v) {
    //if(!$k || $k=="0") { $key = "";} else { $key = $k;}
    //if(!isset($k)) { $key ="";}  else { $key = $k;}
    $key = $k;
    if($key=="0") { $key = "";}
    $options .= "\t\t<option value=\"".$key."\">".$v." ".$ke."</option>\n";
  }
  return $options;
}

function make_wrapper($FormName,$FormDetails,$FormComment=false,$Type="bz") {
  switch($Type) {
  case "bz":
    $html  = "<div class='line'>\n";
    $html .= "\t<label class='form'>$FormName:</label>\n";
    $html .= "\t".$FormDetails."\n";
    $html .= "\t<div class='form_description'>$FormComment</div>\n";
    $html .= "</div>\n";
    break;
  }
  return $html;
}

function make_form_select($Data,$FieldName=false,$ID=false,$Comment=false,$AddAttribute=false,$FirstValues=false) {
  if(!is_array($Structure)) { }
  if($FieldName) { $name = "name=\"".$FieldName."\"";}
  if($ID) { $id = "id=\"".$ID."\"";}
  $html  = "<select $name $id $AddAttribute style='width: 450px;'>\n";

  if(is_array($FirstValues)) {
    $html .= make_form_select_options($FirstValues);
  }
  if(is_array($Data)) {
    $html .= make_form_select_options($Data);
  }
  $html .= "\t</select>";
  $html .= $Comment;
  return $html;
}
function make_form_inputs($Data) {
  $r = rand();
  global $globalfields;
  //  print_r($globalfields);
  foreach($Data as $k=>$v) {
    if(!$k || $k=='0') { $key = "";} else { $key = $k;}
    $name = get_fieldtype($v);
    $inputname = str_replace(" ", "_", $name);
    $comment = get_fieldtype($v,"comment");
    if(get_fieldtype($v,"type")=="radio") {
      $value = "<div class='buttonset'>\n";
      foreach(get_fieldtype($v,"values") as $vs) {
	$data[$vs]=$vs;
	$r2 = rand();
	$value .= "\t\t<input type='radio' id='id".$r2."' name='".$inputname."' class='button2'/>\n\t\t<label for='id".$r2."'>".$vs."</label>\n";
      }
      $value .= "\t</div>\n";
      $value .= '
<script>
   $(function() {
    $( ".buttonset" ).buttonset();
   });
</script>';
      $input = make_form_select($data,"data[".$inputname."]",false,false,false,array($choose_title));
      $html .= make_wrapper($name,$input,$comment);
    } elseif(get_fieldtype($v,"type")=="date") {
      $value = "
       <input name=\"data[Дата_инцидента]\" class=\"input datepicker ready2clear\"/>
       <input name=\"data[Время_инцидента]\" class=\"input timepicker ready2clear\"/>
      ";
      $value .= '
<script>
      $(".datepicker").datepicker( $.datepicker.regional[ "ru" ] );
      $(".timepicker").timepicker({
       timeOnlyTitle: "Выберите время",
       timeText: "Время",
       hourText: "Часы",
       minuteText: "Минуты",
       secondText: "Секунды",
       currentText: "Сейчас",
       closeText: "Закрыть",
       hourGrid: 3,
       minuteGrid: 10
      });
</script>';

      $html .= make_wrapper($name,$value,$comment);
    } else {
      $value = "<input type='text' name='data[".$inputname."]' value='' style='width: 450px;'/>";
      $html .= make_wrapper($name,$value,$comment);
      $html .= "<br/>";
    }
  }
  return $html;
}
function make_form_input($Data,$FieldName=false,$ID=false,$Comment=false,$AddAttribute=false) {
  if(is_array($Data)) {
    $html .= make_form_inputs($Data);
  }
  return $html;
}

function make_appeal_form() {
  $html = '<script>
      $(".textarea-autoresizeonload").autoResize({onResize : function() {$(this).css({opacity:0.8});}, animateCallback : function() { $(this).css({opacity:1});
       },
       // Quite slow animation:
       animateDuration : 300,
       // More extra space:
       extraSpace : 20
   });</script>
  <textarea class="textarea textarea-autoresizeonload ready2clear" placeholder="Фиксируя задачи по МП, необходимо указывать модель и версию ОС телефона, версию приложения." name="appeal"></textarea>';
  return $html;
}

switch($actionname) {
  
case "spheres":
  $placeid = $_REQUEST["placeid"];
  $onchange = " onchange=\"show_services(".$placeid.",this.value);\"";
  echo make_wrapper("Сфера",make_form_select(get_spheres_by_places($placeid),
					     'projectid[Сфера]','sphereid',false,$onchange,array($choose_title)));
  echo "&nbsp;";
  break;
  
case "services":
  $placeid = $_REQUEST["placeid"];
  $sphereid = $_REQUEST["sphereid"];
  if($sphereid) {
    $onchange = " onchange=\"var placeid='".$placeid."'; var sphereid=$('#sphereid').val();show_classificators(placeid,sphereid,this.value);\"";
    echo make_wrapper("Услуга",
		      make_form_select(get_services_by_sphere($placeid,$sphereid),
				       'projectid[Услуга]','serviceid',false,$onchange,array($choose_title)));
    echo "&nbsp;";
  }
  break;

case "classificators":
  $placeid = $_REQUEST["placeid"];
  $sphereid = $_REQUEST["sphereid"];
  $serviceid = $_REQUEST["serviceid"];
  $onchange = " onchange=\"var placeid='".$placeid."'; var sphereid=$('#sphereid').val(); show_fields(this.value);\"";
  if($sphereid && $serviceid) {
    echo  make_wrapper("Классификатор",
		       make_form_select(get_classificators_by_sphere_service($placeid,$sphereid,$serviceid),
					'projectid[Классификатор]','classificatorid',false,$onchange,array($choose_title)));
    echo "&nbsp;";
  }
  break;

case "sfields":
  $serviceid = $_REQUEST["serviceid"]+0;
  //  $onchange = " onchange=\"show_sfields(this.value);\"";
  if($serviceid>0) {
    echo make_form_input(get_fields_by_service($serviceid),'service2name','service2id',false,$onchange,array('Заполните поля'));
    echo "&nbsp;";
  }
  break;

case "fields":
  $incidentid = $_REQUEST["incidentid"];
  $onchange = " onchange=\"show_fields(this.value);\"";
  if($incidentid) {
    //print_r(get_fields_by_service($incidentid));
    echo  make_form_input(get_fields_by_incident($incidentid),'incidentname','incidentidid',false,$onchange,array('Заполните поля'));
    echo "&nbsp;";
  }
  echo make_wrapper("Описание инцидента",make_appeal_form());
  break;
}
?>
