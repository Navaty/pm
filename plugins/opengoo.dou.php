<?
include_once "statusage.php"; //by almaz - usage control
include ("db.inc.php");

include ("functions.php");
include ("module_statistics.php");
?>
<html>
  <head>
    <script type="text/javascript" src="http://cc.citrt.net/oktell/js/jquery.min.js"></script> 
    <script type="text/javascript" src="http://cc.citrt.net/oktell/js/jquery-ui.custom.min.js"></script> 
    <script type="text/javascript" src="http://cc.citrt.net/oktell/js/jquery.ui.datepicker-ru.js"></script> 
    <script type="text/javascript" src="http://cc.citrt.net/oktell/js/jquery-ui-timepicker-addon.js"></script> 
    <script type="text/javascript" src="http://cc.citrt.net/oktell/js/javascripts.js"></script> 
    <link href="http://cc.citrt.net/oktell/css/default/jquery-ui-1.8.7.custom.css" type="text/css" rel="stylesheet"/> 
    <style>
      .row2 { background-color: #E6E6E6;}
      .level1 {   font-size: 15px; font-weight: bold;}
      .level2 {   font-size: 14px; padding-left: 10px; font-weight: bold;}
      .level3 {   font-size: 13px; padding-left: 30px;}
      .level4 {   font-size: 12px; padding-left: 50px;}
      .level5 {   font-size: 11px; padding-left: 70px;}
      
      h1, h2,h3,h4,h5 {
      margin: 0px;
      }
      
    </style>
  </head>
  <body>
    <?#print_r($_REQUEST);?>
    <form action="http://pm.citrt.net/plugins/opengoo.statistics.php" method="POST">
      <table cellpadding="5">
	<tr>
	  <td>Выбрать дату с</td>
	  <td><input type="text" name="starttime" value="<?=$starttime;?>" class="datepicker"/></td>
	  <td>по</td>
	  <td><input type="text" name="endtime" value="<?=$endtime;?>"  class="datepicker"/></td>
	</tr>
	<tr>
	  <td colspan="1"></td>
	  <td>
	  </td>
	  <td><input type="submit" value="Сформировать отчет"/></td>
	</tr>	
      </table>
      <br/><hr/>
      <b>Статистика сформирована по линии ДОУ  <?=$period;?><br/>
	время формирования статистики - <?=date("m.d.y")." ".date("H:i:s");?>
      </b>
      <br/><br/>
      <?
	 echo "<table border='1' cellpadding='5' cellspacing='0'>";
	 echo "<tr class='row2'>
	       <th>Направление</th>
     <th>Всего<br/>обращений</th>
	 </tr>";
	 $projectid = $_REQUEST["projectid"];
	 if(!$projectid) { $projectid = 747;}
	 echo show_tasks_per_project($projectid,1,$starttime,$endtime);
	 echo "<tr>
       <th>Итого</th>
       <th> $total </th>
	 </tr>";
	 echo "</table>";
	 mysql_close($con);
	 ?>
  </body>
</html>
