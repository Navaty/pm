<?php
	error_reporting(E_ALL);
  if(!version_compare(phpversion(), '5.0', '>=')) {
    die('<strong>Installation error:</strong> in order to run Feng Office you need PHP5. Your current PHP version is: ' . phpversion());
  } // if
  if(!defined('PUBLIC_FOLDER')) {
    define('PUBLIC_FOLDER', 'public'); // this file can be included through public/index.php
  } // if
  require 'cacheFunctions-sazan.php';
  require 'init.php';
?>
