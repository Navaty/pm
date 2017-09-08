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

$arr = opengoo_list_subprojects(4054);
echo "<pre>";

echo '<table cellpadding="0" cellspacing="0" border="0">';
$query1 = "SELECT
		     id, name
           FROM
		     `og_projects`
           WHERE
		     `p4` = 4054
";
$massiv = Array();
$res1 = ssql($query1); // Смотрим всег детей проекта Э-образование
$schetchik = 0;
foreach($res1 as $item1) { // Идем по каждому из них
	$query2 = "SELECT
			    object_id, workspace_id
        	   FROM
                            `og_workspace_objects`
	           WHERE
                            workspace_id = ".$item1['id']."
	           AND
        	            object_manager = 'ProjectTasks'
	";
	$res2 = ssql($query2); // Выбираем номера задач из каждого ребенка
	if(!empty($res2)) {
		foreach($res2 as $item2) { //Ищем каждую задачу и выбираем проблему, дату подачи заявки

/*			$query3 = "SELECT
        	        	            id
	        	           FROM
        	        	            `og_project_tasks`
	                	   WHERE
		                            id = ".$item2['object_id']."
                	           AND
                        	            (created_on
	        	           BETWEEN
        	        	            '2013-08-01 00:00:00'
                	           AND
                        	            '2014-01-21 23:59:59')

				   AND `completed_on` = '0000-00-00 00:00:00'
		        ";*/
                        $query3 = "SELECT
                                            id
                                   FROM
                                            `og_project_tasks`
                                   WHERE
                                            id = ".$item2['object_id']."
                                   AND
                                            DAY(created_on) >= 1
                                   AND
                                            MONTH(created_on) >= 8
                                   AND
                                            YEAR(created_on) = 2013

                        ";
//			echo $query3;
			$res3 = ssql($query3); // Запрашиваем данные 
			if(!empty($res3)) {
				foreach($res3 as $item3) {
					$massiv[count($massiv)]=$item3["id"];
				}
			}
		}
	}

}

var_dump($massiv);

$massiv = array_unique($massiv, SORT_NUMERIC);

var_dump($massiv);

foreach($massiv as $item) {
                        $query4 = "
                                        SELECT
                                                *
                                        FROM
                                                `og_object_properties`
                                        WHERE
                                                rel_object_id = ".$item."
                                        AND
                                                rel_object_manager = 'ProjectTasks'
			";
			echo $item.'<br />';
                        $firstname = '';
                        $name = '';
                        $phone = '';
                        $fathername = '';
                        $district = '';
                        $organization = '';
			$fio='';
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
					case "Контактный телефон":
                                                $phone = $item4["value"];
                                        break;
					case "Контактный_номер":
						$phone = $item4["value"];
					break;
                                        case "Отчество":
                                                $fathername = $item4["value"];
                                        break;
                                        case "Район":
                                                $district = $item4["value"];
                                        break;
                                        case "Район/Город":
                                                $district = $item4["value"];
                                        break;
					case "ФИО_пользователя":
                                                $fio = $item4["value"];
                                        break;
                                        case "ЛПУ":
                                                $organization = $item4["value"];
                                        break;
                                }
                        }
                        //      var_dump($res4);

                        $query5 = "
                                SELECT
                                        text
                                FROM
                                        `og_comments`
                                WHERE
                                        rel_object_id = ".$item."
                                AND
                                        rel_object_manager = 'ProjectTasks'
                        ";

                        $res5 = ssql($query5); //Смотрим все комментарии данной задачи
                //      echo count($res5);
                        $count = count($res5);
                        $comment = $res5[$count]["text"]; // Берем последний комментарий к задаче

			$query3 = "SELECT
                                    text, created_on
                           FROM
                                    `og_project_tasks`
                           WHERE
                                    id = ".$item."
	                ";

	                $res3 = ssql($query3); // Запрашиваем данные
			foreach($res3 as $item3) {
				$text = $item3["text"];
				$created_on = $item3["created_on"];
				$schetchik++;

/*				if($fio='') {
					$fio = $firstname.' '.$name.' '.$fathername;
				}*/

				echo '<tr><td>'.$schetchik.'</td><td>'.$item.'</td><td>'.$district.'</td><td>'.$organization.'</td><td>'.$created_on.'</td><td>'.$fio.$firstname.' '.$name.' '.$fathername.'</td><td>'.$phone.'</td><td>'.$text.'</td><td>'.$comment.'</td></tr>';
			}


}

echo '</table>';

?>
