<html>
<head>
<style>
	table tr td {
		border: 1px solid black;
		vertical-align:  top;
	}
</style>
</head>
<body>
<?php
include_once "statusage.php"; //by almaz - usage control

include ("db.inc.php");
include ("functions.php");

$arr = opengoo_list_subprojects(345);
echo "<pre>";

echo '<table style="border">';
$query1 = "SELECT
		     id, name
           FROM  
		     `og_projects` 
           WHERE  
		     `p4` = 345
";
$res1 = ssql($query1); // Смотрим всег детей проекта Э-образование
$schetchik = 0;
foreach($res1 as $item1) { // Идем по каждому из них
//	echo $item1["name"].' - '.$item1["id"].'<br />';
	$query2 = "SELECT
			    object_id, workspace_id
        	   FROM
                            `og_workspace_objects`
	           WHERE
                            workspace_id = ".$item1['id']."
	           AND
        	            object_manager = 'ProjectTasks'
	";
//	echo $query2.'<br />';
	$res2 = ssql($query2); // Выбираем номера задач из каждого ребенка

	foreach($res2 as $item2) { //Ищем каждую задачу и выбираем проблему, дату подачи заявки

//		echo 'Проект: '.$item1["name"].' Номер задачи: '.$item2["object_id"].'<br />';

		$query3 = "SELECT
                	            id, text, created_on
	                   FROM
        	                    `og_project_tasks`
                	   WHERE
	                            id = ".$item2['object_id']."
                           AND
                                    DAY(created_on) >= 1
        	           AND
                	            MONTH(created_on) >= 8
                           AND
                                    YEAR(created_on) >= 2013 
	        ";

//		echo $query3.'<br />';
		$res3 = ssql($query3); // Запрашиваем данные 
//		var_dump($res3);
		foreach($res3 as $item3) {
/*		foreach($res3 as $item3) {
			echo 'Задача номер: '.$item3["id"].'<br />Text: '.$item3["text"];
		}*/

			$query4 = "
					SELECT
						*
					FROM
						`og_object_properties`
					WHERE
						rel_object_id = ".$item3['id']."
					AND
						rel_object_manager = 'ProjectTasks'
			";
			$firstname = '';
			$name = '';
			$phone = '';
			$fathername = '';
			$district = '';
			$organization = '';
			$res4 = ssql($query4); //Смотрим доп. поля данной задачи
			foreach($res4 as $item4) {
				switch($item4["name"]) {
					case "Фамилия":
						$firstname = $item4["value"];
					break;
					case "Имя":
						$name = $item4["value"];
					break;
	                        	case "Контактный_телефон":
						$phone = $item4["value"];
	                	        break;
        	                	case "Отчество":
						$fathername = $item4["value"];
        	        	        break;
                	        	case "Район":
						$district = $item4["value"];
		                        break;
        		                case "Организация":
						$organization = $item4["value"];
                        		break;
				}
			}
			//	var_dump($res4);

			$query5 = "
				SELECT 
					text
				FROM
					`og_comments`
				WHERE
				        rel_object_id = ".$item3['id']."
		        	AND
	                                rel_object_manager = 'ProjectTasks'
			";

			$res5 = ssql($query5); //Смотрим все комментарии данной задачи
		//	echo count($res5);
			$count = count($res5);
		//	var_dump($res5[$count]["text"]);
		
		//	if(!empty)
			$comment = $res5[$count]["text"]; // Берем последний комментарий к задаче
			
//			echo '<br />Comment is: '.$comment.'<br /><br /> ------------------------------------------------------------ <br /><br />';
			$schetchik++;
			echo '<tr><td>'.$schetchik.'</td><td>'.$item3["id"].'</td><td>'.$district.'</td><td>'.$organization.'</td><td>'.$firstname.' '.$name.' '.$fathername.'</td><td>'.$phone.'</td><td>'.$comment.'</td></tr>';
		}
	}

}
echo '</table>';
?>
