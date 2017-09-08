<?php
//include_once "statusage.php"; //by almaz - usage control
include_once 'init.php';
include ("../functions.php");

$file = 'etwas333.txt';
//
file_put_contents($file, print_r($_REQUEST, true));


global $responsiblecolor;
$responsiblecolor = "lightyellow";

//echo "<pre>";
$fields = get_additional_fields();
$spheres = get_spheres();
$places = get_places();

$token = $_REQUEST["token"];
if ($token == "06350280") {
    $js_path = "http://cc2.citrt.net/oktell/js/";
}
?>
<script>
    <?php
include_once 'bzmodule.js';
 ?>
</script>
<div id="places" class="buttons">
    <?= show_places($places); ?>
</div>
<br/>
<fieldset class="ui-widgeta ui-widget-contenta">
    <legend>Регистрация инцидента</legend>
    <input type="hidden" id="opengoosphereid" name="projectid[Источник]"/>
<input type="hidden" name="title" value="Карта жителя">

    <div id="infomatplaces"></div>
    <div id="spheres"></div>
    <div id="services"></div>
    <div id="classificators"></div>
    <div id="sfields"></div>
    <div id="fields"></div>
    <div id="incidents_action"></div>
</fieldset>

print_r($token);
