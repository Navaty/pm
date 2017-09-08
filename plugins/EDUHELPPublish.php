<?php
include_once "statusage.php"; //by almaz - usage control
include("ajax.EDUHELP.php");

$commentid = $_REQUEST["commentid"];
$userid = $_REQUEST["userid"];
$button = "<input type='button' value='Опубликовать' style=\"position: relative; top: -9px; width: 103%;\" onclick=\"EDUHELPPublish(".$commentid.");\"/></div></body>";
$status = "Опубликовано";

if(eduhelp_iscommentpublished($commentid)) {
  $whattoshow = $status;
} else {
  if(eduhelp_ispermitted2publish($userid)) {
    $whattoshow = $button;
  }
}
?><html>
 <head>
  <script type="text/javascript" src="http://cc.citrt.net/oktell/js/jquery.min.js"></script>
  <script>
   function EDUHELPPublish(CommentID) {
   if (confirm('Вы уверены в том, что хотите опубликовать на портале тех.поддержки???')) {
     if (confirm('Это последнее предупреждение! Вы точно уверены???')) {
       $.ajax({
	 url: "ajax.EDUHELP.php",
	     dataType: "html",
	     data: {commentid: CommentID, send: "ok"},
	     success: function(data) {
	     if(data!="problem") {
	       $("#button").html(data);
	     } else {
	       alert("Проблема со сторонним сервером! Обращайтесь АДМИНИСТРАТОРУ!");
	     }
	   },
	     error: function(data) {
	     alert('Упс, какая то проблема! Звоните АДМИНИСТРАТОРУ!!!');
	   }
       })
     }
   }
 }
   </script>
   </head>
   <body><div id='button'><?=$whattoshow;?></div></body>
   </html>

