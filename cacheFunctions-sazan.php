<?php
function sazanCacheClear($Str)
{
    $cacheDir = '/fengoffice/cachedWS';
    $files = glob($cacheDir . '/*'); // get all file names
    $cachedFileLog = $cacheDir . '/_sazanCacheClear.log';
    foreach ($files as $file) { // iterate files
        if ($file != '/fengoffice/cachedWS/_sazanCacheClear.log') {
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }
    }
    file_put_contents($cachedFileLog, "sazanCacheClear = $Str\n", FILE_APPEND); // кодируем и создаем заново локальный кеш
}

function sazanCacheTime($ServiceName, $Criteria, $String)
{
    $sessionID = $_REQUEST['PHPSESSID'];
    $crit = md5(serialize($Criteria));
    $requestName = $ServiceName . "-time-" . $sessionID . '-' . $crit;
    $dirname = dirname(__FILE__);
    $cacheDir = $dirname . '/cachedWS';
    $cachedFilePath = $cacheDir . "/" . $requestName;
    file_put_contents($cachedFilePath, $String . "\n", FILE_APPEND); // кодируем и создаем заново локальный кеш
}

function sazanIsCached($ServiceName, $Criteria, $Seconds)
{
    $sessionID = $_REQUEST['PHPSESSID'];
    $crit = md5(serialize($Criteria));
    $requestName = $ServiceName . "-" . $sessionID . '-' . $crit;
    $dirname = dirname(__FILE__);
    $seconds = $Seconds; // ; //one day // 20 minut// час
    $cacheDir = $dirname . '/cachedWS';
    $cachedFilePath = $cacheDir . "/" . $requestName;

    if (
        !file_exists($cachedFilePath) // если не существует данный КЕШ
        ||
        (filemtime($cachedFilePath) < (time() - $seconds))  // если жизнь данного кеша меньше чем SECONDS
    ) {
        $res['status'] = false;
    } else { // значит кеш есть и он по требованиям актуальный
        $res['status'] = true;
        $res['result'] = unserialize(file_get_contents($cachedFilePath, 1));
    }
    return $res;
}

function sazanCache($ServiceName, $Criteria, $Data)
{
    $sessionID = $_REQUEST['PHPSESSID'];
    $crit = md5(serialize($Criteria));
    $requestName = $ServiceName . "-" . $sessionID . "-" . $crit;
    $dirname = dirname(__FILE__);
    $cacheDir = $dirname . '/cachedWS';
    $cachedFilePath = $cacheDir . "/" . $requestName;

    //    file_put_contents($cachedFilePath, serialize($Data)); // кодируем и создаем заново локальный кеш
    //    $data = json_encode($Data);
    file_put_contents($cachedFilePath, serialize($Data));
    return true;
}