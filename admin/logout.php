<?php
//#######################################################################\\
//# Author: Christopher Schiffner                                       #\\
//# Filename: logout.php                                                #\\    
//# Copyright: Christopher Schiffner, All Rights Reserved               #\\
//# Description: Image gallery software, view readme for more info.     #\\
//#                                                                     #\\
//# License: This software is free to use for personal applications.    #\\
//#          There is a small registration fee for commercial           #\\
//#          applications.  Please contact chris@schiffner.com if       #\\    
//#          you wish to use this program on a commercial website.      #\\          
//#######################################################################\\

//define the level of error reporting
//error_reporting(-1);
//ini_set('error_reporting', E_ALL);
error_reporting(0);

if(file_exists("../conf/auto_conf.php")){
	include "../conf/auto_conf.php";
	
	if(isset($auto_conf['base_url'])){
		$core_settings['base_url']=$auto_conf['base_url'];
	}
}
if(isset($auto_conf['wordpress_plugin'])){
	if($auto_conf['wordpress_plugin']==true){
		die("You're being naughty!");
	}
}

include '../conf/config.php';
$kickmeto=$_GET['kickmeto'];


if ($kickmeto){
	session_name($core_settings['session_identifier']);
	session_start();

	//detect and remember unified mode
	if((isset($_GET['cms']) && $_GET['cms']==1 && $core_settings['unified_mode']==1) || (isset($_POST['cms']) && $_POST['cms']==1 && $core_settings['unified_mode']==1) || ($_SESSION['cms']==1 && $core_settings['unified_mode']==1) || $core_settings['unified_mode']==2){
	        $cms=1;
	}else{
	        $cms=0;
	}
  
	session_unset();
	session_destroy();


	//re-establish unified mode after clearing our previous session.
    session_name($core_settings['session_identifier']);
    session_start();

	if($cms==1){
	        $_SESSION['cms']=1;       
	}else{
	        $_SESSION['cms']=0;  
	}  

	header("Location: http://".$kickmeto);
} else {
	echo "Your being naughty";
}
?>
