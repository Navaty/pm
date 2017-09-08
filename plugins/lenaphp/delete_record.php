<?php
include ("../db.inc.php");
include ("../functions.php");

$id = $_POST["id"];

$query = "DELETE FROM lena_incidents WHERE ID='$id'";
usql($query);
?>
