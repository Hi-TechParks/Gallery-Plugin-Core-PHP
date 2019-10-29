<?php
//#######################################################################\\
//# Author: Christopher Schiffner                                       #\\
//# Filename: index.php                                                 #\\
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

ini_set ('default_charset', 'UTF-8');

if(!file_exists("./conf/config.php")){
    die("<b>You must configure Panda Image Gallery! <br/><br/>Please review config.php.sample, configure as necessary,
	and rename config.php.sample to config.php. <br/><br/>See the README or installation notes for installation details.</b>");
}else{
	include_once './conf/config.php';
}

//benchmarking code
include_once 'includes/class/timer.php';
if($core_settings['benchmark']){
    //benchmark code
    $benchmark_timer = new Timer;
    $benchmark_timer->starttime();
}

include_once 'includes/class/loopvars.php';
include_once 'includes/version.php';
$loopvars = new clsLoopVars();
$theme_settings = new clsLoopVars();
include_once 'includes/class/panda_dir.php';
include_once 'includes/functions.php';

if(file_exists("conf/auto_conf.php")){
	include "conf/auto_conf.php";
	
	if(isset($auto_conf['base_url'])){
		$core_settings['base_url']=$auto_conf['base_url'];
	}
}

if(is_wordpress()){
	if(file_exists("../../../wp-blog-header.php")){
		require_once("../../../wp-blog-header.php");
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

$galleriesRoot="galleries/";

@session_name($core_settings['session_identifier']);
@session_start();

include_once 'includes/varprep.php';

/* THEME */
$loopvars->set_var("theme_path", $core_settings['theme_name']);
include_once $core_settings['theme_name']."/theme.php";

$includeFlag='1'; //for later include detection

if($theme_settings->get_var("enable_ajax")==1){
	$ajaxifythumb=" onClick='$(this).colorbox({width:\"600px\", height:\"500px\", overlayClose: false, iframe:true, next: false, title: \" \", previous: false, current: false, href:function(){ return this.href+\"&vm=ajax\"; }, close: \"\", onClosed: function(){ PandaImageGallery.pageRefresh=0;} });' ";
	$ajaxifycaption=" onClick=' $(this).colorbox({width:\"600px\", height:\"400px\", overlayClose: false, iframe:true, next: false, title: \" \", previous: false, current: false, href:function(){ return this.href+\"&vm=ajax\"; }, close: \"\", onClosed: function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"includes/updating.html\", open: true, close: \"\" }); }PandaImageGallery.pageRefresh=0; } });' ";
	$ajaxifycaptionNoRefresh=" onClick='PandaImageGallery.overrideRefresh=1; $(this).colorbox({width:\"600px\", height:\"400px\", overlayClose: false, iframe:true, next: false, title: \" \", previous: false, current: false, href:function(){ return this.href+\"&vm=ajax\"; }, close: \"\", onClosed: function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"includes/updating.html\", open: true, close: \"\" }); }PandaImageGallery.pageRefresh=0; } });' ";
	$ajaxifydelete=" onClick='$(this).colorbox({width:\"600px\", height:\"500px\", overlayClose: false, iframe:true, next: false, title:  \" \", previous: false, current: false, href:function(){ return this.href+\"&vm=ajax\"; }, close: \"\", onClosed:function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"includes/updating.html\", open: true, close: \"\" }); }PandaImageGallery.pageRefresh=0; } });' ";
	$ajaxifynewgallery=" onClick='$(this).colorbox({width:\"700px\", height:\"500px\", overlayClose: false, iframe:true, next: false, title:  \" \", previous: false, current: false, href:function(){ return this.href+\"&vm=ajax\"; }, close: \"\", onClosed:function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"includes/updating.html\", open: true, close: \"\" }); }PandaImageGallery.pageRefresh=0; } });' ";
	$ajaxifyupload=" onClick='$(this).colorbox({width:\"650px\", height:\"500px\", overlayClose: false, iframe:true, next: false, title: \" \",  previous: false, current: false, href:function(){ return this.href+\"&vm=ajax\"; }, close: \"\", onClosed:function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"includes/updating.html\", open: true, close: \"\" }); } PandaImageGallery.pageRefresh=0; } });' ";
	$ajaxifylogin=" onClick='$(this).colorbox({width:\"400px\", height:\"300px\", iframe:true, next: false, title: \" \",  previous: false, current: false, href:function(){ return this.href+\"&vm=ajax\"; }, close: \"\", onClosed:function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"includes/updating.html\", open: true, close: \"\" }); } PandaImageGallery.pageRefresh=0; } });' ";
	$loopvars->set_var("ajax_login_javascript_block", " onClick='$(this).colorbox({width:\"400px\", height:\"300px\", iframe:true, next: false, title: \" \",  previous: false, current: false, href:function(){ return this.href+\"&vm=ajax\"; }, close: \"\", onClosed:function(){ if(PandaImageGallery.pageRefresh==1){ $(this).colorbox({width:\"300px\", height:\"200px\", iframe:true, scrolling: false, href: \"includes/updating.html\", open: true, close: \"\" }); } PandaImageGallery.pageRefresh=0; } });' ");
}

switch ($a){

	case 'reg':
	
		if(isset($auto_conf['wordpress_plugin'])){
			if($auto_conf['wordpress_plugin']==true){
				header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
			}
		}
		
		if(isset($_SESSION['s_userName'])){
			header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
			break;
		}
		
		if(!$core_settings['allow_user_registration']){
			header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
			break;
		}
	
		/* USER REGISTRATION
		   allows user to register with gallery
		*/
	
		$requestedUsername='';
		$userEmailAddress='';
		$usersFirstName='';
		$usersLastName='';
		$error='';
	
		if(isset($_GET['requestedUsername'])){
			$requestedUsername=strtolower(trim(urldecode($_GET['requestedUsername'])));
		}	
		if(isset($_GET['requestedPassword'])){
			$requestedPassword=trim(urldecode($_GET['requestedPassword']));
		}
		if(isset($_GET['userEmailAddress'])){
			$userEmailAddress=trim(urldecode($_GET['userEmailAddress']));
		}
		if(isset($_GET['usersFirstName'])){
			$usersFirstName=trim(urldecode($_GET['usersFirstName']));
		}
		if(isset($_GET['usersLastName'])){
			$usersLastName=trim(urldecode($_GET['usersLastName']));
		}
		if(isset($_GET['error'])){
			$error=$_GET['error'];
		}
		
		$core_content_temp="<div class='core_container admin_container'>";
	
		$core_content_temp.="<h1 class='reg_title'>New User Registration</h1>";
	
		if(!$core_settings['allow_user_registration']){
			die("Functionality disabled by administrator.");
		}
	
		if($error=="success"){
			$core_content_temp.="<div class='reg_notice'>
		      <p>Your account registration has been received.</p>
		      <p>You will receive an email from <span>".$core_settings['password_retrieval_email']."</span> containing your login information shortly.</p>
		      <p>If you do not see the email in your inbox, please check your junk mail folder.</p>
		      <p><a href='admin/index.php'>Click here to login</a></p>
		      </div>";
	
		}else{
		
			switch($error){
				case 'emailTaken':
					$core_content_temp.="<div class='registrationError'>ERROR: An account has already been registered using the supplied email address.</div>";
					break;
					
			    case 'userTaken':
					$core_content_temp.="<div class='registrationError'>ERROR: The username you requested is not available.</div>";
					break;
				
				case 'userChar':
					$core_content_temp.="<div class='registrationError'>ERROR: Your username contained illegal characters. Usernames may only contain letter A through Z and numbers 0 through 9. Spaces and special characters are not permitted.</div>";
					break;
					
			    case 'fieldBlank':
					$core_content_temp.="<div class='registrationError'>ERROR: You left a field blank. ALL fields are mandatory.</div>";
					break;
		
			    case 'codeMismatch':
					$core_content_temp.="<div class='registrationError'>ERROR: The verification code you entered was not correct.</div>";
					break;
			}
		
			$core_content_temp.="<form class='reg_form' method='post' action='admin/dosub.php'>
				<input type='hidden' name='cms' value='{$cms}' />
				<input type='hidden' name='action' value='reguser' />
				
				<div class='reg_form_line'>
					<label>Desired Username:</label>
					<input type='text' name='requestedUsername' value='{$requestedUsername}' />
				</div>
				
				<div class='reg_form_line'>
					<label>First Name:</label>
					<input type='text' name='usersFirstName' value='{$usersFirstName}' />
				</div>
				
				<div class='reg_form_line'>
					<label>Last Name:</label>
					<input type='text' name='usersLastName' value='{$usersLastName}' />
				</div>
				
				<div class='reg_form_line'>
					<label>Email Address:</label>
					<input type='text' name='usersEmailAddress' value='{$userEmailAddress}' />
				</div>
				
				<div class='reg_form_line'>
					<Label>Verification code:</label>
					<div class='reg_form_line_item'><img src='includes/captcha.php' class='captcha' /></div>
				</div>
				
				<div class='reg_form_line'>
					<label>Enter verification code:</label>
					<input type='text' name='response' />
				</div>
				
				<div class='reg_form_line'>
					<label>User Agreement:</label>
					<textarea rows='10' cols='50'>".$core_settings['user_registration_agreement']."</textarea>
				</div>
				
				<div class='reg_form_line'>
					<input type='submit' value='Register' class='registrationButton' />
				</div>
				
				</form>";
		}
	
		$core_content_temp.="</div>";
	
	
		/* RUN THEME */
		prepare_theme_vars();
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
	
	case 'pr':
	
		if( isset($_SESSION['s_userName'])){
			header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
			break;
		}
		
		$core_content_temp.="<div class='core_container admin_container'>
							 <h1 class='reg_title'>Password Reset</h1>";
		
		$error='';
		$hide_form = 0;
		if(isset($_GET['error'])){
			$error=$_GET['error'];
			$core_content_temp.="<div class='reg_notice'>";
			
			switch($error){

		        case 'success':
		            $core_content_temp.="<p>Your account information has been sent to <span>".urldecode($_GET['userEmailAddress'])."</span> from <span>".$core_settings['password_retrieval_email']."</span>.</p><p>If you do not see the email in your inbox, please check your junk mail folder.</p><p><a href='admin/index.php'>Click here to login</a></p>";
		            
		            $hide_form = 1;
		            
		            break;
		            
		        case 'nomatch':
		            $core_content_temp.="<p>Error: No user was found with that email address.</p>";
		            break;
		            
		        case 'blank':
		            $core_content_temp.="<p>Error: You did not enter an email address.</p>";
		            break;
			}
			$core_content_temp.="</div>";
		}
		
		if( $hide_form == 0 ){
			$core_content_temp.="<form class='reg_form password_reset_form' method='post' action='?a=resetuserpass'>
									<input type='hidden' name='cms' value='{$cms}' />
									
									<div class='reg_form_line'>
										<label>Enter email associated with account:</label>
										<input type='text' name='userEmailAddress'>
									</div>
									
									<div class='reg_form_line'>
										<input type='submit' value='Reset Password' class='registrationButton'>
									</div>
								</form>
							</div>";
		}
		
		/* RUN THEME */
		prepare_theme_vars();
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

	case 'resetuserpass':
	
		/* PASSWORD RECOVERY
		   user inputs password and will receive an email with new pass
		*/
		if(isset($auto_conf['wordpress_plugin'])){
			if($auto_conf['wordpress_plugin']==true){
				header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
			}
		}
		
		$userEmailAddress = $_POST['userEmailAddress'];
		if( trim($userEmailAddress) == "" ){
			$userEmailAddress = $_GET['userEmailAddress'];
		}
		
		$admin_reset = 0;
		if( isset($_GET['admin_reset']) ){
			$admin_reset = $_GET['admin_reset'];
		}
		
		if( isset($_SESSION['s_userName']) && $admin_reset != 1 ){
			header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
			break;
		}
	
		require('./conf/users/users.inc.php'); //INCLUDE OUR USERS FILE
	
		$retrievedUsername='';
		$retrievedPassword='';
		$retrievedFirstName='';
	
		if( trim($userEmailAddress) != "" ){

			/* Loop through users until we find a matching email address. The generate a random password to reset below and email to the user. */
			foreach($users_array as $tmp_users){
			    if(strtolower(trim($tmp_users['email_address']))==strtolower(trim($userEmailAddress))){
					$retrievedUsername=$tmp_users['username'];
					$retrievedPassword=random_password();
					$retrievedFirstName=$tmp_users['firstname'];
					break;
				}
			}
			
			if($retrievedPassword!=""){
		
				//Since we encrypt our user's password we need to reset it to something we know. 
				//A password was generated above. Store it. Then dispatch an email with the account info.
				$users_array[$retrievedUsername]['password']=crypt($retrievedPassword);
				
				write_user_data($users_array);
				
				//send email with account info
				if( $admin_reset != 1 ){
					send_email( strtolower(trim($userEmailAddress)), $core_settings['gallery_title']." - Password Reset / Account Information", $retrievedFirstName.",\n\nYour password for ".$core_settings['gallery_title']." has been reset. The login information is as follows.\n\nUsername: ".$retrievedUsername."\nPassword: ".$retrievedPassword."\n\nYou can login at: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php");
				}
				
				if( $admin_reset == 1 ){
					
					$_SESSION['reset_user'] = array( 'username' => $retrievedUsername, 'password' => $retrievedPassword);
					
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."admin/index.php?action=editusers&error=userPasswordReset");
				}else{
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."index.php?a=pr&error=success&userEmailAddress=".urlencode(strtolower(trim($userEmailAddress))));
				}
			}else{
				header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."index.php?a=pr&error=nomatch");			}
		}else{
			header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."index.php?a=pr&error=blank");
		}
		
		break;
			
	case 'vi':

		if(!file_exists($galleriesRoot.$gallery)) {
			header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
		}
		if(!file_exists($galleriesRoot.$gallery."/".$i)) {
			header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."?a=vg&g=".$gallery);
		}
	
		/* VIEW IMAGE
		   this displays the image a user selected within the current gallery
		*/
	
		get_gallery_details($gallery);
	
		if($loopvars->compare("gallery_sort_order", "") || !$loopvars->var_isset("gallery_sort_order")){
			$loopvars->set_var("gallery_sort_order", $core_settings['default_image_sort']);
		}
		
		$clsDirListingDefault = new clsDirListing($galleriesRoot.$gallery, 0);
		$clsDirListingDefault->sortOrder($loopvars->get_var("gallery_sort_order"));
		$clsDirListingDefault->extensionIncludes(array("jpg", "jpeg", "gif", "png", "ogg", "webm", "mp4"));
		$clsDirListingDefault->prefixExcludes(array("thm_", "lowres_"));
		$images=$clsDirListingDefault->getListing();
		$clsDirListingDefault="";
	
	
		//this is the array position of the current image
		if(file_exists($galleriesRoot.$gallery."/".$i)){
		    $currentimage=locateCurrentImagePosition($i, $images);
		}else{
		    $currentimage=1;
		}
		//this was safer? It will prevent roque url malformations
		$image=$images[$currentimage][0];
	
	    //encoded image name for links
	    $linksafe_image=urlencode(htmlentities($images[$currentimage][0], ENT_QUOTES));
	
		//max img count is used for next/prev
		$maximagecount=(count($images)-1);
	
		/* THEME WANTS TO KNOW NEXT/PREV IMAGES. PROVIDE TRUE OR FALSE. TRUE = LINK TO NEXT/PREV. */
		if($currentimage!=$maximagecount){
			$nextimage=urlencode(htmlentities($images[($currentimage+1)][0], ENT_QUOTES));
		}else{
			$nextimage=0;
		}
		if($currentimage!=0){
			$previmage=urlencode(htmlentities($images[($currentimage-1)][0], ENT_QUOTES));
		}else{
			$previmage=0;
		}
		$nextimagelink="$pageFileName?g=$gallery&amp;i=$nextimage&amp;a=vi&amp;cp={$currentpage}{$appendPG1}{$appendGP}";
		$previmagelink="$pageFileName?g=$gallery&amp;i=$previmage&amp;a=vi&amp;cp={$currentpage}{$appendPG1}{$appendGP}";
	
	
		//get file caption and info
		if(file_exists($galleriesRoot.$gallery."/".$image.".php")){
			include $galleriesRoot.$gallery."/".$image.".php";
		}
		

		//store loop var image caption                  
		if(trim($image_options["caption"])!=""){
			$loopvars->set_var("file_caption", $image_options["caption"]);
		}
		
		$low_resolution_image=0; //Set low res image presence to false. Set to path later if required.
		
		if(is_movie($image)){
		
			$loopvars->set_var("media_type", "movie");
	
			//movie path url
			if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
				$loopvars->set_var("view_media_path", "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/includes/view.php/".$gallery."/".$image);
			}else{
				$loopvars->set_var("view_media_path", "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/".$galleriesRoot.$gallery."/".$image);
			}
				
	
			if(can_edit($gallery, $_SESSION['s_userName'])){
				$currentURLdelete=str_replace("a=vi", "a=vg", $loopvars->get_var("current_url"));
	
				$loopvars->set_var("core_owner_options", "<div class='core_owner_options core_owner_options_vi'><a $ajaxifycaption href='admin/index.php?action=setcaption&amp;galleryname=$gallery&amp;filetoset=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='edit caption' class='caption button'>Edit Caption</a></a><a $ajaxifydelete href='admin/index.php?action=deleteimage&amp;galleryname=$gallery&amp;filetodelete=$linksafe_image&amp;kickmeto=".urlencode($currentURLdelete)."' title='delete $image' class='delete button'>Delete Movie</a></div>");
			}else if( can_manipulate_image($gallery, $linksafe_image, $_SESSION['s_userName']) ){
				$currentURLdelete=str_replace("a=vi", "a=vg", $loopvars->get_var("current_url"));
				
				$loopvars->set_var("core_owner_options", "<div class='core_owner_options core_owner_options_vi'><a $ajaxifycaption href='admin/index.php?action=setcaption&amp;galleryname=$gallery&amp;filetoset=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='edit caption' class='caption button'>Edit Caption</a></a><a $ajaxifydelete href='admin/index.php?action=deleteimage&amp;galleryname=$gallery&amp;filetodelete=$linksafe_image&amp;kickmeto=".urlencode($currentURLdelete)."' title='delete $image' class='delete button'>Delete Movie</a></div>");
			}else{
				$loopvars->set_var("core_owner_options", "");
			}
			
		//otherwise we must be an image
		}else{
			
			$loopvars->set_var("media_type", "image");
			
			$imagedimensions = getimagesize($galleriesRoot.$gallery."/".$image);
	
			//if we need a low res display image and we do not have one create it
			if( (($imagedimensions[0] > $theme_settings->get_var("image_display_width")) && ($imagedimensions[0] > $imagedimensions[1])) 
			||  (($imagedimensions[1] > $theme_settings->get_var("image_display_height")) && ($imagedimensions[1] > $imagedimensions[0])) ){

				if(!(file_exists($galleriesRoot.$gallery."/lowres_".$image))){
				    create_lowres_image($gallery, $image);
				}else{
					//if we have a lowres image let's make sure it't the correct dimensions for the theme. If not, recreate it.
					$imagedimensions = getimagesize($galleriesRoot.$gallery."/lowres_".$image);
						
					if($imagedimensions[0]!=$theme_settings->get_var("image_display_width") && $imagedimensions[1]!=$theme_settings->get_var("image_display_height")){
						create_lowres_image($gallery, $image);
					}
				}
			    
			    if(file_exists($galleriesRoot.$gallery."/lowres_".$image)){
			    	$low_resolution_image="lowres_".$image;
			    }
			}			
						
			if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
				if($low_resolution_image){
					$loopvars->set_var("view_media_path", "includes/view.php/".$gallery."/".$low_resolution_image);
				}else{
					$loopvars->set_var("view_media_path", "includes/view.php/".$gallery."/".$image);
				}
			}else{
				if($low_resolution_image){
					$loopvars->set_var("view_media_path", $galleriesRoot.$gallery."/".$low_resolution_image);
				}else{
					$loopvars->set_var("view_media_path", $galleriesRoot.$gallery."/".$image);
				}
			}
	
			if(can_edit($gallery, $_SESSION['s_userName'])){
				$currentURLdelete=str_replace("a=vi", "a=vg", $loopvars->get_var("current_url"));
	
				$loopvars->set_var("core_owner_options", "<div class='core_owner_options core_owner_options_vi'><a $ajaxifycaption href='admin/index.php?action=setcaption&amp;galleryname=$gallery&amp;filetoset=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='edit caption' class='caption button'>Edit Caption</a><a $ajaxifythumb href='admin/index.php?action=setthumb&amp;galleryname=$gallery&amp;filetoset=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='make $originalImage master thumbnail' class='setthumb button'>Make Thumbnail</a><a $ajaxifydelete href='admin/index.php?action=deleteimage&amp;galleryname=$gallery&amp;filetodelete=$linksafe_image&amp;kickmeto=".urlencode($currentURLdelete)."' title='delete $image' class='delete button'>Delete Image</a></div>");
			}else if( can_manipulate_image($gallery, $linksafe_image, $_SESSION['s_userName']) ){
				$currentURLdelete=str_replace("a=vi", "a=vg", $loopvars->get_var("current_url"));
				
				$loopvars->set_var("core_owner_options", "<div class='core_owner_options core_owner_options_vi'><a $ajaxifycaption href='admin/index.php?action=setcaption&amp;galleryname=$gallery&amp;filetoset=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='edit caption' class='caption button'>Edit Caption</a><a $ajaxifydelete href='admin/index.php?action=deleteimage&amp;galleryname=$gallery&amp;filetodelete=$linksafe_image&amp;kickmeto=".urlencode($currentURLdelete)."' title='delete $image' class='delete button'>Delete Image</a></div>");
			}else{
				$loopvars->set_var("core_owner_options", "");
			}
		}
	
		//store loop var download path
		if(!is_movie($image) && $image!='' && ($loopvars->get_var("gallery_download_policy")=='1' || $loopvars->get_var("gallery_download_policy")=='2')){
			if($low_resolution_image && $loopvars->get_var("gallery_download_policy")=='2'){
				$loopvars->set_var("image_hires", true);
				$loopvars->set_var("image_download_path", "includes/download.php/".$gallery."/".$image);
			}else{
				$loopvars->set_var("image_hires", false);
				$loopvars->set_var("image_download_path", "includes/download.php/".$gallery."/".$low_resolution_image);
			} 
		}else if(is_movie($image)){
			if($loopvars->get_var("gallery_download_policy")=='2'){
				$loopvars->set_var("movie_download", true);
				$loopvars->set_var("movie_download_path", "includes/download.php/".$gallery."/".$image);
			}
		}
	
		$core_content_temp="<div class='core_container'>"; //start core content wrapper
		$core_content_temp.=get_theme_image($loopvars, $theme_settings, $core_settings);
		$core_content_temp.="</div>"; //end core content wrapper
		
		
		/* RUN THEME */
		prepare_theme_vars();
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

	case 'vg':
	  	/* VIEW GALLERY
		   this is what spits out the images within the requested gallery
		*/
	
		if(!file_exists($galleriesRoot.$gallery)) {
			header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
		}
	
		get_gallery_details($gallery);
	
		if($loopvars->compare("gallery_sort_order", "") || !$loopvars->var_isset("gallery_sort_order")){
			$loopvars->set_var("gallery_sort_order", $core_settings['default_image_sort']);
		}
	
	        //users would probably prefer the default sort order so when visiting off the main gallery we user $r for reset
		if($r==1){
			$galleryViewSortOrder=$loopvars->get_var("gallery_sort_order");
			$_SESSION['s_galleryViewSortOrder']=$loopvars->get_var("gallery_sort_order");
		}
	
		if(!$currentpage){
		    $currentpage=1;
		}
		
	    /* GET THE DIRECTORY LISTING OF THE GALLERIES DIRECTORY. EACH GALLERY IS IN ITS OWN
	       DIRECTORY. IF WE ARE DISPLAYING PERSONAL GALLERIES WE NEED TO TAKE ONE ADDITIONAL STEP
	       AND CHECK DIRNAME FORREQUESTED USERS PERSONAL GALLERY STRING.
	    */
	    $clsDirListingDefault = new clsDirListing($galleriesRoot.$gallery, 0);
	    $clsDirListingDefault->sortOrder($galleryViewSortOrder);
		$clsDirListingDefault->extensionIncludes(array("jpg", "jpeg", "gif", "png", "ogg", "webm", "mp4"));
		$clsDirListingDefault->prefixExcludes(array("thm_", "lowres_"));
		$images=$clsDirListingDefault->getListing();
		$clsDirListingDefault="";
	
		$maximagesperpage=$theme_settings->get_var("gallery_images_per_page");
		$gallerycount=($currentpage-1)*$maximagesperpage;
	
	    $pages=intval((count($images)-1) / $maximagesperpage);
	    if($pages < ((count($images)) / $maximagesperpage)){
	    	$extrapage=1;
		} else {
			$extrapage=0;
		}
		$totalpages=$pages+$extrapage;
	
		//<!-- figure out how many pages we need -->
		if($currentpage>1){
		  	$prevtemp=($gallerycount-$maximagesperpage);
		  	$linkpage=$currentpage-1;
		    $previouspage="<a href='$pageFileName?cp={$linkpage}&amp;a=vg&amp;g={$gallery}{$appendPG1}{$appendGP}' $target>&lt;</a>";
		}
		
		if($totalpages>$currentpage){
		  	$nexttemp=($gallerycount+$maximagesperpage);
		  	$linkpage=$currentpage+1;
			$nextpage="<a href='$pageFileName?cp={$linkpage}&amp;a=vg&amp;g={$gallery}{$appendPG1}{$appendGP}' $target>&gt;</a>";
		}
	
		//Work out page breaks. We dont want to overload our users with every page in the gallery...
		$PREpageoutstring="";
		$POSTpageoutstring="";
	    if(intval($currentpage) < 10){ //Were in the first 10 pages
			$startPage=1;
			$endPage=10;
	
			if($totalpages<$endPage){
				$endPage=$totalpages;
			}
	
			if($totalpages!=$endPage){
				$POSTpageoutstring="&nbsp;<b>...</b>&nbsp;<a href='$pageFileName?cp={$totalpages}&amp;a=vg&amp;g={$gallery}{$appendPG1}{$appendGP}' $target>$totalpages</a>";
			}	
		}else{
			if(intval(substr($currentpage, strlen($currentpage)-1, 1))==0){ //were past the first 10 pages and its a "milestone"
				$startPage=intval($currentpage)-1;
				$endPage=intval($currentpage)+10;
	
				if($totalpages<$endPage){
					$endPage=$totalpages;
					$POSTpageoutstring="";
				}
	
				$PREpageoutstring="<a href='$pageFileName?cp=1&amp;a=vg&amp;g={$gallery}{$appendPG1}{$appendGP}' $target>1</a>&nbsp;&nbsp;<b>...</b>&nbsp;";
				if($totalpages!=$endPage){
					$POSTpageoutstring="&nbsp;<b>...</b>&nbsp;&nbsp;<a href='$pageFileName?cp={$totalpages}&amp;a=vg&amp;g={$gallery}{$appendPG1}{$appendGP}' $target>$totalpages</a>";
				}
			}else{ //were past the first 10 pages but its not a "milestone"
				$tempPageCalc=$currentpage;
				$tempPageCalc=substr_replace($currentpage, 0, strlen($currentpage)-1, 1);
				$startPage=$tempPageCalc-1;
				$endPage=$tempPageCalc+10;
	
				if($totalpages<$endPage){
					$endPage=$totalpages;
					$POSTpageoutstring="";
				}
	
				$PREpageoutstring="<a href='$pageFileName?cp=1&amp;a=vg&amp;g={$gallery}{$appendPG1}{$appendGP}' $target>1</a>&nbsp;&nbsp;<b>...</b>&nbsp;";
				if($totalpages!=$endPage){
					$POSTpageoutstring="&nbsp;<b>...</b>&nbsp;&nbsp;<a href='$pageFileName?cp={$totalpages}&amp;a=vg&amp;g={$gallery}{$appendPG1}{$appendGP}' $target>$totalpages</a>";
				}
			}
		}
	
	    $pageoutstringA=$PREpageoutstring;
	  	//compile extra page links
	  	for($z=$startPage; $z < $endPage+1; $z++){
	  		$tempcount=(($z*$maximagesperpage)-$maximagesperpage);
	
			if($z==$currentpage){
			    $curpagemarker1='  [&nbsp;';
			    $curpagemarker2='&nbsp;]';
			    $curpageclasser="class='current_page page'";
			}else{
			    $curpagemarker1='';
			    $curpagemarker2='';
			    $curpageclasser="class='page'";
			}
	
			if($totalpages!=1){
				$pageoutstringA.=" <a href='$pageFileName?cp={$z}&amp;a=vg&amp;g={$gallery}{$appendPG1}{$appendGP}' $target $curpageclasser>$curpagemarker1$z$curpagemarker2</a>&nbsp;";
			}
	  	}
		$pageoutstringA.=$POSTpageoutstring;
	
		if(can_edit($gallery, $_SESSION['s_userName'])){
			$templateUpload="<a title='upload images' $ajaxifyupload href='admin/index.php?action=uploadimages&amp;galleryname={$gallery}&amp;cms={$cms}&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' class='gallery_title_upload_nav button'>Upload</a>";
			$templateEditGallery="<a title='edit gallery properties' $ajaxifynewgallery href='admin/index.php?action=editgallerydetails&amp;galleryname={$gallery}&amp;cms={$cms}&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' class='gallery_title_edit_nav button'>Edit</a>";
			$templateDeleteGallery="<a title='delete gallery' $ajaxifydelete href='admin/index.php?action=deletegallery&amp;galleryname={$gallery}&amp;cms={$cms}&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' class='gallery_title_delete_nav button'>Delete Gallery</a>";
			
			if( $core_settings['license'] == "paid" ){
				$templateGalleryCollaborators="<a title='delete gallery' $ajaxifydelete href='admin/index.php?action=editgallerycollaborators&amp;galleryname={$gallery}&amp;cms={$cms}&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' class='gallery_title_delete_nav button'>Collaborators</a>";
			}else{
				$templateGalleryCollaborators="";
			}
		}else if(can_collaborate($gallery, $_SESSION['s_userName'])){
			$templateUpload="<a title='upload images' $ajaxifyupload href='admin/index.php?action=uploadimages&amp;galleryname={$gallery}&amp;cms={$cms}&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' class='gallery_tile_upload_nav button'>Upload</a>";
			$templateEditGallery="";
			$templateDeleteGallery="";
			$templateGalleryCollaborators="";
		}else{
			$templateUpload="";
			$templateEditGallery="";
			$templateDeleteGallery="";
			$templateGalleryCollaborators="";
		}
	
		$templateSortBy="<form method='post' action='index.php?a=$a&amp;g={$gallery}&amp;q=$searchTerm' enctype='multipart/form-data' class='sortForm'><input type='hidden' name='cms' value='{$cms}' /><select name='galleryViewSortOrder' class='sort_form vg_sort_form' onChange='this.parentNode.submit();'>";
		if($_SESSION['s_galleryViewSortOrder']=='dateDESC'){
			$templateSortBy.="<option value='dateDESC' SELECTED>Newest to Oldest</option>";
		}else{
			$templateSortBy.="<option value='dateDESC'>Newest to Oldest</option>";
		}
		if($_SESSION['s_galleryViewSortOrder']=='dateASC'){
			$templateSortBy.="<option value='dateASC' SELECTED>Oldest to Newest</option>";
		}else{
			$templateSortBy.="<option value='dateASC'>Oldest to Newest</option>";
		}
		if($_SESSION['s_galleryViewSortOrder']=='titleAlphabetical'){
			$templateSortBy.="<option value='titleAlphabetical' SELECTED>Image Title</option>";
		}else{
			$templateSortBy.="<option value='titleAlphabetical'>Image Title</option>";
		}
		$templateSortBy.="</select>&nbsp;<input type='submit' value='go' class='sort_form_button' /></form>";
		$loopvars->set_var("sort_form", $templateSortBy);
	

		$core_content_temp="<div class='core_container'>"; //start core content wrapper
		
	  	/* DISPLAY IMAGE TILES */
	  	if($images && ($gallerycount < (count($images)))){
	
			$image_output_counter=0;
	  		for($y = $gallerycount; $y < ($gallerycount+$maximagesperpage); $y++){
	
				$image_output_counter++;
				
			    if(!isset($images[$y]))
	      			break; // break out of loop
	
	  		    //first column of images
	  		    $v_images=$images[$y][0];
	
	            //encoded image name for links
	            $linksafe_image=urlencode(htmlentities($images[$y][0], ENT_QUOTES));

				//if we need a thumbnail image and we do not have one create it
				if(!(file_exists($galleriesRoot.$gallery."/thm_".$v_images))){
					create_thumbnail_image($gallery, $v_images);
		  		}else{
		  			//if we have a thumbnail image let's make sure it't the correct dimensions for the theme. If not, recreate it.
		  			$imagedimensions = getimagesize($galleriesRoot.$gallery."/thm_".$v_images);
		  			
		  			if($imagedimensions[0]!=$theme_settings->get_var("thumbnail_width") && $imagedimensions[1]!=$theme_settings->get_var("thumbnail_height")){
		  				create_thumbnail_image($gallery, $v_images);
		  			}
		  		}
	
				/* CHECK FOR IMAGE OR MOVIE TYPE AND PASS INFO TO THEME TO DISPLAY APPROPRIATE THUMBNAIL.*/
				$imageExtension=strtolower(substr($v_images, (strlen($v_images)-3), 3));
				if ($imageExtension=='gif' || $imageExtension=='jpg' || strtolower(substr($v_images, (strlen($v_images)-4), 4))=='jpeg'||$imageExtension=='png'){
	
					if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
						if(file_exists($galleriesRoot.$gallery."/thm_".$v_images)){
							$loopvars->set_var("thumbnail_media_path", "includes/view.php/".$gallery."/thm_".$v_images);
						}else{
							$loopvars->set_var("thumbnail_media_path", $core_settings['theme_name']."/images/na.png");
						}
						$loopvars->set_var("thumbnail_link", $pageFileName."?a=vi&amp;g=".$gallery."&i=".$linksafe_image."&cp=".$currentpage.$appendPG1.$appendGP);
					}else{
						if(file_exists($galleriesRoot.$gallery."/thm_".$v_images)){
							$loopvars->set_var("thumbnail_media_path", $galleriesRoot.$gallery."/thm_".$v_images);
						}else{
							$loopvars->set_var("thumbnail_media_path", $core_settings['theme_name']."/images/na.png");
						}
						$loopvars->set_var("thumbnail_link", $pageFileName."?a=vi&amp;g=".$gallery."&i=".$linksafe_image."&cp=".$currentpage.$appendPG1.$appendGP);	
					}
	
					if(can_edit($gallery, $_SESSION['s_userName'])){
						$loopvars->set_var("core_owner_options", "<div class='owner_options'><a $ajaxifycaptionNoRefresh href='admin/index.php?action=setcaption&amp;galleryname=$gallery&amp;filetoset=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='edit caption' class='caption button'>Edit Caption</a><a $ajaxifythumb href='admin/index.php?action=setthumb&amp;galleryname=$gallery&amp;filetoset=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='make $linksafe_image master thumbnail button' class='setthumb button'>Make Thumbnail</a><a $ajaxifydelete href='admin/index.php?action=deleteimage&amp;galleryname=$gallery&amp;filetodelete=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='delete $v_images' class='delete button'>Delete Image</a></div>");
					}else if( can_manipulate_image($gallery, $linksafe_image, $_SESSION['s_userName']) ){
						$loopvars->set_var("core_owner_options", "<div class='owner_options'><a $ajaxifycaptionNoRefresh href='admin/index.php?action=setcaption&amp;galleryname=$gallery&amp;filetoset=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='edit caption' class='caption button'>Edit Caption</a><a $ajaxifydelete href='admin/index.php?action=deleteimage&amp;galleryname=$gallery&amp;filetodelete=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='delete $v_images' class='delete button'>Delete Image</a></div>");
					}else{
						$loopvars->set_var("core_owner_options", "");
					}
	
				} else if(is_movie($v_images)){
					
					$loopvars->set_var("thumbnail_media_path", $core_settings['theme_name']."/images/playMovie.png");
					$loopvars->set_var("thumbnail_link", $pageFileName."?a=vi&amp;g=".$gallery."&i=".$linksafe_image."&cp=".$currentpage.$appendPG1.$appendGP);
	
					if(can_edit($gallery, $_SESSION['s_userName'])){
						$loopvars->set_var("core_owner_options", "<div class='owner_options'><a $ajaxifycaptionNoRefresh href='admin/index.php?action=setcaption&amp;galleryname=$gallery&amp;filetoset=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='edit caption' class='caption button'>Edit Caption</a><a $ajaxifydelete href='admin/index.php?action=deleteimage&amp;galleryname=$gallery&amp;filetodelete=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='delete $v_images' class='delete button'>Delete Movie</a></div>");
					}else if( can_manipulate_image($gallery, $linksafe_image, $_SESSION['s_userName']) ){
						$loopvars->set_var("core_owner_options", "<div class='owner_options'><a $ajaxifycaptionNoRefresh href='admin/index.php?action=setcaption&amp;galleryname=$gallery&amp;filetoset=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='edit caption' class='caption button'>Edit Caption</a><a $ajaxifydelete href='admin/index.php?action=deleteimage&amp;galleryname=$gallery&amp;filetodelete=$linksafe_image&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' title='delete $v_images' class='delete button'>Delete Movie</a></div>");
					}else{
						$loopvars->set_var("core_owner_options", "");
					}
	                                			
				}
				if($image_output_counter == $maximagesperpage || $y == count($images)-1){
					$loopvars->set_var("last_tile", true);
					$last_image=1;
				}else{
					$loopvars->set_var("last_tile", false);
					$last_image=0;
				}
				
				//engage theme
				$core_content_temp.=get_theme_thumbnail_tile($loopvars, $theme_settings, $core_settings);
	
	  		}

			
	  	} else {
	  		//there are no images so exit gracefully
			$core_content_temp.="<div class='notice'><p>There are no images in this gallery.</p></div>";
		}
	
		$core_content_temp.="</div>"; //close core content wrapper
		
		
		
		/* RUN THEME */
		prepare_theme_vars();
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
	
		if(isset($_SESSION['s_userName'])){
			$templateCreateGallery="<a $ajaxifynewgallery href='admin/index.php?action=editgallerydetails&amp;galleryname=NEW&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' class='create_new_gallery_button button'>Create New Gallery</a>";
		}
	
		/* DISPLAY GALLERIES
		   the default action is to list all galleries (or virtual personal galleries if requested)
		*/
	
		if(!$gp){
			$gp=1;
		}
	
		/*
		Create a new directory listing instance and process directory listing for master gallery images
		*/
		
		
		
		switch($a){
			case 's':	
				//The user is searching the available galleries. Get the search listing.
				$clsDirListingDefault = new clsDirListing($galleriesRoot, 6);
				$clsDirListingDefault->sortOrder($galleryListingSortOrder);
				$clsDirListingDefault->includes($searchTerm);
				$clsDirListingDefault->excludes(array("logs"));
				$galleries=$clsDirListingDefault->getListing();
				$clsDirListingDefault="";
				break;
		
			default:
				//Get all galleries
				if($pg!=""){
					$clsDirListingDefault = new clsDirListing($galleriesRoot, 3);
					$clsDirListingDefault->prefixIncludes(array($pg."_"));
				}else{
					$clsDirListingDefault = new clsDirListing($galleriesRoot, 2);
				}
				$clsDirListingDefault->sortOrder($galleryListingSortOrder);
				$clsDirListingDefault->excludes(array("logs"));
				$galleries=$clsDirListingDefault->getListing();
				$clsDirListingDefault="";
				break;
		}	
	
		$maxgalleriesperpage=$theme_settings->get_var("galleries_per_page");
		$pages=intval((count($galleries)-1) / $maxgalleriesperpage);
		if($pages < ((count($galleries)) / $maxgalleriesperpage)){
			$extrapage=1;
		} else {
			$extrapage=0;
		}
		$totalpages=$pages+$extrapage;
	
		$gallerycount=($gp-1)*$maxgalleriesperpage;
	
		/* FIGURE OUT HOW MANY PAGES WERE GOING TO NEED */
		if($gp>1){
			$prevtemp=($gallerycount-$maxgalleriesperpage);
			$linkpage=$gp-1;
			$previouspage="<a href='$pageFileName?gp={$linkpage}{$appendPG1}' $target>&lt;</a>";
		}
	
		if($totalpages>$gp){
			$nexttemp=($gallerycount+$maxgalleriesperpage);
			$linkpage=$gp+1;
			$nextpage="<a href='$pageFileName?gp={$linkpage}{$appendPG1}' $target>&gt;</a>";
		}
	
		$pages=intval((count($galleries)-1) / $maxgalleriesperpage);
		if($pages < ((count($galleries)) / $maxgalleriesperpage)){
			$extrapage=1;
		} else {
			$extrapage=0;
		}
		$totalpages=$pages+$extrapage;
	
		//Work out page breaks. We dont want to overload our users with every page in the gallery...
		$PREpageoutstring="";
		$POSTpageoutstring="";
		if(intval($gp) < 10){ //Were in the first 10 pages
			$startPage=1;
			$endPage=10;
	
			if($totalpages<$endPage){
				$endPage=$totalpages;
			}
	
			if($totalpages!=$endPage){
				$POSTpageoutstring="&nbsp;<b>...</b>&nbsp;<a href='$pageFileName?gp={$totalpages}{$appendPG1}' $target>$totalpages</a>";
			}
		}else{
			if(intval(substr($gp, strlen($gp)-1, 1))==0){ //were past the first 10 pages and its a "milestone"
				$startPage=intval($gp)-1;
				$endPage=intval($gp)+10;
	
				if($totalpages<$endPage){
					$endPage=$totalpages;
					$POSTpageoutstring="";
				}
	
				$PREpageoutstring="<a href='$pageFileName?gp=1{$appendPG1}' $target>1</a>&nbsp;&nbsp;<b>...</b>&nbsp;";
				if($totalpages!=$endPage){
					$POSTpageoutstring="&nbsp;<b>...</b>&nbsp;&nbsp;<a href='$pageFileName?gp={$totalpages}{$appendPG1}' $target>$totalpages</a>";
				}
			}else{ //were past the first 10 pages but its not a "milestone"
				$tempPageCalc=$gp;
				$tempPageCalc=substr_replace($gp, 0, strlen($gp)-1, 1);
				$startPage=$tempPageCalc-1;
				$endPage=$tempPageCalc+10;
	
				if($totalpages<$endPage){
					$endPage=$totalpages;
					$POSTpageoutstring="";
				}
	
				$PREpageoutstring="<a href='$pageFileName?gp=1{$appendPG1}' $target>1</a>&nbsp;&nbsp;<b>...</b>&nbsp;";
				if($totalpages!=$endPage){
					$POSTpageoutstring="&nbsp;<b>...</b>&nbsp;&nbsp;<a href='$pageFileName?gp={$totalpages}{$appendPG1}' $target>$totalpages</a>";
				}
			}
		}
	
		/* LOOP THROUGH THE PAGE COUNT AND CREATE THE OUTPUT FOR USE IN OUR THEME FOR THE PAGE LINKS.
		   WE WILL DENOTE THE CURRENT PAGE WITH BRACKETS. IF THE THEME WANTS TO DO SOMETHING DIFFERENT
		   THEY CAN REPLACE THE CHARS.
		*/
		$pageoutstringA=$PREpageoutstring;
		//compile extra page links
		for($z=$startPage; $z < $endPage+1; $z++){
			$tempcount=(($z*$maxgalleriesperpage)-$maxgalleriesperpage);
	
			if($z==$gp){
				$curpagemarker1='  [&nbsp;';
				$curpagemarker2='&nbsp;]';
				$curpageclasser="class='current_page page'";
			}else{
				$curpagemarker1='';
				$curpagemarker2='';
				$curpageclasser="class='page'";
			}
	
			if($totalpages!=1){
				$pageoutstringA.=" <a href='$pageFileName?gp={$z}{$appendPG1}' $target $curpageclasser>$curpagemarker1$z$curpagemarker2</a>&nbsp;";
			}
		}
		$pageoutstringA.=$POSTpageoutstring;
	
		switch($a){
			case 's':
				$sortAppend="?a=".$a."&amp;q=".$searchTerm.$appendPG1;
				break;
			
			default:
				$sortAppend=$appendPG2;
				break;
		}
			
		$templateSortBy="<form method='post' action='index.php{$sortAppend}' enctype='multipart/form-data' class='sortForm'><input type='hidden' name='cms' value='{$cms}' /><select name='galleryListingSortOrder' class='sort_form listing_sort_form' onChange='this.parentNode.submit();'>";
		if($_SESSION['s_galleryListingSortOrder']=='dateDESC'){
			$templateSortBy.="<option value='dateDESC' SELECTED>Newest to Oldest</option>";
		}else{
			$templateSortBy.="<option value='dateDESC'>Newest to Oldest</option>";
		}
		if($_SESSION['s_galleryListingSortOrder']=='dateASC'){
			$templateSortBy.="<option value='dateASC' SELECTED>Oldest to Newest</option>";
		}else{
			$templateSortBy.="<option value='dateASC'>Oldest to Newest</option>";
		}
		if($_SESSION['s_galleryListingSortOrder']=='dateModifiedDESC'){
			$templateSortBy.="<option value='dateModifiedDESC' SELECTED>Recently Modified</option>";
		}else{
			$templateSortBy.="<option value='dateModifiedDESC'>Recently Modified</option>";
		}
		if($_SESSION['s_galleryListingSortOrder']=='titleAlphabetical'){
			$templateSortBy.="<option value='titleAlphabetical' SELECTED>Gallery Title</option>";
		}else{
			$templateSortBy.="<option value='titleAlphabetical'>Gallery Title</option>";
		}
		if(!$pg){
			if($_SESSION['s_galleryListingSortOrder']=='author'){
				$templateSortBy.="<option value='author' SELECTED>Author</option>";
			}else{
				$templateSortBy.="<option value='author'>Author</option>";
			}
		}
		$templateSortBy.="</select>&nbsp;<input type='submit' value='go' class='sort_form_button' /></form>";
		$loopvars->set_var("sort_form", $templateSortBy);
	
		$core_content_temp="";
	
		//<!-- this is where we display the actual gallery choices and page links -->
		if($galleries && ($gallerycount < (count($galleries)))){
	
			/* START GALLERY TILES */
			$core_content_temp.="<div class='core_container'><div class='gallery_listing'>";
	
			$gallery_output_counter=0;
			for($y = $gallerycount; $y < ($gallerycount+$maxgalleriesperpage); $y++){
	
				$gallery_output_counter++;
				
				if($galleryListingSortOrder=="dateDESC" || $galleryListingSortOrder=="dateASC" ||
					$galleryListingSortOrder=="dateModifiedDESC" || $galleryListingSortOrder=="dateModifiedASC" ||
					$galleryListingSortOrder=="titleAlphabetical" || $galleryListingSortOrder=="author"){
					$v_gallery=$galleries[$y][0];
				}else{
					$V_gallery=$galleries[$y];
				}
	
				/* RETRIEVE INDIVIDUAL GALLERY DATA	*/
				get_gallery_details($v_gallery);
	 
	 			if( isset( $_SESSION['s_userName'] ) ){
					if(can_edit($v_gallery, $_SESSION['s_userName'])){
						$templateUpload="<a title='upload images' $ajaxifyupload href='admin/index.php?action=uploadimages&amp;galleryname={$v_gallery}&amp;cms={$cms}&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' class='gallery_tile_upload_nav button'>Upload</a>";
						$templateEditGallery="<a title='edit gallery properties' $ajaxifynewgallery href='admin/index.php?action=editgallerydetails&amp;galleryname={$v_gallery}&amp;cms={$cms}&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' class='gallery_tile_edit_nav button'>Edit</a>";
						$templateDeleteGallery="<a title='delete gallery' $ajaxifydelete href='admin/index.php?action=deletegallery&amp;galleryname={$v_gallery}&amp;cms={$cms}&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' class='gallery_tile_delete_nav button'>Delete Gallery</a>";
						
						if( $core_settings['license'] == "paid" ){
							$templateGalleryCollaborators="<a title='edit collaborators' $ajaxifydelete href='admin/index.php?action=editgallerycollaborators&amp;galleryname={$v_gallery}&amp;cms={$cms}&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' class='gallery_tile_delete_nav button'>Collaborators</a>";
						}else{
							$templateGalleryCollaborators="";
						}
					}else if(can_collaborate($v_gallery, $_SESSION['s_userName'])){
						$templateUpload="<a title='upload images' $ajaxifyupload href='admin/index.php?action=uploadimages&amp;galleryname={$v_gallery}&amp;cms={$cms}&amp;kickmeto=".$loopvars->get_var("current_url_linksafe")."' class='gallery_tile_upload_nav button'>Upload</a>";
						$templateEditGallery="";
						$templateDeleteGallery="";
						$templateGalleryCollaborators="";
					}else{
						$templateUpload="";
						$templateEditGallery="";
						$templateDeleteGallery="";
						$templateGalleryCollaborators="";
					}
				}else{
					$templateUpload="";
					$templateEditGallery="";
					$templateDeleteGallery="";
					$templateGalleryCollaborators="";
				}
				
	
				/* THE USER MIGHT HAVE SELECTED THEIR OWN DEFAULT SAMPLE IMAGE FOR THIS GALLERY.
				   IF THEY DID THEN CHECK FOR ITS EXISTENCE AND RETURN ITS PRESENCE. OTHERWISE LETS FIND
				   OUR OWN SAMPLE (THE FIRST IMAGE IN THE GALLERY)
				*/
				$sampleimages=array();
				$thm_set=0;
				$galleryThumbnail="";
				if (file_exists($galleriesRoot.$v_gallery."/thm.php")) {
					include $galleriesRoot.$v_gallery."/thm.php";
					if(file_exists($galleriesRoot.$v_gallery."/".$galleryThumbnail)){
						$thm_set=1;
					}
				}
				if($thm_set==0){
					$clsDirListingSample = new clsDirListing($galleriesRoot.$v_gallery, 5);
					$clsDirListingSample->sortOrder("titleAlphabetical");
					$clsDirListingSample->extensionIncludes(array("jpg", "gif", "png", "jpeg"));
					$clsDirListingSample->prefixIncludes(array("thm_"));
					$sampleimages=$clsDirListingSample->getListing();
					$clsDirListingSample="";
				} 
	
				$loopvars->set_var("personal_gallery_link_gallery_tile", "{$pageFileName}?pg={$loopvars->get_var("gallery_poster")}");
				$loopvars->set_var("create_gallery_block", $templateCreateGallery);
				$loopvars->set_var("delete_gallery_block", $templateDeleteGallery);
				$loopvars->set_var("edit_gallery_block", $templateEditGallery);
				$loopvars->set_var("upload_files_block", $templateUpload);
				$loopvars->set_var("edit_collaborators_block", $templateGalleryCollaborators);
				$loopvars->set_var("link_target", $target);
				$loopvars->set_var("theme_path", $core_settings['theme_name']);
				$loopvars->set_var("gallery_link", "$pageFileName?a=vg&amp;g={$v_gallery}&amp;r=1{$appendPG1}{$appendGP}");
				$loopvars->set_var("personal_gallery_user", $pg);

				/* IF THE USER SPECIFIED A THUMB USE IT, OTHERWISE DISPLAY THE FIRST THUMB WE ENCOUNTER */
				if($thm_set==1){
				
					//if we have a thumbnail image let's make sure it't the correct dimensions for the theme. If not, recreate it.
					$imagedimensions = getimagesize($galleriesRoot.$v_gallery."/".$galleryThumbnail);
						
					if($imagedimensions[0]!=$theme_settings->get_var("thumbnail_width") && $imagedimensions[1]!=$theme_settings->get_var("thumbnail_height")){
						$image_to_create=str_replace("thm_", "", $galleryThumbnail);
						create_thumbnail_image($v_gallery, $image_to_create);
					}
				
					if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
						$loopvars->set_var("thumbnail_media_path", "includes/view.php/".$v_gallery."/".$galleryThumbnail);
					}else{
						$loopvars->set_var("thumbnail_media_path", $galleriesRoot.$v_gallery."/".$galleryThumbnail);
					}
				}else if (count($sampleimages) > 0){
					
					//if we have a thumbnail image let's make sure it't the correct dimensions for the theme. If not, recreate it.
					$imagedimensions = getimagesize($galleriesRoot.$v_gallery."/".$sampleimages[0][0]);
						
					if($imagedimensions[0]!=$theme_settings->get_var("thumbnail_width") && $imagedimensions[1]!=$theme_settings->get_var("thumbnail_height")){
						$image_to_create=str_replace("thm_", "", $sampleimages[0][0]);
						create_thumbnail_image($v_gallery, $image_to_create);
					}
					
					if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
						$loopvars->set_var("thumbnail_media_path", "includes/view.php/".$v_gallery."/".$sampleimages[0][0]);
					}else{
						$loopvars->set_var("thumbnail_media_path", $galleriesRoot.$v_gallery."/".$sampleimages[0][0]);
					}
				}else{
					$loopvars->set_var("thumbnail_media_path", $core_settings['theme_name']."/images/na.png");
				}

				if($gallery_output_counter == $maxgalleriesperpage || $y == count($galleries)-1){
					$loopvars->set_var("last_tile", true);
				}else{
					$loopvars->set_var("last_tile", false);
				}
				
				$core_content_temp.=get_theme_gallery_tile($loopvars, $theme_settings, $core_settings);	
	
				/* IF THIS IS THE END OF THE ROW THEN ITS TIME TO END THIS ROW AND FLAG FOR NEW ROW.
				IF WERE OUT OF IMAGES THEN BREAK LOOP
				*/

				//check for a break again
				if(($y) == (count($galleries)-1)){
					break;
				}
			}
			$core_content_temp.="</div></div>";
	
		}else{
		
			switch($a){
				case 's':
					$core_content_temp.="<div class='notice'><p>There are no galleries containing the search term: ".$searchTerm."</p>";

					break;
					
				default:
					$core_content_temp.="<div class='notice'>
										 <p>There are no galleries.</p>
										 <p>Login to your account, create a gallery, and begin populating it with images!</p>
										 </div>";

					break;
			}		
		}
	
	
		/* RUN THEME */
		prepare_theme_vars();
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
}

unset($loopvars);
unset($theme_settings);
unset($core_settings);
?>
