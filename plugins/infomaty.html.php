<!DOCTYPE html> 
<?
include_once "statusage.php"; //by almaz - usage control

include("db.inc.php");
include("functions.php");

 $oktellphonenumber = $_REQUEST["phonefrom"];

 $xml = file_get_contents("config.xml");
 $xmlObj = simplexml_load_string($xml);
 $data = objectsIntoArray($xmlObj);

 $ogvs = opengoo_list_subprojects(506);

 $cities = opengoo_list_subprojects(507);
//print_r($infomats);
 $types    = opengoo_list_subprojects(508);

//echo "<pre>";print_r($data);
?>
<html> 
<head> 
  <meta charset="UTF-8" /> 
  <title></title>

  <link rel="stylesheet" href="css/redmond/jquery-ui-1.8.5.custom.css" type="text/css" media="all" /> 
  <script src="js/jquery-1.4.2.min.js" type="text/javascript"></script>
  <script src="js/jquery-ui-1.8.5.custom.min.js" type="text/javascript"></script> 
  <script src="js/jquery.ui.datepicker-ru.js" type="text/javascript"></script> 

<style>
  body { font-size: 100%;font-family: arial;}
//  label, input { display:block; }
  input.text { margin-bottom:12px; width:200px; padding: .4em; }
  fieldset { padding:0; border:0; margin-top:15px; }
  h1 { font-size: 1.2em; margin: .6em 0; }
  td { font-size: 0.9em; font-weight: bold;}
/*  div { width: 450px; margin: 20px 0; }
  div table { margin: 1em 0; border-collapse: collapse; width: 100%; }
  div table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
*/
  .ui-dialog .ui-state-error { padding: .3em; }
  .validateTips { border: 1px solid transparent; padding: 0.3em; }
  </style>

</head>

<body>
  <form 
     action="http://pm.citrt.net/plugins/opengoowebtest.php" 
     aaction="stis.php" 
     method="post" id="formappeal" name="formappeal">

    <input type="hidden" id="isname" name="isname" value="infomatincidents"/>
    <table width=50%>
      <tr>
	<td>Учреждение представляющее услугу</td>
	<td><select id="ogvs" name="data[Учреждение]" class="text ui-widget-content ui-corner-all"></select></td>
      </tr>
      <tr>
	<td>Услуга</td>
	<td><select id="services" name="data[Услуга][]" class="text ui-widget-content ui-corner-all" multiple=multiple size=5></select></td>
      </tr>
      <tr>
	<td>Тип обращения</td>
	<td><select id="types" name="data[Тип_обращения][]" class="text ui-widget-content ui-corner-all" multiple=multiple size=5>
            <?
               foreach($types as $k=>$v) {
            $typename = $v["name"];
            echo '<option class="text" value="'.$typename.'">'.$typename.'</option>';
            }
            ?>
	</select></td>
      </tr>
      <tr>
	<td>Инфоматы</td>
	<td><select id="infomats" name="data[Инфомат]" class="text ui-widget-content ui-corner-all">
	    <?
foreach($cities as $k=>$v) {
  $cityname = $v["name"];
  $citydesc = $v["description"];
  echo '<optgroup label="'.$cityname.'" title="'.$citydesc.'">'."\n";
  $districts = opengoo_list_subprojects($v["id"]);
  if(is_array($districts)) {
    foreach($districts as $dk=>$dv) {
      $districtname = $dv["name"];
      $districtdesc = $dv["description"];
      echo "\t\t".'<optgroup label="&nbsp;&nbsp;'.$districtname.'" title="'.$districtdesc.'">'."\n";
      $infomats = opengoo_list_subprojects($dv["id"]);
      if(is_array($infomats)) {
        foreach($infomats as $ik=>$iv) {
	  $infomatname = $iv["name"];
          $infomatid = $iv["id"];
	  $infomatdesc = $iv["description"];
          echo "\t\t\t".'<option class="text" value="'.$infomatid.'" title="'.$infomatdesc.'">'.$infomatname.'</option>'."\n";
        }
      }
    echo "\t\t</optgroup>\n";
    }
  echo "\t\t\t</optgroup>";
  }

}
		
	    ?>
	  </select><input type="checkbox" id="allinfomats" name="allinfomats"/> <label for ="allinfomats">Все инфоматы</label>
	</td>
      </tr>
      <tr>
	<td>Дата и время инцидента</td>
	<td><input type="text" name="data[Дата_инцидента]" id="incidentdate" class="text ui-widget-content ui-corner-all" style="width:80px"/> <input type="text" name="data[Время_инцидента]" id="incidenttime" class="text ui-widget-content ui-corner-all" style="width:70px"/> 
</td>
      </tr>
      <tr>
	<td>ФИО плательщика</td>
	<td><input type="text" name="data[Ф.И.О]" id="appealedfio" class="text ui-widget-content ui-corner-all"/></td>
      </tr>
      <tr>
	<td>Контактный телефон плательщика(дополнительно)</td>
	<td><input type="text" name="data[Контакты]" id="appealedposition" class="text ui-widget-content ui-corner-all"/></td>
      </tr>
      <tr>
	<td>Телефон</td>
	<td><input type="text" name="data[Телефон]" id="appealedphone" value="<?=$oktellphonenumber;?>" class="text ui-widget-content ui-corner-all"/></td>
      </tr>
      <tr>
	<td>Э-почта обратившегося</td>
	<td><input type="text" name="data[Э-почта]" id="appealedemail" class="text ui-widget-content ui-corner-all" /></td>
      </tr>
      <tr>
	<td colspan="2"><hr/></td>
      </tr>
      <tr>
	<td>Когда нужно отдать распечатанные А4</td>
	<td><input type="text" name="data[Время_выдачи_документов]" id="orgaddress" class="text ui-widget-content ui-corner-all"/></td>
      </tr>
      <tr>
	<td>Общая сумма оплаты</td>
	<td><input type="text" name="data[Общая_сумма_оплаты]" id="orgaddress" class="text ui-widget-content ui-corner-all"/></td>
      </tr>
      <tr>
	<td>Кол-во штрафов</td>
	<td><input type="text" name="data[Количество_штрафов]" id="orgaddress" class="text ui-widget-content ui-corner-all"/></td>
      </tr>
      <tr>
	<td>Услуга (если ГИБДД-количество штрафов и какими купюрами)</td>
	<td><input type="text" name="data[ГИБДД]" id="orgaddress" class="text ui-widget-content ui-corner-all"/></td>
      </tr>
      <tr>
	<td>Сумма сдачи</td>
	<td><input type="text" name="data[Сумма_сдачи]" id="orgaddress" class="text ui-widget-content ui-corner-all"/></td>
      </tr>
      <tr>
	<td>Примечание</td>
	<td><textarea name="appeal" id="appeal" class="text ui-widget-content ui-corner-all" rows=5 cols=30></textarea></td>
      </tr>
      <tr>
	<td></td>
	<td><input type="submit" value="ОК" class="text ui-widget-content ui-corner-all"/></td>
      </tr>
    </table>
    
<!--
      <label for="">Когда нужно отдать распечатанные А4</label>
      <input type="text" name="data[Адрес_Организации]" id="orgaddress" class="text ui-widget-content ui-corner-all"/>

      <label for="orgaddress">Общая сумма оплаты</label>
      <input type="text" name="data[Адрес_Организации]" id="orgaddress" class="text ui-widget-content ui-corner-all"/>

      <label for="orgaddress">Кол-во штрафов</label>
      <input type="text" name="data[Адрес_Организации]" id="orgaddress" class="text ui-widget-content ui-corner-all"/>

      <label for="orgaddress">Услуга (если ГИБДД-количество штрафов и какими купюрами)</label>
      <input type="text" name="data[Адрес_Организации]" id="orgaddress" class="text ui-widget-content ui-corner-all"/>

      <label for="orgaddress">Сумма сдачи</label><input type="text" name="data[Адрес_Организации]" id="orgaddress" class="text ui-widget-content ui-corner-all"/>
-->
      <script>
	$(function() {
	$( "#incidentdate" ).datepicker( $.datepicker.regional[ "ru" ] );
	});
      </script>
    </fieldset>
  </form>

<script>
var ogvs_services = [
<?
 foreach($ogvs as $ok=>$ov) {
  $services = opengoo_list_subprojects($ov["id"]);
  if(is_array($services)) {

  $js .= "{\n";
  $js .= "\"ogvs\"\t:\t\"".$ov["name"]."\",\n";
  $js .= "\"services\"\t:\t[";
  foreach($services as $K=>$V) {
   $js .= "'".$V["name"]."',";
  }
  $js .= "]\n";
  $js .= "},\n";
  }

 }
echo $js;
?>];

$(function() {          // create an array to be referenced by state name
 ogv = [] ;
 for(var i=0; i<ogvs_services.length; i++) {
  ogv[ogvs_services[i].ogvs] = ogvs_services[i].services ;
 }
});

$(function() {
 // populate states select box
 var options = '' ;
 options = '<option>Выберите учреждение...</option>';
 for (var i = 0; i < ogvs_services.length; i++) {
  options += '<option value="' + ogvs_services[i].ogvs + '">' + ogvs_services[i].ogvs + '</option>'; 
 }
 $("#ogvs").html(options);   // populate select box with array

 // selecting state (change) will populate cities select box
 $("#ogvs").bind("change",
   function() {
    $("#services").children().remove() ;          // clear select box
    var options = '' ;
    for (var i = 0; i < ogv[this.value].length; i++) { 
     options += '<option value="' + ogv[this.value][i] + '">' + ogv[this.value][i] + '</option>'; 
    }
    $("#services").html(options);   // populate select box with array
   }            // bind function end
 );             // bind end 
});


$(function() {
			var availableTags = [
<?
for($h=0;$h<24;$h++) {
 if($h<10) { $hour = "0".$h;} else { $hour = $h;}
 for($m=0;$m<6;$m++) {
  $minute = $m*10;
  if($minute==0) { $minute = "00";} else { $minute = $m."0";}
  $tags .= "\"".$hour.":".$minute."\",\n";
 }
}
$tags .= '"утром","днем","вечером","ночью"';
?>
<?=$tags;?>
			];
			$( "#incidenttime" ).autocomplete({
			source: availableTags,
                        minLength: 1,
delay: 0
			});
			});

</script>

</body>
</html>
