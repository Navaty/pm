<?php
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");
include ("functions.php");

function get_responsible($ProjectID) {
  $resposible = opengoo_get_project_role($ProjectID,"исполнитель");
  return $resposible;
}

function get_incident_fields($XML) {
  $xml_arr = xml2array($XML);
  //  print_r($xml_arr[xml][fieldtypes]);
  if($xml_arr[xml][fieldtypes]) {
    //    print_r($xml_arr);
    return $pieces = explode(",", $xml_arr["xml"]["fieldtypes"]);
  } else {
    return false;    echo "not array\n";
  }
  //  echo "\n\n";
}

function get_spheres() {
  $arr = opengoo_list_subprojects(948); //Сферы
  return $arr;
}
function get_additional_fields() {
  $arr = opengoo_list_subprojects(2568);
  foreach($arr as $v) {
    $xml = opengoo_get_projectdescription_by_projectID($v["id"]);
    $xml_arr = xml2array($xml);
    $id = $xml_arr["xml"]["id"];
    $name = $xml_arr["xml"]["name"];
    $comment = $xml_arr["xml"]["comment"];
    $data[$id]["idname"] = opengoo_get_project_name($v["id"],false);
    $data[$id]["name"] = $name;
    $data[$id]["comment"] = $comment;
  }
  return $data;
}
function get_places() {
  $arr = opengoo_list_subprojects(2554);
  return $arr;
}

function get_sphere_service($ProjectID) {
  $arr = opengoo_list_subprojects($ProjectID);
  return $arr;
}

$fields = get_additional_fields();
$spheres = get_spheres();
$places = get_places();
?>

<html>
<head>
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="lenaphp/css.css" />
</head>
<body>
<div id="result"></div>
<form action="lenasearch.php" id="lena_form">
<table style="vertical-align: top;" class="form_row">
<tr>
<td>
<label>Источник</label><br />
<select size="10" name="source" style="width: 300px;">
<?php
foreach($places as $item) {
	echo "<option value='".$item["id"]."'>".$item["name"]."</option>\n";
}
?>
</select>
</td><td>
<label>Сфера + Услуга</label><br />
<select size="10" name="service" style="width: 300px;">
<?php
	foreach($spheres as $spherev) {
        	$sphereid = $spherev["id"];
	        $spherename = $spherev["name"];
		echo "<optgroup label='&nbsp;&nbsp;".$spherename."'></optgroup>\n";
	        $services = get_sphere_service($sphereid);
        	$count_services = count($services);
		if($count_services==0) {
			$services[1]['name'] = 'НЕТ ДАННЫХ!';
			$services[1]['id'] = 0;
			$count_services = 1;
		}
		foreach($services as $service) {
			$serviceid = $service["id"];
			$servicename = $service["name"];
			echo "<option value='".$serviceid."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$servicename."</option>\n";
		}
	}
?>
</select>
</td><td>
<label>Инциденты</label><br />
<select size="10" name="incidents" style="width: 300px;">
<?php
echo '<pre>';
//var_dump($spheres);
	foreach($spheres as $spherev) {
                $sphereid = $spherev["id"];
                $spherename = $spherev["name"];
		echo "<optgroup label='&nbsp;&nbsp;".$spherename."'></optgroup>\n";
                $services = get_sphere_service($sphereid);
                foreach($services as $service) {
			$serviceid = $service["id"];
                        $servicename = $service["name"];
			echo "<optgroup label='&nbsp;&nbsp;&nbsp;&nbsp;".$servicename."'></optgroup>\n";
			$sql = "SELECT cip.IncidentID AS IncidentID, op.Name FROM сс_incidents_projects AS cip, og_projects AS op WHERE  cip.IncidentID = op.id AND cip.SphereID = '$sphereid'	AND cip.ServiceID = '$serviceid' AND cip.IsDeleted = '0'";
			$res = ssql($sql);
			foreach($res as $res_item) {
				$incidentname = $res_item["Name"];
				$incidentsid = $res_item["IncidentID"];
				echo "<option value='".$sphereid."-".$serviceid."-".$incidentsid."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$incidentname."</option>\n";
			}
		}
        }
?>
</select>
</td>
</tr>
</table>
</form>
<button id="submitForm">Искать</button>
<script>
	var form = $('#lena_form');
	$('#submitForm').click(function(){
        	$.ajax({
			url: 'lenaphp/testlenasearch.php',
			type: "POST",
			data: form.serialize(),
			success: function(msg) {
				$('#result').html(msg);
			},
			error: function() {
				alert('Ошибка. Исполнитель не назначен');
			}
		});
	});
</script>
</body>
</html>
