<?
include_once "statusage.php"; //by almaz - usage control
include("db.inc.php");
include("functions.php");
break;
exit;
die();

$districtsDISABLED =
 Array(
	'г.Казань, Авиастроительный район',
	'г.Казань, Вахитовский район',
	'г.Казань, Кировский район',
	'г.Казань, Московский район',
	'г.Казань, Ново-Савиновский район',
	'г.Казань, Приволжский район',
	'г.Казань, Совесткий район',
	'г.Набережные челны (Тукаевский район)',
	'Агрызский район',
	'Азнакаевский район',
	'Аксубавеский район',
	'Актанышский район',
	'Алексеевский район',
	'Алькеевский район',
	'Альметьевский район',
	'Апастовский район',
	'Арский район',
	'Атнинский район',
	'Бавлинский район',
	'Балтасинский район',
	'Бугульминский район',
	'Буинский район',
	'Верхнеуслонский район',
	'Высокогорский район',
	'Дрожжановский район',
	'Елабужский район',
	'Заинский район',
	'Зеленодольский район',
	'Кайбицкий район',
	'Камско-Устьинский район',
	'Кукморский район',
	'Лаишевский район',
	'Лениногорский район',
	'Мамадышский район',
	'Менделеевский район',
	'Мензелинский район',
	'Муслюмовский район',
	'Нижнекамский район',
	'Новошешминский район',
	'Нурлатский район',
	'Пестречинский район',
	'Рыбно-Слободский район',
	'Сабинский район',
	'Сармановский район',
	'Спасский район',
	'Тетюшский район',
	'Тукая район',
	'Тюлячинский район',
	'Черемшанский район',
	'Чистопольский район',
	'Ютазинский район');

function opengoo_temp_add_project($ProjectName,$Projects) {
  $sql = "INSERT INTO `og_projects` 
    (`name`, 
     `description`, `show_description_in_overview`,
     `completed_on`, `completed_by_id`, `created_on`, `created_by_id`, `updated_on`, `updated_by_id`,
     `color`, `parent_id`, 
     `p1`, `p2`, `p3`, `p4`, `p5`, `p6`, `p7`, `p8`, `p9`, `p10`)
VALUES
    ('$ProjectName',
     '','1',
     '0000-00-00 00:00:00','0',NOW(),'25',NOW(),'25',
     '18','0',
     $Projects)
";
    $projectid = usql($sql);
    return $projectid;
    // UPDATE og_projects SET P6 = id WHERE P6 = '999999'
  //return $sql;
}

function opengoo_temp_add_project_permission($ProjectID,$GroupID) {
 $sql = "
 INSERT INTO  `fengoffice`.`og_project_users` (`project_id` ,`user_id` ,`created_on` ,`created_by_id` ,`can_read_messages` ,`can_write_messages` ,`can_read_tasks` ,`can_write_tasks` ,`can_read_milestones` ,`can_write_milestones` ,`can_read_files` ,`can_write_files` ,`can_read_events` ,`can_write_events` ,`can_read_weblinks` ,`can_write_weblinks` ,`can_read_mails` ,`can_write_mails` ,`can_read_contacts` ,`can_write_contacts` ,`can_read_comments` ,`can_write_comments` ,`can_assign_to_owners` ,`can_assign_to_other`)
VALUES ( '$ProjectID',  '$GroupID',  NOW(),  '25',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1',  '1'
)";
 return usql($sql);

}

echo "<pre>";
// Посмотри куда и какая группа пользователей должен иметь доступ!
// http://pm.citrt.net/index.php?c=group&a=view_group&id=10000031
// http://pm.citrt.net/index.php?c=group&a=view_group&id=10000026
foreach($districts as $k=>$v) {
  echo "\n";
  $projectid = opengoo_temp_add_project($v,
					"'60','4043','4045','4054','4056','999999','0','0','0','0'"
					);
  if($projectid) {
    echo opengoo_temp_add_project_permission($projectid,'10000058');
    echo opengoo_temp_add_project_permission($projectid,'10000059');
    echo opengoo_temp_add_project_permission($projectid,'10000060');
    echo opengoo_temp_add_project_permission($projectid,'10000061');
    echo opengoo_temp_add_project_permission($projectid,'10000062');
    echo opengoo_temp_add_project_permission($projectid,'10000063');
  }
}
?>