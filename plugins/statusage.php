<?
function stats() {
  $dir = "/fengoffice/plugins/statusage/";
  $filerequested = $_SERVER["SCRIPT_FILENAME"];
  $fileout = str_replace("/","|",$filerequested);
  $filename = $dir.$fileout;
  //  $filename = $dir.$filerequested;
  touch($filename);
}

stats();
