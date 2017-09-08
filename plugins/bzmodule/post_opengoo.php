<?
$predata = $_POST;
//if($predata["isname"] == "stis60") { echo "ERROR Press"; return false; }

if($predata["email_prefix"] && $predata["email_suffix"]) {
  $predata[Контактный_e-mail] = $predata["email_prefix"]."@".$predata["email_suffix"];
}
if($predata["appeal"] && $predata["answer"]) {
  $predata[appeal] = "Обращение гражданина:\n".$predata[appeal]."\n\nОтвет сотрудника ЦИТ:\n".$predata[answer];
}

$data = http_build_query($predata);

$file = 'etwas.txt';

file_put_contents($file, print_r($data, true));

//$file=fopen($filee,"w");
//fwrite($file,print_r($array, 1));
//fclose($file);

//$start = microtime(1);
//инициализируем сеанс
$curl = curl_init();
//уcтанавливаем урл, к которому обратимся
curl_setopt($curl,CURLOPT_URL,'http://pm2.citrt.net/plugins/opengooweb.php');
//выключаем вывод заголовков
curl_setopt($curl,CURLOPT_HEADER, 0);
//передаем данные по методу post
curl_setopt($curl,CURLOPT_POST, 1);
//теперь curl вернет нам ответ, а не выведет
curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($curl, CURLOPT_USERAGENT, 'Opera 10.00');
curl_setopt($curl,CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/5.0)');
curl_setopt($curl,CURLOPT_POSTFIELDS,$data);


$res = curl_exec($curl);
//проверяем, если ошибка, то получаем номер и сообщение
if(!$res){
  $error = curl_error($curl).'('.curl_errno($curl).')';
  echo $error;
  $ok = false;
} else{
  $ok = true;
  //echo $res.'<br />';
}
curl_close($curl);
//echo $diff = microtime(1) - $start;
echo $res;
?>

