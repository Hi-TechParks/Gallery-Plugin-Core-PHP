<?php
//#######################################################################\\
//# Author: Christopher Schiffner                                       #\\
//# Filename: login.php                                                 #\\    
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

$action=$_GET['action'];

if(isset($_POST['kickmeto'])){
	$kickmeto=urldecode($_POST['kickmeto']);
}else if(isset($_GET['kickmeto'])){
	$kickmeto=urldecode($_GET['kickmeto']);
}

if( isset($_GET['vm']) ){
	$vm=$_GET['vm'];
}

if(trim($kickmeto)==""){
	$kickmeto=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
}

$unified_mode_operator=explode("/", $kickmeto);
$unified_mode_operator=$unified_mode_operator[count($unified_mode_operator)-1];
if(stristr($unified_mode_operator, "?")){
	$unified_mode_operator="&";
}else{
	$unified_mode_operator="?";
}
	switch($action){

		case 'login':
		
			include "../conf/users/users.inc.php";
			include "../conf/config.php";

			$user=strtolower(trim($_POST['username']));
			$pass=$_POST['password'];
			$cms=$_POST['cms'];
			$vm=$_POST['vm'];

			if((!$user) || (!$pass)){
			   header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php?error=fail&cms=$cms&vm=$vm&kickmeto=$kickmeto");
            } else if( ($users_array[$user]['username'] == $user) && ($users_array[$user]['password'] == crypt($pass, $users_array[$user]['password'])) ){

			    session_name($core_settings['session_identifier']);
			    session_start();

				if( isset($_SESSION['s_userName']) ){
					unset($_SESSION['s_userName']);
				}
				
                //set username session to acknowledge login
			    $_SESSION['s_userName']=strtolower(trim($users_array[$user]['username']));

			    if((isset($_GET['cms']) && $_GET['cms']==1 && $core_settings['unified_mode']==1) || (isset($_POST['cms']) && $_POST['cms']==1 && $core_settings['unified_mode']==1) || ($_SESSION['cms']==1 && $core_settings['unified_mode']==1) || $core_settings['unified_mode']==2){
				    $cms=1;
				    $_SESSION['cms']=1;
			    }else{
				    $cms=0;
				    $_SESSION['cms']=0;
			    }

			    if($vm=='ajax'){
			    	echo "<script type='text/javascript'>
					parent.PandaImageGallery.pageRefresh=1;
                                        parent.$.fn.colorbox.close();
                                  </script>";
			    }else{
			    	header("Location: http://".$kickmeto."{$unified_mode_operator}error=login&cms=$cms&vm=$vm");
			    }
			} else {
			   header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php?error=fail&cms=$cms&vm=$vm&kickmeto=$kickmeto");
			}

			break;

		default:
			echo "Your being naughty";

			break;
	}
?>
