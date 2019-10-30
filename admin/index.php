<?php
//#######################################################################\\
//# Author: Christopher Schiffner                                       #\\
//# Filename: index.php                                                 #\\
//# Copyright: Christopher Schiffner, All Rights Reserved               #\\
//# Description: Image gallery software, s readme for more info.     #\\
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

ini_set ('default_charset', 'UTF-8');

if(!file_exists("../conf/config.php")){
    die("<strong>You must configure Panda Image Gallery! <br/><br/>Please review config.php.sample, configure as necessary,
        and rename config.php.sample to config.php. <br/><br/>See the README or installation notes for installation details.</strong>");
}else{
	include_once '../conf/config.php';
}

//benchmarking code
include_once '../includes/class/timer.php';
if($core_settings['benchmark']){
    //benchmark code
    $benchmark_timer = new Timer;
    $benchmark_timer->starttime();
}

include_once '../includes/class/loopvars.php';
include_once '../includes/version.php';
$loopvars = new clsLoopVars();
$theme_settings = new clsLoopVars();
include_once '../includes/class/panda_dir.php';
include_once '../includes/functions.php';

if(file_exists("../conf/auto_conf.php")){
	include "../conf/auto_conf.php";
	
	if(isset($auto_conf['base_url'])){
		$core_settings['base_url']=$auto_conf['base_url'];
	}
}

if(is_wordpress()){
	if(file_exists("../../../../wp-blog-header.php")){
		require_once("../../../../wp-blog-header.php");
		add_action('wp_print_scripts', 'WP_KILL_ALL_SCRIPTS', 100);
		add_action('wp_print_styles', 'WP_KILL_ALL_STYLES', 100);
	}
}

//the theme needs to know if we're integrated with wordpress. If we are we do not want to display some options.
if(is_wordpress()){
	$loopvars->set_var("wordpress", true);
	$core_settings['unified_mode']=2;
	$cms=1;
}else{
	$loopvars->set_var("wordpress", false);
}

$galleriesRoot='../galleries/';

@session_name($core_settings['session_identifier']);
@session_start();
$sessionid=session_id();

/* THEME */
$loopvars->set_var("theme_path", "../themes/".$core_settings['theme_name']);
include '../themes/'.$core_settings['theme_name'].'/theme.php';

$includeFlag='1'; //for later include detection

if((isset($_GET['cms']) && $_GET['cms']==1 && $core_settings['unified_mode']==1) || (isset($_POST['cms']) && $_POST['cms']==1 && $core_settings['unified_mode']==1) || ($_SESSION['cms']==1 && $core_settings['unified_mode']==1) || $core_settings['unified_mode']==2){
    $cms=1;
    $_SESSION['cms']=1;
    $target=" target='_top' ";
    $loopvars->set_var("link_target", " target='_top' "); 
    $backtogallerylink=$core_settings['base_url'];
    $chgPassPath=$core_settings['base_url'];
    $passwordRecoveryPath=$core_settings['base_url'].'?a=pr&cms=1';
    $cms_link="&cms=1";
    $cms_link2="?cms=1";
}else{
    $cms=0;
    $_SESSION['cms']=0;
    $backtogallerylink = "../index.php";
    $chgPassPath='../index.php';
    $passwordRecoveryPath='../index.php?a=pr';
    $cms_link="";
    $cms_link2="";
}

//detect wordpress login--fallback to regular login check
$logged_in=is_logged_in();

$styleSheetPath='../themes/'.$core_settings['theme_name'].'/';
$scriptsPath='../scripts/';
$sitemapURL='../sitemap.xml.php';
$galleryAdminSortOrder='';
$loopvars->set_var("gallery_listing_link", $backtogallerylink."?cms=$cms");
if(is_wordpress()){
	$loopvars->set_var("manage_link", '/wp-admin/admin.php?page=panda_image_gallery/wp_integration.php');
	$loopvars->set_var("manage_link_target", ' target=\'_top\' ');
}else{
	$loopvars->set_var("manage_link", 'index.php'.$cms_link2);
	$loopvars->set_var("manage_link_target", '');
}
$loopvars->set_var("login_link", 'index.php?kickmeto='.$kickmeto);
$loopvars->set_var("logout_link", 'logout.php?kickmeto='.urlencode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
$loopvars->set_var("password_recovery_link", $backtogallerylink."?a=pr&amp;cms=$cms");
$loopvars->set_var("registration_link", $backtogallerylink."?a=reg&amp;cms=$cms");
$loopvars->set_var("change_pass_link", "?action=chgpass");
$loopvars->set_var("edit_users_link", "?action=editusers");
$loopvars->set_var("edit_config_link", "?action=editconf");
$loopvars->set_var("current_url", $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$loopvars->set_var("current_url_linksafe", urlencode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
$loopvars->set_var("is_admin_page", true);

if(isset($_POST['galleryAdminSortOrder']))
    $_SESSION['s_galleryAdminSortOrder']=$_POST['galleryAdminSortOrder'];

//set default gallery sort order is none is selected
if(!isset($_SESSION['s_galleryAdminSortOrder'])){
        $_SESSION['s_galleryAdminSortOrder']="dateDESC";
}
$galleryAdminSortOrder=$_SESSION['s_galleryAdminSortOrder'];

$location='';
$error='';

$action='';
if(isset($_GET['action'])){
	$action=$_GET['action'];
	$loopvars->set_var("action", $_GET['action']);
}else if(isset($_POST['action'])){
	$action=$_POST['action'];
	$loopvars->set_var("action", $_POST['action']);
}

if(isset($_GET['error'])){
	$error=$_GET['error'];
}

if(isset($_GET['vm'])){
        $vm=$_GET['vm'];
}else{
	$vm='false';
}

$kickmeto='';
if(isset($_POST['kickmeto'])){
	$kickmeto=urlencode($_POST['kickmeto']);
}else if(isset($_GET['kickmeto'])){
	$kickmeto=urlencode($_GET['kickmeto']);
}else{
	$kickmeto=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php";
}
if(trim($kickmeto)==""){
	$kickmeto=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php";
}

if(isset($_SESSION['s_userName'])){
	$loopvars->set_var("current_user", $_SESSION['s_userName']);
}


if($theme_settings->get_var("enable_ajax")){
	$ajaxifythumb=" onClick='$(this).colorbox({width:\"600px\", height:\"500px\", iframe:true, next: false, title: \" \", previous: false, current: false, href:function(){ return this.href+\"&amp;vm=ajax\"; }, close: \"\" });' ";
	$ajaxifydelete=" onClick='$(this).colorbox({width:\"600px\", height:\"500px\", iframe:true, next: false, title:  \" \", previous: false, current: false, href:function(){ return this.href+\"&amp;vm=ajax\"; }, close: \"\", onClosed: function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"../includes/updating.html\", open: true, close: \"\" }); } PandaImageGallery.pageRefresh=0; } });' ";
	$ajaxifydeleteInput=" onClick='$(this).colorbox({width:\"600px\", height:\"500px\", iframe:true, next: false, title:  \" \", previous: false, current: false, href:function(){ return this.alt+document.deletefile.filetodelete.value+\"&amp;vm=ajax\"; }, close: \"\", onClosed: function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"../includes/updating.html\", open: true, close: \"\" }); } PandaImageGallery.pageRefresh=0; } });' ";
	$ajaxifysetthumbInput=" onClick='$(this).colorbox({width:\"600px\", height:\"500px\", iframe: true, next: false, title:  \" \", previous: false, current: false, href:function(){ return this.alt+document.setfile.filetoset.value+\"&amp;vm=ajax\"; }, close: \"\", onClosed: function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"../includes/updating.html\", open: true, close: \"\" }); } PandaImageGallery.pageRefresh=0; } });' ";
	$ajaxifycaptionInput=" onClick='$(this).colorbox({width:\"600px\", height:\"400px\", overlayClose: false, iframe:true, next: false, title: \" \", previous: false, current: false, href:function(){ return this.alt+document.setcaption.filetoset.value+\"&amp;vm=ajax\"; }, close: \"\", onClosed: function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"../includes/updating.html\", open: true, close: \"\" }); }PandaImageGallery.pageRefresh=0; } });' ";
	$ajaxifynewgallery=" onClick='$(this).colorbox({width:\"700px\", height:\"500px\", overlayClose: false, iframe:true, next: false, title:  \" \", previous: false, current: false, href:function(){ return this.href+\"&vm=ajax\"; }, close: \"\", onClosed:function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"../includes/updating.html\", open: true, close: \"\" }); }PandaImageGallery.pageRefresh=0; } });' ";
	$ajaxifyupload=" onClick='$(this).colorbox({width:\"650px\", height:\"500px\", iframe:true, next: false, title: \" \", previous: false, current: false, href:function(){ return this.href+\"&amp;vm=ajax\"; }, close: \"\", onClosed:function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"../includes/updating.html\", open: true, close: \"\" }); } PandaImageGallery.pageRefresh=0; } });' ";
	$loopvars->set_var("ajax_login_javascript_block", " onClick='$(this).colorbox({width:\"400px\", height:\"300px\", iframe:true, next: false, title: \" \", previous: false, current: false, href:function(){ return this.href+\"&vm=ajax\"; }, close: \"\", onClosed:function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"../includes/updating.html\", open: true, close: \"\" }); } PandaImageGallery.pageRefresh=0; } });' ");
}

$core_content_temp="";

//must be a valid user
if(isset($_SESSION['s_userName'])) {

	switch($action){

		case 'editusers':

			if( !can_admin() ){
				header("Location: http://".$kickmeto);
			}
			
			$ary_userlevels = array();
			$ary_userlevels[] = "admin";
			$ary_userlevels[] = "standard";
			
			$requestedUsername='';
			$userEmailAddress='';
			$usersFirstName='';
			$usersLastName='';
			$error='';
			
			if(isset($_GET['requestedUsername'])){
				$requestedUsername=urldecode($_GET['requestedUsername']);
			}
			if(isset($_GET['userEmailAddress'])){
				$userEmailAddress=urldecode($_GET['userEmailAddress']);
			}
			if(isset($_GET['usersFirstName'])){
				$usersFirstName=urldecode($_GET['usersFirstName']);
			}
			if(isset($_GET['usersLastName'])){
				$usersLastName=urldecode($_GET['usersLastName']);
			}
			if(isset($_GET['error'])){
				$error=$_GET['error'];
			}
			
			include "../conf/users/users.inc.php";
			
			$core_content_temp.="<div class='core_container admin_container'>
									<h1 class='admin_page_title'>Edit Users</h1>
									
									<div class='add_user_container'>";
									
			if( $error=="userPasswordReset" && isset($_SESSION['reset_user']) ){
				$core_content_temp.="<div id='add_user_notice' class='add_user_notice' style='display:none;'></div><div id='user_password_reset_notice' class='user_password_reset_notice'>
			      <p>A user passsword was reset. The login credentials are below.</p>
			      <p><br/>Username: <span>".$_SESSION['reset_user']['username']."</span><br/>Password: <span>".$_SESSION['reset_user']['password']."</span></p>
			      </div>";
			      
			    unset($_SESSION['reset_user']);
			    unset($_SESSION['new_user']);
			}else if( $error=="success" && isset($_SESSION['new_user']) ){
				$core_content_temp.="<div id='user_password_reset_notice' class='user_password_reset_notice' style='display:none;'></div><div id='add_user_notice' class='add_user_notice'>
			      <p>A new user was added. The login credentials are below.</p>
			      <p><br/>Username: <span>".$_SESSION['new_user']['username']."</span><br/>Password: <span>".$_SESSION['new_user']['password']."</span></p>
			      </div>";
			}else{
			
				switch($error){
					case 'emailTaken':
						$core_content_temp.="<div class='add_user_error'>ERROR: An account has already been registered using the supplied email address.</div>";
						break;
						
				    case 'userTaken':
						$core_content_temp.="<div class='add_user_error'>ERROR: The username you requested is not available.</div>";
						break;
					
					case 'userChar':
						$core_content_temp.="<div class='add_user_error'>ERROR: Your username contained illegal characters. Usernames may only contain letter A through Z and numbers 0 through 9. Spaces and special characters are not permitted.</div>";
						break;
						
				    case 'fieldBlank':
						$core_content_temp.="<div class='add_user_error'>ERROR: You left a field blank. ALL fields are mandatory.</div>";
						break;
			
				    case 'codeMismatch':
						$core_content_temp.="<div class='add_user_error'>ERROR: The verification code you entered was not correct.</div>";
						break;
				}
			}
			
			if( $error == "success" && isset($_SESSION['new_user']) ){
				$add_user_button_style = "";
				$add_user_form_style = "display: none;";
				unset($_SESSION['new_user']);
			}else if( $error != "" && $error != "userPasswordReset" ){
				$add_user_button_style = "display: none;";
				$add_user_form_style = "";
			}else{
				$add_user_button_style = "";
				$add_user_form_style = "display: none;";
			}
			
			$core_content_temp.="
			<a id='add_new_user_button' onclick='document.getElementById(\"add_user_form\").style.display = \"\"; this.style.display=\"none\"; document.getElementById(\"add_user_notice\").style.display = \"none\"; document.getElementById(\"user_password_reset_notice\").style.display = \"none\";' class='button' style='".$add_user_button_style."'>Add New User</a>
			
			<form id='add_user_form' class='add_user_form' method='post' action='./dosub.php' style='".$add_user_form_style."'>
				<input type='hidden' name='cms' value='{$cms}' />
				<input type='hidden' name='action' value='adduser' />
				
				<div class='add_user_form_line'>
					<label>Desired Username:</label>
					<input type='text' name='requestedUsername' value='{$requestedUsername}' />
				</div>
				
				<div class='add_user_form_line'>
					<label>First Name:</label>
					<input type='text' name='usersFirstName' value='{$usersFirstName}' />
				</div>
				
				<div class='add_user_form_line'>
					<label>Last Name:</label>
					<input type='text' name='usersLastName' value='{$usersLastName}' />
				</div>
				
				<div class='add_user_form_line'>
					<label>Email Address:</label>
					<input type='text' name='usersEmailAddress' value='{$userEmailAddress}' />
				</div>
				
				<div class='add_user_form_line'>
					<input type='submit' value='Add User' class='registrationButton' /><a onclick='document.getElementById(\"add_user_form\").style.display = \"none\"; document.getElementById(\"add_new_user_button\").style.display = \"\"; document.getElementById(\"add_user_notice\").style.display = \"none\";' class='button' style='".$add_user_button_style."'>Cancel</a>
				</div>
				
				</form>
				</div>";
				
				$core_content_temp.="<table class='user_table' cellspacing='0' cellpadding='0'>
									   <tr class='user_row'>
										<td class='user_row-narrow_item'><span>Username</span></td>
										<td class='user_row-narrow_item'><span>First Name</span></td>
										<td class='user_row-narrow_item'><span>Last Name</span></td>
										<td class='user_row-wide_item'><span>Email Address</span></td>
										<td class='user_row-narrow_item'><span>User Level</span></td>
										<td class='user_row-narrow_item'></td>
									   </tr>";
			
			$row_classer = "user_table_dark";
			
			foreach ($users_array as $key => $value) {
			
				$core_content_temp .= "<tr class='user_row ".$row_classer."'>
										<td class='user_row-narrow_item'>&nbsp;&nbsp;".$value["username"]."</td>
										<td class='user_row-narrow_item'>".$value["firstname"]."</td>
										<td class='user_row-narrow_item'>".$value["lastname"]."</td>
										<td class='user_row-wide_item'>".$value["email_address"]."</td>
										<td class='user_row-narrow_item'>".$value["userlevel"]."</td>
										<td class='user_row-narrow_item'><a class='button' onclick='document.getElementById(\"edit-".$key."\").style.display = \"\";'>edit</a><a class='button' onclick='if( confirm(\"Would you like to delete the user ".$value["username"]."?\") ){ location.href = \"dosub.php?action=deluser&usr=".$key."\"}'>delete</a><a class='button' onclick='if( confirm(\"Would you like to reset the password for the user ".$value["username"]."?\") ){ location.href = \"../index.php?a=resetuserpass&admin_reset=1&userEmailAddress=".$value["email_address"]."\"}'>reset password</a></td>
									   </tr>
									   
									   <tr class='user_row ".$row_classer."' id='edit-".$key."' style='display: none;'>
									   <td colspan='6'>
									   		<form  method='post' action='dosub.php?action=edituser&amp;vm={$vm}'>
									   			<table width='100%' cellspacing='0' cellpadding='0'>
									   			  <tr class='user_row'>
													<td class='user_row-narrow_item'>&nbsp;</td>
													<td class='user_row-narrow_item'><input type='text' name='firstname' value='".$value["firstname"]."' /></td>
													<td class='user_row-narrow_item'><input type='text' name='lastname' value='".$value["lastname"]."' /></td>
													<td class='user_row-wide_item'><input type='text' name='email_address' value='".$value["email_address"]."' /></td>
													<td class='user_row-narrow_item'>
														<select name='userlevel' >";
														
				foreach ($ary_userlevels as $sub_value) {
					
					if( $sub_value == $value["userlevel"] ){
						$selected_flag = "selected";
					}else{
						$selected_flag = "";
					}

					$core_content_temp .= "<option ".$selected_flag.">".$sub_value."</option>";
				}
													
													
				$core_content_temp .= "					</select>
									                </td>
													<td class='user_row-narrow_item'>
														<input type='hidden' name='usr' value='".$key."' />
														<input type='submit' value='save'><a class='button' onclick='document.getElementById(\"edit-".$key."\").style.display = \"none\";'>cancel</a>
													</td>
												  </tr>
												</table>
											</form>
									   </td>
									   </tr>";
									   
				if( $row_classer == "user_table_dark" ){
					$row_classer = "";
				}else{
					$row_classer = "user_table_dark";
				}
			}
			
			$core_content_temp .= " </table>
								 </div>";
			
			/* RUN THEME */
			//prepare_theme_vars();
			$core_content_temp=do_theme($core_content_temp, $loopvars, $theme_settings, $core_settings); //Call mail theme function. Pass all necessary vars.
			$loopvars->set_var("markup_output", $core_content_temp); //Store body content
			unset($core_content_temp); //free memory
				
			//make sure the theme is following the rules
			if(!$loopvars->get_var("get_head")){
				die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_head().</div>");
			}
			if(!$loopvars->get_var("get_footer")){
				die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_footer().</div>");
			}
			
			//echo body content
			$loopvars->echo_var("markup_output");
			
			break;

		case 'editconf':

			if( !can_admin() ){
				header("Location: http://".$kickmeto);
			}
			
			$core_content_temp.="<div class='core_container admin_container'>";
			
			$core_content_temp .= "<h1 class='admin_page_title'>Edit Settings</h1>";
			
			$core_content_temp.="</div>";
			
			/* RUN THEME */
			//prepare_theme_vars();
			$core_content_temp=do_theme($core_content_temp, $loopvars, $theme_settings, $core_settings); //Call mail theme function. Pass all necessary vars.
			$loopvars->set_var("markup_output", $core_content_temp); //Store body content
			unset($core_content_temp); //free memory
				
			//make sure the theme is following the rules
			if(!$loopvars->get_var("get_head")){
				die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_head().</div>");
			}
			if(!$loopvars->get_var("get_footer")){
				die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_footer().</div>");
			}
			
			//echo body content
			$loopvars->echo_var("markup_output");
			
			break;
				
		case 'deleteimage':

			$galleryname='';
			if(isset($_GET['galleryname'])){
				$galleryname=$_GET['galleryname'];
			}else{
				$galleryname=$_POST['galleryname'];
			}

			$filetodelete='';
			if(isset($_GET['filetodelete'])){
				$filetodelete=$_GET['filetodelete'];
			}else if(isset($_POST['filetodelete'])){
				$filetodelete=$_POST['filetodelete'];
			}
			
			if( $filetodelete == "" ){
				if(isset($_GET['filetoset'])){
					$filetodelete=$_GET['filetoset'];
				}else if(isset($_POST['filetoset'])){
					$filetodelete=$_POST['filetoset'];
				}
			}
			
			if( !can_edit($galleryname, $_SESSION['s_userName']) && !can_manipulate_image($galleryname, $filetodelete, $_SESSION['s_userName']) ){
				header("Location: http://$kickmeto");
				break;
			}
			
            $filetoset=html_entity_decode($filetodelete, ENT_QUOTES);

            //encoded file name
            $linksafe_file=urlencode(htmlentities($filetodelete, ENT_QUOTES));

			$completepath=$galleriesRoot.$galleryname."/".$filetodelete;

			get_gallery_details($galleryname);

			if($vm=='ajax'){
				$core_content_temp.="<!DOCTYPE html>
									 <html lang='en'>
									 <head>";
									 
				$core_content_temp.=get_core_head();
									 
				$core_content_temp.="</head>
									 </html>
									 <body>";
			}

			$core_content_temp.="<div class='core_container admin_container delete_media_page'>";
						
			/*if($vm=='ajax'){
				$core_content_temp.="<!DOCTYPE html>
									 <html lang='en'>
									 <head>";
									 
				$core_content_temp.=get_core_head();
									 
				$core_content_temp.="</head>
									 </html>
									 <body>";
			}*/

			//if($vm!='ajax'){
			$core_content_temp.="<h1 class='admin_page_title'>Delete Image</h1>
									 <div class='admin_gallery_details'>
										 <div class='admin_gallery_name'><span>Gallery Name:</span> {$loopvars->get_var("gallery_title")}</div>
										 <div class='admin_gallery_description'><span>Gallery Description:</span> {$loopvars->get_var("gallery_description")}</div>
										 <div class='admin_gallery_filename'><span>Image Name:</span> $filetodelete</div>
									 </div>";
			//}

			if(file_exists($galleriesRoot.$galleryname."/".$filetodelete)){


				if(is_movie($filetodelete)){
					$fileTypeTitle="movie";
				}else{
					$fileTypeTitle="image";
				}

				if(is_movie($filetodelete)){				

					$core_content_temp.="<img src='{$styleSheetPath}images/playMovie.png' border='0' class='action_image' alt='{$filetodelete}' />";
					$core_content_temp.="<div class='action_video_title'>$filetodelete</div>";

				}else{
					$imagedimensions = getimagesize($galleriesRoot.$galleryname."/".$filetodelete);
					if( (($imagedimensions[0] > $theme_settings->get_var("image_display_width")) && ($imagedimensions[0] > $imagedimensions[1])) ||
						(($imagedimensions[1] > $theme_settings->get_var("image_display_height")) && ($imagedimensions[1] > $imagedimensions[0])) ){
	
						if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
							$core_content_temp.="<<img src='../includes/view.php/".$galleryname."/lowres_".$filetodelete."' alt='{$filetodelete}' class='action_image' />";
						}else{
							$core_content_temp.="<img src='".$galleriesRoot.$galleryname."/lowres_".$filetodelete."' alt='{$filetodelete}' class='action_image' />";
						}
					}else{
						if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
							$core_content_temp.="<img src='../includes/view.php/".$galleryname."/".$filetodelete."' alt='{$filetodelete}' class='action_image' />";
						}else{
							$core_content_temp.="<img src='".$galleriesRoot.$galleryname."/".$filetodelete."' alt='{$filetodelete}' class='action_image' />";
						}
					}
				}

				$core_content_temp.="<div class='action_message'>Are you sure you want to <span>delete</span> this {$fileTypeTitle}?</div>";
				
				$core_content_temp.="<div class='action_buttons'>
									 <a href='dosub.php?action=confirmdeleteimage&galleryname={$galleryname}&filetodelete={$linksafe_file}&vm={$vm}&kickmeto={$kickmeto}' onClick='parent.PandaImageGallery.pageRefresh=1;' class='confirm button'>Delete Image</a>
									 <a onClick='parent.$.fn.colorbox.close();' href='http://".urldecode($kickmeto)."' class='cancel button'>Cancel</a>
									 </div>";

			}

			$core_content_temp.="</div>";
			
			if($vm!='ajax'){
				/* RUN THEME */
				//prepare_theme_vars();
				$core_content_temp=do_theme($core_content_temp, $loopvars, $theme_settings, $core_settings); //Call mail theme function. Pass all necessary vars.
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
				
				//make sure the theme is following the rules
				if(!$loopvars->get_var("get_head")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_head().</div>");
				}
				if(!$loopvars->get_var("get_footer")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_footer().</div>");
				}
				
			}else{
				$core_content_temp.=get_core_footer();
				$core_content_temp.="</body></html>";
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
			}
			
			//echo body content
			$loopvars->echo_var("markup_output");

			break;

		case 'setcaption':
                                    
			$galleryname='';
			if(isset($_GET['galleryname'])){
				$galleryname=$_GET['galleryname'];
			}else if(isset($_POST['galleryname'])){
				$galleryname=$_POST['galleryname'];
			}
                                    
			$filetoset='';
			if(isset($_GET['filetoset'])){
				$filetoset=$_GET['filetoset'];
			}else if(isset($_POST['filetoset'])){
				$filetoset=$_POST['filetoset'];
			}
			$filetoset=html_entity_decode($filetoset, ENT_QUOTES);

			if( !can_edit($galleryname, $_SESSION['s_userName']) && !can_manipulate_image($galleryname, $filetoset, $_SESSION['s_userName']) ){
				header("Location: http://$kickmeto");
				break;
			}
			
			$completepath=$galleriesRoot.$galleryname."/".$filetoset;
                        
			get_gallery_details($galleryname);            
                        
			if(file_exists($completepath.".php")){
				include $completepath.".php";
			}

			if($vm=='ajax'){
				$core_content_temp.="<!DOCTYPE html>
									 <html lang='en'>
									 <head>";
									 
				$core_content_temp.=get_core_head();
									 
				$core_content_temp.="</head>
									 </html>
									 <body>";
			}

			$core_content_temp.="<div class='core_container admin_container set_caption_page'>";
				
			//if($vm!='ajax'){
                                         
			$core_content_temp.="<h1 class='admin_page_title'>Set Image Caption</h1>
								 <div class='admin_gallery_details'>
								 	<div class='admin_gallery_name'><span>Gallery Name:</span> {$loopvars->get_var("gallery_title")}</div>
								 	<div class='admin_gallery_description'><span>Gallery Description:</span> {$loopvars->get_var("gallery_description")}</div>
								 	<div class='admin_gallery_description'><span>Image Name:</span> $filetoset</div>
								 </div>";


			if(is_movie($filetoset)){
                                                
				$core_content_temp.="<div style='position: relative; width: 100%; height: 100%'>
										<a style='padding: 0; margin: 0;'>
											<img src='{$styleSheetPath}images/playMovie.png' border='0' class='imageTiles' alt='{$filetoset}' />
										</a>
										<div class='videoTitle'>
											<a>$filetoset</a>
										</div>
									 </div>";
                                
			}else{
				$imagedimensions = getimagesize($galleriesRoot.$galleryname."/".$filetoset);
				if( (($imagedimensions[0] > $theme_settings->get_var("image_display_width")) && ($imagedimensions[0] > $imagedimensions[1])) ||(($imagedimensions[1] > $theme_settings->get_var("image_display_height")) && ($imagedimensions[1] > $imagedimensions[0])) ){                    
	                                        
					if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
						$core_content_temp.="<br/><img src='../includes/view.php/".$galleryname."/thm_".$filetoset."' alt='{$filetoset}' class='action_image' $ajaxcss /><br/>";
					}else{
						$core_content_temp.="<br/><img src='".$galleriesRoot.$galleryname."/thm_".$filetoset."' alt='{$filetoset}' class='action_image' $ajaxcss /><br/>";
					}
				}else{
					if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
						$core_content_temp.="<br/><img src='../includes/view.php/".$galleryname."/thm_".$filetoset."' alt='{$filetoset}' class='action_image' $ajaxcss /><br/>";
					}else{
						$core_content_temp.="<br/><img src='".$galleriesRoot.$galleryname."/thm_".$filetoset."' alt='{$filetoset}' class='action_image' $ajaxcss /><br/>";
					}
				}
			}


			$core_content_temp.="<form class='admin_form' method='post' action='dosub.php?action=saveimagecaption&amp;vm={$vm}' enctype='multipart/form-data' accept-charset='UTF-8'>
									<input type='hidden' name='galleryname' value='$galleryname' />
									<input type='hidden' name='filetoset' value='$filetoset' />
									<input type='hidden' name='kickmeto' value='$kickmeto' />
									
									<div class='admin_form_row'>
										<textarea id='image_caption' name='caption' cols='30' onKeyDown='limitText(this,{$theme_settings->get_var("max_description_length")});' onKeyUp='limitText(this,{$theme_settings->get_var("max_description_length")});'>".$image_options["caption"]."</textarea>
									</div>
									
									<div class='admin_form_row'>
										<input type='submit' value='Save Caption' class='save' onClick='if(parent.PandaImageGallery.overrideRefresh!=1){ parent.PandaImageGallery.pageRefresh=1; }' />
									<button onClick='parent.$.fn.colorbox.close();' type='submit' name='action' value='Cancel' formaction='http://".urldecode($kickmeto)."' class='button' >Cancel</button>
									</div>
									
								 </form>
							</div>";
               
			if($vm!='ajax'){
				/* RUN THEME */
				//prepare_theme_vars();
				$core_content_temp=do_theme($core_content_temp, $loopvars, $theme_settings, $core_settings); //Call mail theme function. Pass all necessary vars.
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
				
				//make sure the theme is following the rules
				if(!$loopvars->get_var("get_head")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_head().</div>");
				}
				if(!$loopvars->get_var("get_footer")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_footer().</div>");
				}
				
			}else{
				$core_content_temp.=get_core_footer();
				$core_content_temp.="</body></html>";
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
			}
			
			//echo body content
			$loopvars->echo_var("markup_output");
                        
			break;

		case 'setthumb':

			$galleryname='';
			if(isset($_GET['galleryname'])){
				$galleryname=$_GET['galleryname'];
			}else if(isset($_POST['galleryname'])){
				$galleryname=$_POST['galleryname'];
			}

			if( !can_edit($galleryname, $_SESSION['s_userName']) ){
				header("Location: http://$kickmeto");
				break;
			}
			
			$filetoset='';
			if(isset($_GET['filetoset'])){
				$filetoset=$_GET['filetoset'];
			}else if(isset($_POST['filetoset'])){
				$filetoset=$_POST['filetoset'];
			}
			$filetoset=html_entity_decode($filetoset, ENT_QUOTES);

			//encoded file name
			$linksafe_file=urlencode(htmlentities($filetoset, ENT_QUOTES));

			if(is_movie($filetoset)){
				if($vm!='ajax'){
						header("Location: http://".$kickmeto);
				}else{
					$core_content_temp.="<script type='text/javascript'>parent.$.fn.colorbox.close();</script>";
				}
			}

			$completepath=$galleriesRoot.$galleryname."/".$filetoset;

			get_gallery_details($galleryname);


			if($vm=='ajax'){
				$core_content_temp.="<!DOCTYPE html>
									 <html lang='en'>
									 <head>";
									 
				$core_content_temp.=get_core_head();
									 
				$core_content_temp.="</head>
									 </html>
									 <body>";
			}

			$core_content_temp.="<div class='core_container admin_container set_thumbnail_page'>";
			
			if($vm!='ajax'){
				$core_content_temp.="<h1 class='admin_page_title'>Set Gallery Thumbnail Image</h1>
									 <div class='admin_gallery_details'>
										 <div class='admin_gallery_name'><span>Gallery Name:</span> {$loopvars->get_var("gallery_title")}</div>
										 <div class='admin_gallery_description'><span>Gallery Description:</span> {$loopvars->get_var("gallery_description")}</div>
										 <div class='admin_gallery_filename'><span>Image Name:</span> $filetoset</div>
									 </div>";
			}
			
			if(file_exists($galleriesRoot.$galleryname."/".$filetoset)){

				$imagedimensions = getimagesize($galleriesRoot.$galleryname."/".$filetoset);
				if( (($imagedimensions[0] > $theme_settings->get_var("image_display_width")) && ($imagedimensions[0] > $imagedimensions[1])) || (($imagedimensions[1] > $theme_settings->get_var("image_display_height")) && ($imagedimensions[1] > $imagedimensions[0])) ){

					if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
						$core_content_temp.="<img src='../includes/view.php/".$galleryname."/lowres_".$filetoset."' alt='{$filetoset}' class='action_image' />";
					}else{
						$core_content_temp.="<img src='".$galleriesRoot.$galleryname."/lowres_".$filetoset."' alt='{$filetoset}' class='action_image' />";
					}
				}else{
					if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
						$core_content_temp.="<img src='../includes/view.php/".$galleryname."/".$filetoset."' alt='{$filetoset}' class='action_image' />";
					}else{
						$core_content_temp.="<img src='".$galleriesRoot.$galleryname."/".$filetoset."' alt='{$filetoset}' class='action_image' />";
					}
				}

				$core_content_temp.="<div class='action_message'>Are you sure you want to <span>make this the thumbnail</span>?</div>";
			
				$core_content_temp.="<div class='action_buttons'>
										 <a href='dosub.php?action=confirmsetthumb&galleryname={$galleryname}&filetoset={$linksafe_file}&vm={$vm}&kickmeto={$kickmeto}' onClick='parent.PandaImageGallery.pageRefresh=1;' class='confirm button'>Set Thumbnail Image</a>
										 <a onClick='parent.$.fn.colorbox.close();' href='http://".urldecode($kickmeto)."' class='cancel button'>Cancel</a>
										 </div>";
			}

			$core_content_temp.="</div>";
			
			if($vm!='ajax'){
				/* RUN THEME */
				//prepare_theme_vars();
				$core_content_temp=do_theme($core_content_temp, $loopvars, $theme_settings, $core_settings); //Call mail theme function. Pass all necessary vars.
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
				
				//make sure the theme is following the rules
				if(!$loopvars->get_var("get_head")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_head().</div>");
				}
				if(!$loopvars->get_var("get_footer")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_footer().</div>");
				}
				
			}else{
				$core_content_temp.=get_core_footer();
				$core_content_temp.="</body></html>";
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
			}
			
			//echo body content
			$loopvars->echo_var("markup_output");

			break;

		case 'editgallerydetails':

			$error=$_GET['error'];

			$galleryname='';
			if(isset($_GET['galleryname'])){
				$galleryname=$_GET['galleryname'];
			}else if(isset($_POST['galleryname'])){
				$galleryname=$_POST['galleryname'];
			}
			
			if(!can_create_gallery() && !can_edit($galleryname, $_SESSION['s_userName']) ){
				header("Location: http://".$kickmeto);
			}
			
			$completepath=$galleriesRoot.$galleryname."/".$filetoset;

			if($vm=='ajax'){
				$core_content_temp.="<!DOCTYPE html>
									 <html lang='en'>
									 <head>";
									 
				$core_content_temp.=get_core_head();
									 
				$core_content_temp.="</head>
									 </html>
									 <body>";
			}

			$core_content_temp.="<div class='core_container admin_container edit_gallery_details_page'>";
			
			if(file_exists($galleriesRoot.$galleryname)){

				//RETRIEVE GALLERY INFORMATION
				get_gallery_details($galleryname);

				if($loopvars->compare("gallery_sort_order", "") || !$loopvars->var_isset("gallery_sort_order")){
					$loopvars->set_var("gallery_sort_order", $core_settings['default_image_sort']);
				}

				if($vm!='ajax'){
					$core_content_temp.="<h1 class='admin_page_title'>Edit Gallery Details</h1>
										 <div class='admin_gallery_details'>
											<div class='admin_gallery_name'><span>Gallery Name:</span> {$loopvars->get_var("gallery_title")}</div>
											<div class='admin_gallery_description'><span>Gallery Description:</span> {$loopvars->get_var("gallery_description")}</div>
										 </div>";
				}else{
					$core_content_temp.="<h1 class='admin_page_title'>Edit Gallery Details</h1>";
				}
			}else{
				if($vm!='ajax'){
					$core_content_temp.="<h1 class='admin_page_title'>Create New Gallery</h1>";
				}else{
					$core_content_temp.="<h1 class='admin_page_title'>Create New Gallery</h1>";
					$core_content_temp.="<div style='position: relative; display: inline;  float:right;'><a onClick='parent.PandaImageGallery.pageRefresh=0; parent.$.fn.colorbox.close();' href='#'><img src='../themes/".$core_settings['theme_name']."/images/close.gif' border=0 /></a></div>";
					$core_content_temp.="<br/><br/>";
				}
			}

			$core_content_temp.="<script  type='text/javascript'>
				<!--
				function limitText(limitField, limitNum) {
				    if (limitField.value.length > limitNum) {
				        limitField.value = limitField.value.substring(0, limitNum);
				    }
				}
				-->
			      </script>";

                        $core_content_temp.="<br/><form class='admin_form' method='post' action='dosub.php?action=confirmgallerydetails&amp;vm={$vm}' enctype='multipart/form-data' accept-charset='UTF-8'>
                                <input type='hidden' name='galleryname' value='$galleryname' />
				<input type='hidden' name='gallerydate' value='{$loopvars->get_var("gallery_date_posted")}' />
                                <input type='hidden' name='kickmeto' value='$kickmeto' />";

			switch ($error){
				case 'GCblank':
					$core_content_temp.="<tr><td colspan='3' align='center'><font style='color:red; font-weight:bold;'>Error: You left a field blank. Please make sure you have filled out ALL required fields.</font><br /><br/><br/></td></tr>";
					break;
			    default:
					$core_content_temp.="<br/><br/>";
					break;
			}

			$core_content_temp.="
				<div class='admin_form_row'>
					<label>Gallery Title:</label>
					<input type='text' name='gallerytitle' size=50 value='{$loopvars->get_var("gallery_title")}' maxlength='{$theme_settings->get_var("max_title_length")}' onKeyDown='limitText(this,{$theme_settings->get_var("max_title_length")});' onKeyUp='limitText(this,{$theme_settings->get_var("max_title_length")});'  class='gallery_title_field' />
				</div>
				
				<div class='admin_form_row'>
					<label>Gallery Description:</label>
					<textarea name='gallerydesc' cols='50' rows='6' onKeyDown='limitText(this,{$theme_settings->get_var("max_description_length")});' onKeyUp='limitText(this,{$theme_settings->get_var("max_description_length")});' class='gallery_description_field' >{$loopvars->get_var("gallery_description")}</textarea>
				</div>
				
				<div class='admin_form_row'>
					<label>Default Sort Order:</label>
					<select name='galleryViewSortOrder' class='sortForm'>";

					if($loopvars->compare("gallery_sort_order", "dateDESC")){
						$core_content_temp.="<option value='dateDESC' SELECTED>Newest to Oldest</option>";
					}else{
						$core_content_temp.="<option value='dateDESC'>Newest to Oldest</option>";
					}
					if($loopvars->compare("gallery_sort_order", "dateASC")){
						$core_content_temp.="<option value='dateASC' SELECTED>Oldest to Newest</option>";
					}else{
						$core_content_temp.="<option value='dateASC'>Oldest to Newest</option>";
					}
					if($loopvars->compare("gallery_sort_order", "titleAlphabetical")){
						$core_content_temp.="<option value='titleAlphabetical' SELECTED>Image Title</option>";
					}else{
						$core_content_temp.="<option value='titleAlphabetical'>Image Title</option>";
					}
					$core_content_temp.="</select>
				</div>
				
				<div class='admin_form_row'>
                	<label>Image Protection:</label>
					<select name='concealPath' class='sortForm'>";
						if($loopvars->get_var("conceal_paths")==0){
							$core_content_temp.="<option value='0' SELECTED>Do not conceal the image path</option>";
						}else{
							$core_content_temp.="<option value='0'>Do not conceal the image path</option>";
						}
						if($loopvars->get_var("conceal_paths")==1){
							$core_content_temp.="<option value='1' SELECTED>Conceal the image path</option>";
						}else{
							$core_content_temp.="<option value='1'>Conceal the image path</option>";
						}
					$core_content_temp.="</select>
				</div>
                
                <div class='admin_form_row'>
				<label>Download Setting:</label>
				<select name='downloadLink' class='sortForm'>";
					if($loopvars->get_var("gallery_download_policy")==0){
						$core_content_temp.="<option value='0' SELECTED>Do not allow download</option>";
					}else{
						$core_content_temp.="<option value='0'>Do not allow download</option>";
					}
					if($loopvars->get_var("gallery_download_policy")==1){
						$core_content_temp.="<option value='1' SELECTED>Allow low resoution download</option>";
					}else{
						$core_content_temp.="<option value='1'>Allow low resoution download</option>";
					}
					if($loopvars->get_var("gallery_download_policy")==2){
						$core_content_temp.="<option value='2' SELECTED>Allow High Resolution (original quality) download</option>";
					}else{
						$core_content_temp.="<option value='2'>Allow High Resolution (original quality) download</option>";
					}
				$core_content_temp.="</select>
				</div>
				
				<div class='admin_form_row'>
					<label>Gallery Copyright:</label>
					<input type='text' name='gallerycopyright' size=50 value='{$loopvars->get_var("gallery_copyright")}' maxlength='{$theme_settings->get_var("max_title_length")}' onKeyDown='limitText(this,{$theme_settings->get_var("max_title_length")});' onKeyUp='limitText(this,{$theme_settings->get_var("max_title_length")});' class='gallery_copyright_field' />
				</div>
				
				<div class='admin_form_row'>";

					if (file_exists($galleriesRoot.$galleryname)){
						$core_content_temp.="<input type='submit' value='Save' class='save' onClick='parent.PandaImageGallery.pageRefresh=1;' /><button onClick='parent.$.fn.colorbox.close();' type='submit' name='action' value='Cancel' formaction='http://".urldecode($kickmeto)."' class='button' >Cancel</button>";
		
					}else{
						$core_content_temp.="<input type='submit' value='Create New Gallery' onClick='parent.PandaImageGallery.pageRefresh=1;' class='create_new_gallery' /><button onClick='parent.$.fn.colorbox.close();' type='submit' name='action' value='Cancel' formaction='http://".urldecode($kickmeto)."' class='button' >Cancel</button>";
					}

			$core_content_temp.="</div>
                              </form>";

			$core_content_temp.="</div>";
			
			if($vm!='ajax'){
				/* RUN THEME */
				//prepare_theme_vars();
				$core_content_temp=do_theme($core_content_temp, $loopvars, $theme_settings, $core_settings); //Call mail theme function. Pass all necessary vars.
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
				
				//make sure the theme is following the rules
				if(!$loopvars->get_var("get_head")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_head().</div>");
				}
				if(!$loopvars->get_var("get_footer")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_footer().</div>");
				}
				
			}else{
				$core_content_temp.=get_core_footer();
				$core_content_temp.="</body></html>";
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
			}
			
			//echo body content
			$loopvars->echo_var("markup_output");

			break;

	case 'editgallerycollaborators':

			include "../conf/users/users.inc.php";
			
			$error=$_GET['error'];

			$galleryname='';
			if(isset($_GET['galleryname'])){
				$galleryname=$_GET['galleryname'];
			}else if(isset($_POST['galleryname'])){
				$galleryname=$_POST['galleryname'];
			}
			
			if(!can_edit($galleryname, $_SESSION['s_userName']) ){
				header("Location: http://".$kickmeto);
			}
			
			$completepath=$galleriesRoot.$galleryname."/".$filetoset;

			if($vm=='ajax'){
				$core_content_temp.="<!DOCTYPE html>
									 <html lang='en'>
									 <head>";
									 
				$core_content_temp.=get_core_head();
									 
				$core_content_temp.="</head>
									 </html>
									 <body>";
			}

			$core_content_temp.="<div class='core_container admin_container edit_gallery_collaborators_page'>";
			
			if(file_exists($galleriesRoot.$galleryname)){

				//RETRIEVE GALLERY INFORMATION
				get_gallery_details($galleryname);

				$gallery_collaborators = $loopvars->get_var("gallery_collaborators");
				
				if($loopvars->compare("gallery_sort_order", "") || !$loopvars->var_isset("gallery_sort_order")){
					$loopvars->set_var("gallery_sort_order", $core_settings['default_image_sort']);
				}

				if($vm!='ajax'){
					$core_content_temp.="<h1 class='admin_page_title'>Edit Gallery Collaborators</h1>
										 <div class='admin_gallery_details'>
											<div class='admin_gallery_name'><span>Gallery Name:</span> {$loopvars->get_var("gallery_title")}</div>
											<div class='admin_gallery_description'><span>Gallery Description:</span> {$loopvars->get_var("gallery_description")}</div>
										 </div>";
				}else{
					$core_content_temp.="<h1 class='admin_page_title'>Edit Gallery Collaborators</h1>";
				}
			}

			$core_content_temp.="<div class='collaborator_form'>";
			
			switch($error){
					case 'alreadyContributor':
						$core_content_temp.="<div class='add_user_error'>ERROR: The user you attempted to add is already a contributor.</div>";
						break;
						
				    case 'cantAddSelf':
						$core_content_temp.="<div class='add_user_error'>ERROR: You cannot add yourself as a contributor.</div>";
						break;
					
					case 'notFound':
						$core_content_temp.="<div class='add_user_error'>ERROR: The contributor was not found.</div>";
						break;
					
					case 'contributorDeleted':
						$core_content_temp.="<div class='add_user_error'>ERROR: The contributor has been deleted.</div>";
						break;
							
				    case 'success':
						$core_content_temp.="<div class='add_user_success'>SUCCESS: The contributor has been added.</div>";
						break;


			}
				
			$core_content_temp.="<form class='reg_form password_reset_form' method='post' action='dosub.php?action=addgallerycollaborator'>
									<input type='hidden' name='cms' value='{$cms}' />
									<input type='hidden' name='galleryname' value='{$galleryname}' />
									
									<div class='reg_form_line'>
										<label>Enter collaborator email or username:</label>
										<input type='text' name='collaborator'>
									</div>
									
									<div class='reg_form_line'>
										<input type='submit' value='Add Collaborator' class='registrationButton'>
									</div>
								</form>
								</div>";
							
			if( is_array($gallery_collaborators) && count($gallery_collaborators) > 0 ){
				
				$row_classer = "user_table_dark";
				
				$core_content_temp.="<table class='user_table' cellspacing='0' cellpadding='0'>
									   <tr class='user_row'>
										<td class='user_row-narrow_item'><span>Username</span></td>
										<td class='user_row-narrow_item'><span>First Name</span></td>
										<td class='user_row-narrow_item'><span>Last Name</span></td>
										<td class='user_row-wide_item'><span>Email Address</span></td>
										<td class='user_row-narrow_item'></td>
									   </tr>";
				
				foreach ($gallery_collaborators as $collaborator) {
				
					$core_content_temp .= "<tr class='user_row ".$row_classer."'>
											<td class='user_row-narrow_item'>&nbsp;&nbsp;".$collaborator."</td>
											<td class='user_row-narrow_item'>".$users_array[$collaborator]["firstname"]."</td>
											<td class='user_row-narrow_item'>".$users_array[$collaborator]["lastname"]."</td>
											<td class='user_row-wide_item'>".$users_array[$collaborator]["email_address"]."</td>
											<td class='user_row-narrow_item'><a class='button' onclick='if( confirm(\"Would you like to delete the gallery collaborator ".$collaborator."?\") ){ location.href = \"dosub.php?action=delgallerycollaborator&galleryname=".$galleryname."&collaborator=".$collaborator."\"}'>delete</a></td>
										   </tr>";
										   
					if( $row_classer == "user_table_dark" ){
						$row_classer = "";
					}else{
						$row_classer = "user_table_dark";
					}
				}
				
				$core_content_temp.= "</table>";
			}
			
			$core_content_temp.="</div>";
			
			if($vm!='ajax'){
				/* RUN THEME */
				//prepare_theme_vars();
				$core_content_temp=do_theme($core_content_temp, $loopvars, $theme_settings, $core_settings); //Call mail theme function. Pass all necessary vars.
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
				
				//make sure the theme is following the rules
				if(!$loopvars->get_var("get_head")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_head().</div>");
				}
				if(!$loopvars->get_var("get_footer")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_footer().</div>");
				}
				
			}else{
				$core_content_temp.=get_core_footer();
				$core_content_temp.="</body></html>";
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
			}
			
			//echo body content
			$loopvars->echo_var("markup_output");

			break;
			
		case 'deletegallery':

			$galleryname='';
			if(isset($_POST['galleryname'])){
                $galleryname=$_POST['galleryname'];
			}else if(isset($_GET['galleryname'])){
			    $galleryname=$_GET['galleryname'];
			}

			if( !can_edit($galleryname, $_SESSION['s_userName']) ){
				header("Location: http://$kickmeto");
				break;
			}
			
			get_gallery_details($galleryname);

			if($vm=='ajax'){
				$core_content_temp.="<!DOCTYPE html>
									 <html lang='en'>
									 <head>";
									 
				$core_content_temp.=get_core_head();
									 
				$core_content_temp.="</head>
									 </html>
									 <body>";
			}

			$core_content_temp.="<div class='core_container admin_container delete_gallery_page'>";
			
			if($vm!='ajax'){
				$core_content_temp.="<h1 class='admin_page_title'>Delete Gallery</h1>
									 <div class='admin_gallery_details'>
										 <div class='admin_gallery_name'><span>Gallery Name:</span> {$loopvars->get_var("gallery_title")}</div>
										 <div class='admin_gallery_description'><span>Gallery Description:</span> {$loopvars->get_var("gallery_description")}</div>
									 </div>";
			}
			

			//RETRIEVE AND DISPLAY GALLERY IMAGE
			$core_content_temp.=get_gallery_action_image($loopvars, $core_settings, $galleriesRoot, $galleryname);

			$core_content_temp.="<div class='action_message'>Are you sure you want to <span>delete</span> the gallery named: \"{$loopvars->get_var("gallery_title")}\"?</div>";
			
			$core_content_temp.="<div class='action_buttons'>
								 <a href='dosub.php?action=confirmdeletegallery&galleryname={$galleryname}&vm={$vm}&kickmeto={$kickmeto}' onClick='parent.PandaImageGallery.pageRefresh=1;' class='confirm button'>Delete Gallery</a>
								 <a onClick='parent.$.fn.colorbox.close();' href='http://".urldecode($kickmeto)."' class='cancel button'>Cancel</a>
								 </div>";

			$core_content_temp.="</div>";
			
			if($vm!='ajax'){
				/* RUN THEME */
				//prepare_theme_vars();
				$core_content_temp=do_theme($core_content_temp, $loopvars, $theme_settings, $core_settings); //Call mail theme function. Pass all necessary vars.
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
				
				//make sure the theme is following the rules
				if(!$loopvars->get_var("get_head")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_head().</div>");
				}
				if(!$loopvars->get_var("get_footer")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_footer().</div>");
				}
				
			}else{
				$core_content_temp.=get_core_footer();
				$core_content_temp.="</body></html>";
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
			}
			
			//echo body content
			$loopvars->echo_var("markup_output");

			break;

		case 'uploadimages':

			if(isset($_POST['galleryname'])){
				$galleryname=$_POST['galleryname'];
			}else{
				if(isset($_GET['galleryname'])){
					$galleryname=$_GET['galleryname'];
				}
			}
			
			if( !can_edit($galleryname, $_SESSION['s_userName']) && !can_collaborate($galleryname, $_SESSION['s_userName'])){
				header("Location: http://$kickmeto");
				break;
			}
			
			get_gallery_details($galleryname);
			
			if($vm=='ajax'){
				$core_content_temp.="<!DOCTYPE html>
									 <html lang='en'>
									 <head>";
									 
				$core_content_temp.=get_core_head();
									 
				$core_content_temp.="</head>
									 </html>
									 <body>";
			}else{
				$core_content_temp.="<div class='core_container admin_container upload_page'>
									 <h1 class='admin_page_title'>Upload Images</h1>
									 <div class='admin_gallery_details'>
				            		 <div class='admin_gallery_name'><span>Gallery Name:</span> {$loopvars->get_var("gallery_title")}</div>
				            		 <div class='admin_gallery_description'><span>Gallery Description:</span> {$loopvars->get_var("gallery_description")}</div>
				            		 </div>";
			}

			$core_content_temp.="
								<div id='uploader'>
									<p>Your browser doesn\'t have HTML5, Flash, or Silverlight support.</p>
								</div>
								
								<script type='text/javascript'>

								// Initialize the widget when the DOM is ready
								$(function() {
									$('#uploader').plupload({
										// General settings
										runtimes : 'html5,flash,silverlight,html4',
										url : \"upload.php?galleryname=".$galleryname."&sessionid=".$sessionid."\",
								
										// Maximum file size
										max_file_size : '".ini_get('upload_max_filesize')."',
								
										chunk_size: '1mb',";
										
			if($core_settings['restrict_uploaded_image_dimensions']){
					$core_content_temp.="   // Resize images on clientside if we can
											resize : {
												width : ".$core_settings['max_uploaded_width'].",
												height : ".$core_settings['max_uploaded_height'].",
												quality : 100,
												crop: false // crop to exact dimensions
											},";
			}
									
			$core_content_temp.="					
										// Specify what files to browse for
										filters : [
											{title : \"Image files\", extensions : \"jpg,jpeg,gif,png\"},
											{title : \"Zip files\", extensions : \"zip,mp4,ogg,webm\"}
										],
								
										// Rename files by clicking on their titles
										rename: true,
										
										// Sort files
										sortable: true,
								
										// Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
										dragdrop: true,
								
										// Views to activate
										views: {
											list: true,
											thumbs: true, // Show thumbs
											active: 'thumbs'
										},									
										                           
										// Flash settings
										flash_swf_url : '../scripts/plupload/Moxie.swf',
									
										// Silverlight settings
										silverlight_xap_url : '../scripts/plupload/Moxie.xap',
										
										
										// Post init events, bound after the internal events
								        init : 
								        {
								            StateChanged: function(up) 
								            {
								            	//upload has started
								                if(up.state == 2){
								                	document.getElementById('cancel_button').disabled = true;
								                	document.getElementById('cancel_button').style.display = 'none';
								                	document.getElementById('finished_uploading_button').disabled = true;
								                	document.getElementById('finished_uploading_button').style.display = 'none';
								                	document.getElementById('uploading_message').style.display = '';
								                	document.getElementById('upload_complete_message').style.display = 'none';
								                }
								            },
								
								            UploadComplete: function(up, files)
								            {
								                document.getElementById('finished_uploading_button').disabled = false;
								                document.getElementById('finished_uploading_button').style.display = '';
								                document.getElementById('uploading_message').style.display = 'none';
								                document.getElementById('upload_complete_message').style.display = '';
								            },
								
								        }
        										
									});
								});
								</script>
								";
			
								
			$core_content_temp.="<div class='action_buttons'>
									
									<div id='uploading_message' style='display: none;'>
										<span>Uploading</span> <img src='../includes/images/loading.gif' />
									</div>
									
									<div id='upload_complete_message' style='display: none;'>
										Upload complete!
									</div>
									
								 	<input type='submit' onClick='parent.$.fn.colorbox.close(); location.href=\"http://".urldecode($kickmeto)."\";' href='http://".urldecode($kickmeto)."' class='button' value='Close Uploader' id='finished_uploading_button' disabled style='display: none;' />
								 	<input type='submit' onClick='parent.$.fn.colorbox.close(); location.href=\"http://".urldecode($kickmeto)."\";' class='cancel button' value='Cancel Upload' id='cancel_button' />
								 </div>";

			$core_content_temp.="</div>";
			
			if($vm!='ajax'){
				/* RUN THEME */
				//prepare_theme_vars();
				$core_content_temp=do_theme($core_content_temp, $loopvars, $theme_settings, $core_settings); //Call mail theme function. Pass all necessary vars.
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
				
				//make sure the theme is following the rules
				if(!$loopvars->get_var("get_head")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_head().</div>");
				}
				if(!$loopvars->get_var("get_footer")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_footer().</div>");
				}
				
			}else{
				$core_content_temp.=get_core_footer();
				$core_content_temp.="</body></html>";
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
			}
			
			//echo body content
			$loopvars->echo_var("markup_output");

			break;

		case 'chgpass':
			
			if(isset($auto_conf['wordpress_plugin'])){
				if($auto_conf['wordpress_plugin']==true){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
				}
			}
	
			if(isset($_GET['error']))
				$error=$_GET['error'];
	
	        if(!isset($_SESSION['s_userName'])){
				header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
				break;
	        }
	
			$core_content_temp="<div class='core_container admin_container'>
								<h1 class='reg_title'>Change Password</h1>";
	
	        switch($error){
		        case 'success':
			        $core_content_temp.="<span class='registrationSuccess'>Success: Your password has been changed.</span>";
			        break;
			        		
		        case 'wrongpass':
			        $core_content_temp.="<span class='registrationError'>ERROR: The current password you entered was incorrect.</span>";
			        break;
		
		        case 'passMissMatch':
			        $core_content_temp.="<span class='registrationError'>ERROR: The passwords you entered did not match.</span>";
			        break;
		
		        case 'passChar':
		        	$core_content_temp.="<div class='registrationError'>ERROR: Your password contained spaces. Passwords must not contain spaces.</div>";
		        	break;
		        
		        case 'passShort':
		        	$core_content_temp.="<div class='registrationError'>ERROR: Your password was not long enough. Passwords must be at least 8 characters.</div>";
		        	break;
		
		        case 'fieldBlank':
			        $core_content_temp.="<span class='registrationError'>ERROR: You left a field blank. ALL fields are mandatory.</span>";
			        break;
	        }
	
	        $core_content_temp.="<form class='admin_form' method='post' action='dosub.php'>
		        						<input type='hidden' name='cms' value='{$cms}' />
		        						<input type='hidden' name='action' value='chgpass' />
		        						
		        						<div class='admin_form_row'>
		        							<label>Current Password:</label>
		        							<input type='password' name='currentPassword' value='' />
		        						</div>
		        						
		        						<div class='admin_form_row'>
		        							<label>New Password:</label>
		        							<input type='password' name='requestedPassword' />
		        						</div>
		        						
		        						<div class='admin_form_row'>
		        							<label>Re-enter New Password:</label>
		        							<input type='password' name='requestedPassword2' />
		        						</div>
		        						
		        						<div class='admin_form_row'>
		        							<input type='submit' value='Change Password' />
		        						</div>
	        					   </form>
	        					</div>";
	
	
			/* RUN THEME */
			$core_content_temp=do_theme($core_content_temp, $loopvars, $theme_settings, $core_settings); //Call mail theme function. Pass all necessary vars.
			$loopvars->set_var("markup_output", $core_content_temp); //Store body content
			unset($core_content_temp); //free memory
			
			//make sure the theme is following the rules
			if(!$loopvars->get_var("get_head")){
				die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_head().</div>");
			}
			if(!$loopvars->get_var("get_footer")){
				die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_footer().</div>");
			}
			
			//echo body content
			$loopvars->echo_var("markup_output");
			
			break;
				
		default:
		
			$core_content_temp.="<div class='core_container admin_container'>";
		
			//heading
			$core_content_temp.="<div class='admin_help'><h1>Creating an image gallery is easy. Simply follow these steps:</h1>";
			$core_content_temp.="<ul>";
			$core_content_temp.="<li><span>Click the 'create new gallery' button below.</span></li>";
			$core_content_temp.="<li><span>Enter a gallery title and description on the following page and then click the 'create gallery' button.</span></li>";
			$core_content_temp.="<li><span>Once a gallery has been created, locate the gallery below. By default new galleries should appear on-top.</span></li>";
			$core_content_temp.="<li><span>To add an image or movie to a gallery click the \"Upload\" button for the gallery you wish to add to. Files can be dragged and dropped on the uploader widget or you can browse for the files you wish to upload. Once files have been selected, click the \"Start Upload\" button to begin uploading your files.</span></li>";
			$core_content_temp.="</ul></div>";
		
			if(is_wordpress()){
				$core_content_temp.=" <div class='wordpress_instructions'>
									    <h1>Wordpress Users</h1>
									
									    <p>Paste the shortcode <strong>[wp_panda_image_gallery_page]</strong> on any blank Wordpress page to display a page of your image galleries.</p>
									    <p>Paste the shortcode included with each gallery's data found below (ex: <strong>[wp_panda_image_gallery_slideshow gallery=xxxx]</strong>) to display a slideshow of the gallery on any page or post.</p>
									  </div>";
			}
		
			$error='';
			if(isset($_GET['error'])){
				$error=$_GET['error'];
				$core_content_temp.="<div class='error_notice'>";
				
				switch($error){
		            case 'IMGnofile':
		                $core_content_temp.="Error: You did not select a file to upload.";
		                break;
		            case 'IMGnogalleryname':
		            	$core_content_temp.="Error: An unknown error occurred. Please try again later.";
		                break;
		            case 'IMGexists':
		            	$core_content_temp.="Error: That image already exists.";
		                break;
		            case 'IMGtoobig':
		            	$core_content_temp.="Error: The image you are trying to upload exceeds the maximum size.";
		                break;
		            case 'IMGbadextension':
		            	$core_content_temp.="Error: The file type is unsupported.";
		                break;
		            case 'IMGSuccess':
		        		$core_content_temp.="Success: Your file was successfully uploaded.";
		                break;
				    case 'GCsuccessful':
						$core_content_temp.="Success: Your gallery has successfully been created.";
						break;
			    	case 'GCexists':
		                $core_content_temp.="Error: You have entered a duplicate identifier. Please choose a different identifier and try again.";
			            break;
				    case 'GCblank':
						$core_content_temp.="Error: You left a field blank. Please make sure you have filled out ALL required fields.";
						break;
			    	case 'GCfail':
						$core_content_temp.="Error: An unknown error occured. Please try again later.";
						break;
		            case 'DELSuccess':
		                $core_content_temp.="Success:  The file was deleted.";
		                break;
			    	case 'DELGSuccess':
						$core_content_temp.="Success:  The gallery was deleted.";
						break;
		            case 'THMSuccess':
		                $core_content_temp.="Success:  The thumbnail image was selected.";
		                break;
				}
				$core_content_temp.="</div>";
			}
			
			//CREATE GALLERY DISPLAY
			$core_content_temp.="<a $ajaxifynewgallery href='index.php?action=editgallerydetails&amp;galleryname=NEW' class='admin_create_new_gallery button'>Create New Gallery</a>";
		
		
			//present sorting options for the admin galleries
			$core_content_temp.="<div class='admin_sort_form_container'>
			      <form method='post' action='index.php' enctype='multipart/form-data' class='admin_sort_form'><input type='hidden' name='cms' value='{$cms}' />
		              <label>Sort by:</label> <select name='galleryAdminSortOrder' class='sortForm' onChange='this.parentNode.submit();'>";
			if($_SESSION['s_galleryAdminSortOrder']=='dateDESC'){
				$core_content_temp.="<option value='dateDESC' SELECTED>Newest to Oldest</option>";
			}else{
				$core_content_temp.="<option value='dateDESC'>Newest to Oldest</option>";
			}
			if($_SESSION['s_galleryAdminSortOrder']=='dateASC'){
				$core_content_temp.="<option value='dateASC' SELECTED>Oldest to Newest</option>";
			}else{
				$core_content_temp.="<option value='dateASC'>Oldest to Newest</option>";
			}
			if($_SESSION['s_galleryAdminSortOrder']=='dateModifiedDESC'){
				$core_content_temp.="<option value='dateModifiedDESC' SELECTED>Recently Modified</option>";
			}else{
				$core_content_temp.="<option value='dateModifiedDESC'>Recently Modified</option>";
			}
			if($_SESSION['s_galleryAdminSortOrder']=='titleAlphabetical'){
				$core_content_temp.="<option value='titleAlphabetical' SELECTED>Gallery Title</option>";
			}else{
				$core_content_temp.="<option value='titleAlphabetical'>Gallery Title</option>";
			}
			$core_content_temp.="</select><input type='submit' value='go' class='sort_form_button' /></form></div>";
		
			/* GET THE DIRECTORY LISTING OF THE GALLERIES DIRECTORY. EACH GALLERY IS IN ITS OWN
			DIRECTORY. IF WE ARE DISPLAYING PERSONAL GALLERIES WE NEED TO TAKE ONE ADDITIONAL STEP
			AND CHECK DIRNAME FORREQUESTED USERS PERSONAL GALLERY STRING.
			*/
		
			$parsedusername=str_replace(" ", "", $loopvars->get_var("current_user"));
			$parsedusername.="_";
			/*
			Create a new directory listing instance and process directory
			*/
			$clsDirListingDefault = new clsDirListing($galleriesRoot, 3);
			$clsDirListingDefault->sortOrder($galleryAdminSortOrder);
			$clsDirListingDefault->prefixIncludes(array($parsedusername));
			$clsDirListingDefault->excludes(array("logs"));
			$galleries=$clsDirListingDefault->getListing();
			$clsDirListingDefault="";
		
		
			$counter=0;
			if(empty($galleries)){
			
				$core_content_temp.="<div class='notice'>You do not have any galleries</div>";
			
			}else{
			
				$gallery_count;
			    foreach ($galleries as $galleryentry){
					
					$gallery_count++;
					
				    $core_content_temp.="<div class='gallery_listing_tile'>";
			
					$galleryentry=$galleryentry[0];
			
					//$counter=$counter+1;
			
					get_gallery_details($galleryentry);
			
					if($loopvars->compare("gallery_sort_order", "") || !$loopvars->var_isset("gallery_sort_order")){
						$loopvars->set_var("gallery_sort_order", $core_settings['default_image_sort']);
					}
			
					$galleryMasterLink=$backtogallerylink."?a=vg&amp;g=$galleryentry"; //this is just to save typing this everywhere below
			
					switch($loopvars->get_var("gallery_sort_order")){
					    case 'dateDESC':
							$printSortOrder="Newest to Oldest";
							break;
			
					    case 'dateASC':
							$printSortOrder="Oldest to Newest";
							break;
			
					    case 'titleAlphabetical':
							$printSortOrder="Image Title";
							break;
					}
			
			
					/* THE USER MIGHT HAVE SELECTED THEIR OWN DEFAULT SAMPLE IMAGE FOR THIS GALLERY.
					IF THEY DID THEN CHECK FOR ITS EXISTENCE AND RETURN ITS PRESENCE. OTHERWISE LETS FIND
					OUR OWN SAMPLE (THE FIRST IMAGE IN THE GALLERY)
					*/
					$clsDirListingSample = new clsDirListing($galleriesRoot.'/'.$galleryentry, 5);
					$clsDirListingSample->sortOrder("titleAlphabetical");
					$clsDirListingSample->extensionIncludes(array("jpg", "gif", "png", "jpeg"));
					$clsDirListingSample->prefixIncludes(array("thm_"));
					$sampleimagesingallery=$clsDirListingSample->getListing();
					$clsDirListingSample="";
			
					$thm_set=0;
					$galleryThumbnail="";
					if (file_exists($galleriesRoot.'/'.$galleryentry."/thm.php")) {
					
						include $galleriesRoot.'/'.$galleryentry."/thm.php";
			
						if(file_exists($galleriesRoot.'/'.$galleryentry."/".$galleryThumbnail)){
							$thm_set=1;
						}
					}
			
					$core_content_temp.="<div class='gallery_thumbnail'>";
					/* IF THE USER SPECIFIED A THUMB USE IT, OTHERWISE DISPLAY THE FIRST THUMB WE ENCOUNTER */
					if($thm_set==1){
						if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
							$core_content_temp.="<a href='{$galleryMasterLink}' $target><img alt='' src='../includes/view.php/".$galleryentry."/".$galleryThumbnail."' border='0' /></a>";
						}else{
							$core_content_temp.="<a href='{$galleryMasterLink}' $target><img alt='' src='{$galleriesRoot}/{$galleryentry}/{$galleryThumbnail}' border='0' /></a>";
						}
					}else if (count($sampleimagesingallery) > 0){
						if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
							$core_content_temp.="<a href='{$galleryMasterLink}' $target><img alt='' src='../includes/view.php/".$galleryentry."/".$sampleimagesingallery[0][0]."' border='0' /></a>";
						}else{
							$core_content_temp.="<a href='{$galleryMasterLink}' $target><img alt='' src='{$galleriesRoot}/{$galleryentry}/{$sampleimagesingallery[0][0]}' border='0' /></a>";
						}
					}else{
						$core_content_temp.="<a href='{$galleryMasterLink}' $target><img src='../themes/".$core_settings['theme_name']."/images/na.png' alt='' border=0 /></a>";
					}
					$core_content_temp.="</div>";
			
					$core_content_temp.="<div class='gallery_data'>";
					
					$core_content_temp.="<h2><a href='{$galleryMasterLink}' name='{$galleryentry}' $target><span class='adminGalleryTitle'>{$loopvars->get_var("gallery_title")}</span></a></h2><h3>{$loopvars->get_var("gallery_description")}</h3>";
					
					if($loopvars->get_var("gallery_copyright")!=""){
						$core_content_temp.="<p><span>Gallery Copyright:</span> ".$loopvars->get_var("gallery_copyright")."</p>";
					}
					$core_content_temp.="<p><span>Default Sort Order:</span> ".$printSortOrder."</p>";
					$core_content_temp.="<p><span>Created:</span> {$loopvars->get_var("gallery_date_posted")}</p>";
							
					$core_content_temp.="<div class='admin_options'>";		
					//DISPLAY UPLOAD OPTION
					$core_content_temp.="<a $ajaxifyupload href='index.php?action=uploadimages&amp;galleryname={$galleryentry}&cms={$cms}' class='button'>Upload</a>";
			
					//EDIT GALLERY DETAILS DISPLAY
					$core_content_temp.="<a $ajaxifynewgallery href='index.php?action=editgallerydetails&amp;galleryname={$galleryentry}&cms={$cms}' class='button'>Edit</a>";
			
					//DELETE GALLERY DISPLAY
					$core_content_temp.="<a $ajaxifydelete href='index.php?action=deletegallery&amp;galleryname={$galleryentry}&cms={$cms}' class='button'>Delete Gallery</a>";
					
					if( $core_settings['license'] == "paid" ){
						$core_content_temp.="<a $ajaxifynewgallery href='index.php?action=editgallerycollaborators&amp;galleryname={$galleryentry}&cms={$cms}' class='button'>Collaborators</a>";
					}
										
					$core_content_temp.="</div>";
		
			        $clsDirListingDefault = new clsDirListing($galleriesRoot.'/'.$galleryentry, 0);
			        $clsDirListingDefault->sortOrder("titleAlphabetical");
			        $clsDirListingDefault->extensionIncludes(array("jpg", "gif", "png", "avi", "mpg", "mpeg", "mov", "wmv", "jpeg", "mp4", "flv"));
			        $clsDirListingDefault->prefixExcludes(array("thm_", "lowres_"));
			        $imagesingallery=$clsDirListingDefault->getListing();
			        $clsDirListingDefault="";
		
					if (count($imagesingallery) > 0){
		
						//DISPLAY DIRECT FILE OPTIONS
						$core_content_temp.="<div class='admin_gallery_dropdown'><form method='post' name='deletefile' action='index.php' enctype='multipart/form-data' style='padding: 0; margin: 0;'>
						<label>Delete:</label> <select name='filetoset' class='adminSelect'>";
			
						if(is_array($imagesingallery)){
						    foreach($imagesingallery as $value){
			                    $value[0]=urlencode(htmlentities($value[0], ENT_QUOTES));
								$core_content_temp.="<option>".$value[0]."</option>";
						    }
						}
						$core_content_temp.="</select><input type='hidden' name='galleryname' value='$galleryentry' />
						<div class='admin_file_options_clear'></div>
						<button $ajaxifydeleteInput type='submit' name='action' value='deleteimage' class='button'>Delete</button>
						<button $ajaxifydeleteInput type='submit' name='action' value='setthumb' class='button'>Make Thumnail</button>
						<button $ajaxifydeleteInput type='submit' name='action' value='setcaption' class='button'>Set Caption</button>
						</form></div>";						
					}
					
					if(is_wordpress()){
						$core_content_temp.="<div class='wordpress_shortcode'>Wordpress shortcode: <strong>[wp_panda_image_gallery_slideshow gallery=".$galleryentry."]</strong></div>";
					}
					
					$core_content_temp.="</div></div>";
					
					if($gallery_count < count($galleries)){
						$core_content_temp.="<div class='gallery_divider'><hr/></div>";		
					}
		    	}
			}
	
			$core_content_temp.="</div>";

			/* RUN THEME */
			//prepare_theme_vars();
			$core_content_temp=do_theme($core_content_temp, $loopvars, $theme_settings, $core_settings); //Call mail theme function. Pass all necessary vars.
			$loopvars->set_var("markup_output", $core_content_temp); //Store body content
			unset($core_content_temp); //free memory
			
			//make sure the theme is following the rules
			if(!$loopvars->get_var("get_head")){
				die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_head().</div>");
			}
			if(!$loopvars->get_var("get_footer")){
				die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_footer().</div>");
			}
			
			//echo body content
			$loopvars->echo_var("markup_output");
		
			break;
	}

}else{

	if(!$loopvars->get_var("wordpress")){
			if($vm=="ajax"){
				$core_content_temp.="<!DOCTYPE html><html lang='en'>
									  <head>
									  <meta name='viewport' content='width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;' />
							 		  <link rel='stylesheet' type='text/css' href='{$styleSheetPath}/content.css' />
							 		  <title>PANDA Image gallery Login</title></head><body class='adminfuncts'>";
			}

			$core_content_temp.="<h1 class='reg_title'>Login</h1>";
			
			if($error == "login"){
				$core_content_temp.="<strong>ERROR:</strong> Invalid username or password!\n";
			}
			
			$core_content_temp.="<div class='login_form_container'>
								<form method='post' action='login.php?action=login' class='login_form'>
									<input type='hidden' name='cms' value='{$cms}' />
									<input type='hidden' name='vm' value='{$vm}' />
									<input type='hidden' name='kickmeto' value='{$kickmeto}' />
									<input type='hidden' name='action' value='login' />
									<input type='hidden' name='location' value='{$loopvars->get_var("current_url")}' />
									<div class='login_username_wrapper'><label>Username:</label> <input type='text' name='username' class='login_username_field' /></div>
									<div class='login_password_wrapper'><label>Password:</label> <input type='password' name='password' class='login_password_field' /></div>
									<div class='login_button_wrapper'><input type='submit' value='login' class='login_button' /></div>
								</form>
								<div class='login_password_recovery_wrapper'><a href='{$passwordRecoveryPath}' $target>Forgot Password?</a></div>
								</div>";

			if($vm=="ajax"){
				$core_content_temp.="</body></html>";
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
			}else{
				/* RUN THEME */
				//prepare_theme_vars();
				$core_content_temp=do_theme($core_content_temp, $loopvars, $theme_settings, $core_settings); //Call mail theme function. Pass all necessary vars.
				$loopvars->set_var("markup_output", $core_content_temp); //Store body content
				unset($core_content_temp); //free memory
				
				//make sure the theme is following the rules
				if(!$loopvars->get_var("get_head")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_head().</div>");
				}
				if(!$loopvars->get_var("get_footer")){
					die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_footer().</div>");
				}
			}
						
			//echo body content
			$loopvars->echo_var("markup_output");
	}else{
		
		$core_content_temp.="<div class='notice'>You must be logged into Wordpress to manage galleries.</div>";
		
		/* RUN THEME */
		//prepare_theme_vars();
		$core_content_temp=do_theme($core_content_temp, $loopvars, $theme_settings, $core_settings); //Call mail theme function. Pass all necessary vars.
		$loopvars->set_var("markup_output", $core_content_temp); //Store body content
		unset($core_content_temp); //free memory
		
		//make sure the theme is following the rules
		if(!$loopvars->get_var("get_head")){
			die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_head().</div>");
		}
		if(!$loopvars->get_var("get_footer")){
			die("<div style='font-weight: bold; font-size: 1.3em; padding-top: 100px; padding-bottom: 100px;'>Error: Theme must make call to the function get_core_footer().</div>");
		}
		
		//echo body content
		$loopvars->echo_var("markup_output");
	}
}

unset($loopvars);

?>
