<?php
function file_get_contents2($Str) {
  $seconds = 30*60;
  $dir = "/fengoffice/cache";
  $filePath = $dir. "/".$Str;
  if(!file_exists($filePath)) { // если не существует данный КЕШ
    return false;
  }
  elseif(filemtime($filePath) < ( time() -  $seconds  )  ) { // если жизнь данного кеша меньше чем SECONDS
    return false;
  }
  else { // значит кеш есть и он по требованиям актуальный
    $fileContent = file_get_contents($filePath); // читаем локальный кеш
  }
  return $fileContent;
}
?>
