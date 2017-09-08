<?
include_once "statusage.php"; //by almaz - usage control

function aaa() {
  return 11;
}
function feedback_fieldtype($Name,$ID="",$FieldType,$DefaultValue="",$OnClick="") {
  switch ($FieldType) {
  case "districts":
    if(!$Name) { $Name = "kladr_district_id";}
    if(!$ID) { $ID = "kladr_district_id";}

    $html = "<select id=\"$ID\" name=\"$Name\" class='s-large'>
<option value=\"\">Нажмите для выбора</option>
<option value=\"Казань\">г.Казань</option>
<option value=\"Набережные Челны\">г.Набережные Челны</option>
<option value=\"Агрызский\">Агрызский</option>
<option value=\"Азнакаевский\">Азнакаевский</option>
<option value=\"Азнакаевский\">Аксубаевский</option>
<option value=\"Актанышский\">Актанышский</option>
<option value=\"Алексеевский\">Алексеевский</option>
<option value=\"Алькеевский\">Алькеевский</option>
<option value=\"Альметьевский\">Альметьевский</option>
<option value=\"Апастовский\">Апастовский</option>
<option value=\"Арский\">Арский</option>
<option value=\"Атнинский\">Атнинский</option>
<option value=\"Бавлинский\">Бавлинский</option>
<option value=\"Балтасинский\">Балтасинский</option>
<option value=\"Бугульминский\">Бугульминский</option>
<option value=\"Буинский\">Буинский</option>
<option value=\"Верхнеуслонский\">Верхнеуслонский</option>
<option value=\"Высокогорский\">Высокогорский</option>
<option value=\"Дрожжановский\">Дрожжановский</option>
<option value=\"Елабужский\">Елабужский</option>
<option value=\"Заинский\">Заинский</option>
<option value=\"Зеленодольский\">Зеленодольский</option>
<option value=\"Кайбицкий\">Кайбицкий</option>
<option value=\"Камско-Устьинский\">Камско-Устьинский</option>
<option value=\"Кукморский\">Кукморский</option>
<option value=\"Лаишевский\">Лаишевский</option>
<option value=\"Лениногорский\">Лениногорский</option>
<option value=\"Мамадышский\">Мамадышский</option>
<option value=\"Менделеевский\">Менделеевский</option>
<option value=\"Мензелинский\">Мензелинский</option>
<option value=\"Муслюмовский\">Муслюмовский</option>
<option value=\"Нижнекамский\">Нижнекамский</option>
<option value=\"Новошешминский\">Новошешминский</option>
<option value=\"Нурлатский\">Нурлатский</option>
<option value=\"Пестречинский\">Пестречинский</option>
<option value=\"Рыбно-Слободский\">Рыбно-Слободский</option>
<option value=\"Сабинский\">Сабинский</option>
<option value=\"Сармановский\">Сармановский</option>
<option value=\"Спасский\">Спасский</option>
<option value=\"Тетюшский\">Тетюшский</option>
<option value=\"Тукаевский\">Тукаевский</option>
<option value=\"Тукая\">Тукая</option>
<option value=\"Тюлячинский\">Тюлячинский</option>
<option value=\"Черемшанский\">Черемшанский</option>
<option value=\"Чистопольский\">Чистопольский</option>
<option value=\"Ютазинский\">Ютазинский</option>
</select>\n";
    break;
  case "text":
    $html = "\t\t<input id=\"$ID\" name=\"$Name\" value=\"$DefaultValue\" class='s-medium'>\n";
    break;
  }
  return $html;
}
?>