<?php
include_once "statusage.php"; //by almaz - usage control
if($_REQUEST["passwd"]!='lil2016new') {
?>
<form method="post" action="lena.php">
<label>Пароль: </label>
<input type="text" name="passwd" />
<br />
<input type="submit" name="submit" value="Зайти"/>
</form>
<?
}
else {
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

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<script type="text/javascript" src="lenaphp/prettify.js"></script>
<script type="text/javascript" src="lenaphp/jquery.multiselect.js"></script>
<script type="text/javascript" src="lenaphp/jquery.multiselect.filter.js"></script>

<link rel="stylesheet" type="text/css" href="lenaphp/css.css" />

<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="lenaphp/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="lenaphp/jquery.multiselect.filter.css" />

</head>
<body>
<div id="edit"></div>
<div id="content"></div>
<form action="lena_incidents.php" id="lena_form">
<table style="vertical-align: top;" class="form_row">
<tr class="hide_tr">
<td style="vertical-align: top;">
<label>Порядок<sup style="color: red;">*</sup></label><br />
<?php 
$sql="SELECT MAX(Rank) as max FROM lena_incidents";
$res = ssql($sql);
foreach($res as $i_res) {
$max = (int) $i_res["max"];
$max++;
echo '<input type="text" name="rank[%%num%%]" value="'.$max.'"/>';
}
?>
<!--<input type="text" name="rank[%%num%%]" />-->
</td>
<td style="vertical-align: top;">
<label>Источник</label><br />
<select size="10" name="source[%%num%%][]" multiple style="width: 300px;">
<?php
foreach($places as $item) {
	echo "<option value='".$item["id"]."'>".$item["name"]."</option>\n";
}
?>
</select>
</td><td style="vertical-align: top;">
<label>Сфера + Услуга</label><br />
<select multiple size="10" name="service[%%num%%][]" style="width: 500px;">
<?php
	foreach($spheres as $spherev) {
        	$sphereid = $spherev["id"];
	        $spherename = $spherev["name"];
		echo "<optgroup label='&nbsp;&nbsp;".$spherename."'>";
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
		echo '</optgroup>';
	}
?>
</select>
</td><td style="vertical-align: top;">
<label>Инциденты</label><br />
<select multiple size="10" name="incidents[%%num%%][]" style="width: 500px;">
<?php
echo '<pre>';
//var_dump($spheres);
	foreach($spheres as $spherev) {
                $sphereid = $spherev["id"];
                $spherename = $spherev["name"];
		echo "<optgroup label='&nbsp;&nbsp;".$spherename."'>";
                $services = get_sphere_service($sphereid);
                foreach($services as $service) {
			$serviceid = $service["id"];
                        $servicename = $service["name"];
			echo "<optgroup label='&nbsp;&nbsp;&nbsp;&nbsp;".$servicename."'>";
			$sql = "SELECT cip.IncidentID AS IncidentID, op.Name FROM сс_incidents_projects AS cip, og_projects AS op WHERE  cip.IncidentID = op.id AND cip.SphereID = '$sphereid'	AND cip.ServiceID = '$serviceid' AND cip.IsDeleted = '0'";
			$res = ssql($sql);
			foreach($res as $res_item) {
				$incidentname = $res_item["Name"];
				$incidentsid = $res_item["IncidentID"];
				echo "<option value='".$sphereid."-".$serviceid."-".$incidentsid."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$incidentname."</option>\n";
			}
			echo '</optgroup>';
		}
		echo '</optgroup>';
        }
?>
</select>
</td><td style="vertical-align: top;">
<label>Исполнитель<sup style="color: red;">*</sup></label><br />
<select size="1" name="executor[%%num%%]" id="executor" style="width: 150px;">
<?php
	$sql = "SELECT id, name FROM og_companies ORDER BY name";
	$companies = ssql($sql);
	foreach($companies as $company) {
		$companyid = $company["id"];
		$companyname = $company["name"];
		$sql = "SELECT id, display_name as name FROM og_users WHERE company_id='$companyid' ORDER BY display_name";
		$persons = ssql($sql);
		if(count($persons)>0) {
                	echo "<optgroup label='&nbsp;".$companyname."'>";
			foreach($persons as $person) {
				$personid = $person["id"];
				$personname = $person["name"];
				if($personid=='51') $selected="selected"; else $selected="";
				echo "<option value='".$personid."' ".$selected.">&nbsp;&nbsp;&nbsp;&nbsp;".$personname."</option>\n";
			}
			echo '</optgroup>';
		}
	}
?>
</select>
</td><td style="vertical-align: top;">
<label>Подписчики</label><br />
<select multiple size="10" name="readers[%%num%%][]" id="readers" style="width: 240px;">
<?php
        $sql = "SELECT id, name FROM og_companies  ORDER BY name";
        $companies = ssql($sql);
        foreach($companies as $company) {
                $companyid = $company["id"];
                $companyname = $company["name"];
                $sql = "SELECT id, display_name as name FROM og_users WHERE company_id='$companyid' ORDER BY display_name";
                $persons = ssql($sql);
                if(count($persons)>0) {
                        echo "<optgroup label='&nbsp;".$companyname."'>";
                        foreach($persons as $person) {
                                $personid = $person["id"];
                                $personname = $person["name"];
                                echo "<option value='".$personid."'>&nbsp;&nbsp;&nbsp;&nbsp;".$personname."</option>\n";
                        }
			echo "</optgroup>";
                }
        }
?>
</select><br />
</td>
<td style="vertical-align: top;">
<label>Комментарий<sup style="color: red;">*</sup></label><br />
<textarea name="comment[%%num%%]" cols="20" rows="10"></textarea>
</td>
<td style="vertical-align: top;">
<input type="checkbox" name="active[%%num%%]" value="1" checked>Активна
</td>
</tr>
</table>
</form>
<button id="clickme">+</button>
<button id="submitForm">Отправить</button>
<script>
	$('#content').load("lenaphp/lenatable.php");
	var block = $('.form_row').html();
	var counter = 1;
	$('.hide_tr').css("display","none");
	$('.form_row').append(block.replace(/%%num%%/g, counter++));
	$('.form_row').css("display", "block");
	$('#clickme').click(function(){
		$('.form_row').append(block.replace(/%%num%%/g, counter++));
	});
	var form = $('#lena_form');
	$('#submitForm').click(function(){
        if(confirm("Вы точно хотите назначить исполнителя?")) {
		flag=true;
		i=1;
		j=1;
		while($('input[name="rank[' + i + ']"]').length) {
			if($('input[name="rank[' + i + ']"]').val()=='') {
				alert('Поле "Порядок" должно быть заполнено во всех строках!');
				flag=false;
			}
                        if($('textarea[name="comment[' + i + ']"]').val()=='') {
                                alert('Поле "Комментарий" должно быть заполнено во всех строках!');
                                flag=false;
                        }
			if(!flag) break;
			i++;
		}

		if(flag) {
			$.ajax({
				url: form.attr('action'),
				type: "POST",
				dataType: "json",
				data: form.serialize(),
				success: function(json) {
					alert('Исполнитель назначен');
					$('#content').load("lenaphp/lenatable.php");
				},
				error: function(json) {
					alert('Ошибка. Исполнитель не назначен');
				}
			});
		}
	}
	});

	$('#editForm').live("click", function()	{
	editform = $('#editLenaform');
	if(confirm("Вы точно хотите применить изменения?")) {
                flag=true;
                if($('input[name="editrank"]').val()=='') {
                	alert('Поле "Порядок" должно быть заполнено во всех строках!');
                        flag=false;
                }
                if($('textarea[name="comment"]').val()=='') {
                	alert('Поле "Комментарий" должно быть заполнено во всех строках!');
                        flag=false;
                }
		
                if(flag) {
                        $.ajax({
                                url: editform.attr('action'),
                                type: "POST",
                                data: editform.serialize(),
                                success: function(msg) {
					alert('Исполнитель отредактирован');
					$('#edit').css("display", "none");
                                        $('#content').load("lenaphp/lenatable.php");
					
				},
                                error: function() {
                                        alert('Ошибка. Исполнитель не отредактирован');
                                }
                        });
                }
        }
	return false;
        });

	$('.edit').live("click", function(){
		n = $(this).attr('value');
		$('#edit').css("display", "block");
		$('#edit').load("lenaphp/lenaedittable.php?id="+n);
	});

	$('#closeForm').live("click", function() {
		$('#edit').css("display", 'none');
	});

	$('.delete').live("click", function(){
		if(confirm("Вы точно хотите удалить запись?")) {
			id = $(this).attr('value');
			$.ajax({
				url: "lenaphp/delete_record.php",
				type: "POST",
				data: ({id: id}),
				success: function() {
				        $('#content').load("lenaphp/lenatable.php");
				},
				error: function() {
					alert('Не удалено');
				}
			});
		}
	});
        //Если нужно сделать фильтр по содержимому select - убрать комментирование нижней строки
	//$("select").multiselect().multiselectfilter();
</script>
</body>
</html>
<?php } ?>
