<?php 
if (defined('BOOKEASE_DB_CONFIG_LOADED')) {
  return;
}
define('BOOKEASE_DB_CONFIG_LOADED', true);

  $hname = 'mysql';          
  $uname = 'bookease_user';   
  $pass = 'bookease_pass';    
  $db = 'bookease_db';        

  $con = mysqli_connect($hname,$uname,$pass,$db);
  if(!$con){
    die("Cannot Connect to Database".mysqli_connect_error());
  }
