<?

function show_service($Par) {
  foreach($Par as $k=>$v) {
    if($v["r"] == "required") {
      echo "<b>".$k. "</b>:\n".$v["d"];
      echo "\n\t".$v["r"];
      echo "\n\t".$v["t"];
      
      echo "\n\t".$v["v"];
      echo "<hr/>";
    }
  }
}

echo "<pre>";

$edoc = $_REQUEST["edoc"];

$doc["toa"]["r"] = "not required";
$doc["toa"]["t"] = "array";
$doc["toa"]["d"] = "идентификатор подписанта (значение 1634081 - Контакт-центр государственных и муниципальных услуг Республики Татарстан (ЦИТ РТ))";
$doc["toa"]["v"] = "1634081";

$doc["out_number"]["r"] = "required";
$doc["out_number"]["t"] = "array";
$doc["out_number"]["d"] = "номер исходящего документа";
$doc["out_number"]["v"] = $edoc[""];

$doc["out_date"]["r"] = "maybe";
$doc["out_date"]["t"] = "array";
$doc["out_date"]["d"] = "дата документа (по умолчанию текущая дата)";
$doc["out_date"]["v"] = $edoc[""];

$doc["out_comments"]["r"] = "maybe";
$doc["out_comments"]["t"] = "array";
$doc["out_comments"]["d"] = "комментарий подписанта (по умолчанию пустая строка)";
$doc["out_comments"]["v"] = $edoc[""];
//количество элементов всех вышеперечисленных массивов должно быть одинаковым.
//Сейчас получается что во всех этих массивах может быть только один элемент, но тип данных - "array" остался сознательно для совместимости с СДО

$doc["prepaired_by"]["r"] = "not required";
$doc["prepaired_by"]["t"] = "int";
$doc["prepaired_by"]["d"] = "идентификатор исполнителя (значение 1634081 - Контакт-центр государственных и муниципальных услуг Республики Татарстан (ЦИТ РТ))";
$doc["prepaired_by"]["v"] = "1634081";

$doc["tor"]["r"] = "required";
$doc["tor"]["t"] = "array";
$doc["tor"]["d"] = "идентификаторы адресатов документа";
$doc["tor"]["v"] = "";

$doc["og_kind_id"]["r"] = "not required";
$doc["og_kind_id"]["t"] = "int";
$doc["og_kind_id"]["d"] = "вид обращения (значение 21 - Обращение)";
$doc["og_kind_id"]["v"] = "21";


$doc["og_lang_id"]["r"] = "maybe";
$doc["og_lang_id"]["t"] = "int";
$doc["og_lang_id"]["d"] = "идентификатор языка обращения (по умолчанию 1 - русский)";
$doc["og_lang_id"]["v"] = 1;

$doc["og_enclosure_id"]["r"] = "not required";
$doc["og_enclosure_id"]["t"] = "int";
$doc["og_enclosure_id"]["d"] = "приложения (значение - нет приложений)";
$doc["og_enclosure_id"]["v"] = "";

$doc["og_event_id"]["r"] = "not required";
$doc["og_event_id"]["t"] = "int";
$doc["og_event_id"]["d"] = "идентификатор события (значение - нет приложений)";
$doc["og_event_id"]["v"] = "";

$doc["og_social_group_id"]["r"] = "maybe";
$doc["og_social_group_id"]["t"] = "int";
$doc["og_social_group_id"]["d"] = "идентификатор соц. группы (по умолчанию 1 - нет установлена)";
$doc["og_social_group_id"]["v"] = "1";

$doc["og_privileged_id"]["r"] = "maybe";
$doc["og_privileged_id"]["t"] = "int";
$doc["og_privileged_id"]["d"] = "идентификатор льготного состава (по умолчанию 1 - нет установлена)";
$doc["og_privileged_id"]["v"] = "1";

$doc["og_status"]["r"] = "maybe";
$doc["og_status"]["t"] = "int";
$doc["og_status"]["d"] = "идентификатор результата рассмотрения (по умолчанию 1 - в стадии рассмотрения)";
$doc["og_status"]["v"] = "1";

$doc["og_og_date"]["r"] = "not required";
$doc["og_og_date"]["t"] = "string";
$doc["og_og_date"]["d"] = "дата направления обращения (значение - текущая дата)";
$doc["og_og_date"]["v"] = "";

$doc["og_file_number"]["r"] = "not required";
$doc["og_file_number"]["t"] = "string";
$doc["og_file_number"]["d"] = "поместить в дело (пустое значение)";
$doc["og_file_number"]["v"] = "";

$doc["sheet_count"]["r"] = "not required";
$doc["sheet_count"]["t"] = "string";
$doc["sheet_count"]["d"] = "кол-во листов, прил., экз. (значение '1+0+1')";
$doc["sheet_count"]["v"] = "";

$doc["document_kind"]["r"] = "not required";
$doc["document_kind"]["t"] = "int";
$doc["document_kind"]["d"] = "идентификатор вида документа (значение 29 - письмо граждан)";
$doc["document_kind"]["v"] = "29";

$doc["delivery_type"]["r"] = "not required";
$doc["delivery_type"]["t"] = "int";
$doc["delivery_type"]["d"] = "идентификатор вида доставки (значение 7 - интернет-приемная)";
$doc["delivery_type"]["v"] = "7";

$doc["short_content"]["r"] = "required";
$doc["short_content"]["t"] = "string";
$doc["short_content"]["d"] = "Краткое содержание";
$doc["short_content"]["v"] = "";

$doc["text"]["r"] = "maybe";
$doc["text"]["t"] = "string";
$doc["text"]["d"] = "текст документа (по умолчанию пустая строка)";
$doc["text"]["v"] = "";

$doc["urgent"]["r"] = "maybe";
$doc["urgent"]["t"] = "bool";
$doc["urgent"]["d"] = "срочный документ или нет (по умолчанию не срочный)";
$doc["urgent"]["v"] = "";

$doc["is_dsp"]["r"] = "not required";
$doc["is_dsp"]["t"] = "bool";
$doc["is_dsp"]["d"] = "ДСП (значение - нет)";
$doc["is_dsp"]["v"] = "";

$doc["og_subject_category"]["r"] = "maybe";
$doc["og_subject_category"]["t"] = "array";
$doc["og_subject_category"]["d"] = "идентификатор тематики (по умолчанию - не выбрана)";
$doc["og_subject_category"]["v"] = "";

$doc["og_subject"]["r"] = "maybe";
$doc["og_subject"]["t"] = "array";
$doc["og_subject"]["d"] = "идентификатор 'подтематики' (по умолчанию - не выбрана)";
$doc["og_subject"]["v"] = "";

$doc["og_fields"]["r"] = "maybe";
$doc["og_fields"]["t"] = "bool";
$doc["og_fields"]["d"] = "передаются ли Персональные данные граждан (по умолчанию false) (не обязательно передавать true, любое значение будет преобразовано в булевый тип)";
$doc["og_fields"]["v"] = false;

//$doc["  Если будет передан этот параметр, то необходимо передать так же данные хотя бы об одном гражданине
     $doc["oga_ln"]["r"] = "required";
     $doc["oga_ln"]["t"] = "array";
     $doc["oga_ln"]["d"] = "Имя";
     $doc["oga_ln"]["v"] = "";

     $doc["oga_fn"]["r"] = "required";
     $doc["oga_fn"]["t"] = "array";
     $doc["oga_fn"]["d"] = "Фамилия";
     $doc["oga_fn"]["v"] = "";

     $doc["oga_pn"]["r"] = "required";
     $doc["oga_pn"]["t"] = "array";
     $doc["oga_pn"]["d"] = "Отчество";
     $doc["oga_pn"]["v"] = " ";

     $doc["oga_region_id"]["r"] = "maybe";
     $doc["oga_region_id"]["t"] = "array";
     $doc["oga_region_id"]["d"] = "идентификатор региона (по умолчанию пустая строка)";
     $doc["oga_region_id"]["v"] = "";

     $doc["oga_country_id"]["r"] = "maybe";
     $doc["oga_country_id"]["t"] = "array";
     $doc["oga_country_id"]["d"] = "идентификатор страны (по умолчанию пустая строка)";
     $doc["oga_country_id"]["v"] = "";

$doc["oga_republic_id"]["r"] = "maybe";
$doc["oga_republic_id"]["t"] = "array";
     $doc["oga_republic_id"]["d"] = "идентификатор республики/области (по умолчанию пустая строка)";
     $doc["oga_republic_id"]["v"] = "";

     $doc["oga_republic_area_id"]["r"] = "maybe";
     $doc["oga_republic_area_id"]["t"] = "array";
     $doc["oga_republic_area_id"]["d"] = "идентификатор района республики/области (по умолчанию пустая строка)";
     $doc["oga_republic_area_id"]["v"] = "";


     $doc["oga_city_id"]["r"] = "maybe";
     $doc["oga_city_id"]["t"] = "array";
     $doc["oga_city_id"]["d"] = "идентификатор населенного пункта (по умолчанию пустая строка)";
     $doc["oga_city_id"]["v"] = "";
     
     $doc["oga_district_id"]["r"] = "maybe";
     $doc["oga_district_id"]["t"] = "array";
     $doc["oga_district_id"]["d"] = "идентификатор района населенного пункта (по умолчанию пустая строка)";
     $doc["oga_district_id"]["v"] = "";

     $doc["oga_author_type_id"]["r"] = "maybe";
     $doc["oga_author_type_id"]["t"] = "array";
     $doc["oga_author_type_id"]["d"] = "идентификатор типа автора (по умолчанию пустая строка)";
     $doc["oga_author_type_id"]["v"] = "";

     $doc["oga_street"]["r"] = "maybe";
     $doc["oga_street"]["t"] = "array";
     $doc["oga_street"]["d"] = "улица (по умолчанию пустая строка)";
     $doc["oga_street"]["v"] = "";

     $doc["oga_house"]["r"] = "maybe";
     $doc["oga_house"]["t"] = "array";
     $doc["oga_house"]["d"] = "дом (по умолчанию пустая строка)";
     $doc["oga_house"]["v"] = "";

     $doc["oga_building"]["r"] = "maybe";
     $doc["oga_building"]["t"] = "array";
     $doc["oga_building"]["d"] = "корпус (по умолчанию пустая строка)";
     $doc["oga_building"]["v"] = "";

     
     $doc["oga_apartment"]["r"] = "maybe";
     $doc["oga_apartment"]["t"] = "array";
     $doc["oga_apartment"]["d"] = "квартира (по умолчанию пустая строка)";
     $doc["oga_apartment"]["v"] = "";

     $doc["oga_zip"]["r"] = "maybe";
$doc["oga_zip"]["t"] = "array";
     $doc["oga_zip"]["d"] = "индекс (по умолчанию пустая строка)";
     $doc["oga_zip"]["v"] = "";

     $doc["oga_phone"]["r"] = "maybe";
     $doc["oga_phone"]["t"] = "array";
     $doc["oga_phone"]["d"] = "телефон (по умолчанию пустая строка)";
     $doc["oga_phone"]["v"] = "";

     $doc["oga_email"]["r"] = "maybe";
     $doc["oga_email"]["t"] = "array";
     $doc["oga_email"]["d"] = "email (по умолчанию пустая строка)";
     $doc["oga_email"]["v"] = "maybe";


show_service($doc);