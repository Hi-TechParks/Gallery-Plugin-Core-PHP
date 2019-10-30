<?php
//#######################################################################\\
//# Author: Christopher Schiffner                                       #\\
//# Filename: dosub.php                                                 #\\
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

include_once '../conf/config.php';
include_once '../includes/class/loopvars.php';
include_once '../includes/version.php';

$loopvars = new clsLoopVars();
$theme_settings = new clsLoopVars();
include '../includes/functions.php';

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

$galleriesRoot="../galleries/";

//theme
$loopvars->set_var("theme_path", "../themes/".$core_settings['theme_name']);
include_once '../themes/'.$core_settings['theme_name'].'/options.php';

session_name($core_settings['session_identifier']);
session_start();

$includeFlag='1'; //for include detection later.

$username=$_SESSION['s_userName'];

if(isset($_GET['action'])){
	$action=$_GET['action'];
}elseif (isset($_POST['action'])){
	$action=$_POST['action'];
}

if(isset($_GET['vm'])){
        $vm=$_GET['vm'];
}else{
        $vm='false';
}

if(isset($_POST['kickmeto'])){
	$kickmeto=urldecode($_POST['kickmeto']);
}else if(isset($_GET['kickmeto'])){
	$kickmeto=urldecode($_GET['kickmeto']);
}else{
	$kickmeto=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php";
}
if(trim($kickmeto)==""){
	$kickmeto=$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php";
}

//you must be a valid user to do this
if(isset($_SESSION['s_userName'])) {

	switch($action){
	
		case 'confirmdeleteimage':

				$galleryname='';
				if(isset($_GET['galleryname'])){
					$galleryname=$_GET['galleryname'];
				}else if(isset($_POST['galleryname'])){
					$galleryname=$_POST['galleryname'];
				}

				$filetodelete='';
				if(isset($_GET['filetodelete'])){
					$filetodelete=$_GET['filetodelete'];
				}else if(isset($_POST['filetodelete'])){
					$filetodelete=$_POST['filetodelete'];
				}
				$filetodelete=html_entity_decode($filetodelete, ENT_QUOTES);

				if(!can_edit($galleryname, $_SESSION['s_userName']) && !can_manipulate_image($galleryname, $filetodelete, $_SESSION['s_userName'])){
					header("Location: http://$kickmeto");
					break;
				}
				
				//Security check. make sure this is a supported file type.
				if(!is_image($filetodelete) && !is_movie($filetodelete)){
					log_action("DELETE_FILE", "FAILED", $galleryname, "ATTEMPT TO DELETE UNSUPPORTED FILE - {$filetodelete}");
					die("NO HACK PROTECTION ACTIVATED");
					exit(0);
				}

                $completepath=$galleriesRoot.$galleryname."/".$filetodelete;
				$completepathconfig=$galleriesRoot.$galleryname."/".$filetodelete.".php";
				$completepaththm=$galleriesRoot.$galleryname."/thm_".$filetodelete;
				$completepathlowres=$galleriesRoot.$galleryname."/lowres_".$filetodelete;

				@unlink($completepath);
				@unlink($completepathconfig);
				@unlink($completepaththm);
				@unlink($completepathlowres);

				log_action("DELETE_FILE", "SUCCESS", $galleryname, "DELETED FILE - {$filetodelete}");
				
				if($vm!='ajax'){
				    header("Location: http://$kickmeto");
				    break;
                }else{
					echo "<script type='text/javascript'>parent.$.fn.colorbox.close();</script>";
                }
				break;

		case 'confirmdeletegallery':

				$galleryname='';
				if(isset($_POST['galleryname'])){
					$galleryname=$_POST['galleryname'];
				}else if(isset($_GET['galleryname'])){ 
					$galleryname=$_GET['galleryname'];
				}

				if(!can_edit($galleryname, $_SESSION['s_userName'])){
					header("Location: http://$kickmeto");
					break;
				}

				$completepath=$galleriesRoot.$galleryname."/";

				//RETRIEVE GALLERY FILE LISTING
				$g_Listing2 = dir($galleriesRoot.'/'.$galleryname);
				while($entry = $g_Listing2->read()) {
					if( ($entry!="." && $entry!="..") ){
						@unlink($completepath.$entry);
					}
				}
				$g_Listing2->close();

				@rmdir($completepath);

				log_action("DELETE_GALLERY", "SUCCESS", $galleryname, "DELETED GALLERY - {$galleryname}");

				if($vm!='ajax'){
					$kickmeto=str_replace("a=vg", "a=", $kickmeto);
					header("Location: http://".$kickmeto);
					break;
				}else{
					echo "<script type='text/javascript'>parent.$.fn.colorbox.close();</script>";
				}

				break;

		case 'saveimagecaption':

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
				
				if(!can_edit($galleryname, $_SESSION['s_userName']) && !can_manipulate_image($galleryname, $filetoset, $_SESSION['s_userName']) ){
					header("Location: http://$kickmeto");
					break;
				}

				$caption='';
				if(isset($_GET['caption'])){
					$caption=htmlentities(trim(stripslashes($_GET['caption'])), ENT_QUOTES);
				}else if(isset($_POST['caption'])){ 
					$caption=htmlentities(trim(stripslashes($_POST['caption'])), ENT_QUOTES);
				}

				//Security check. make sure this is a supported file type.
				if(!is_image($filetoset) && !is_movie($filetoset)){
					log_action("SET_CAPTION", "FAILED", $galleryname, "ATTEMPT TOSET CAPTION ON UNSUPPORTED FILE - {$filetoset}");
					die("NO HACK PROTECTION ACTIVATED");
					exit(0);
				}
                         
				update_image_option($galleryname, $filetoset, "caption", $caption);

				log_action("SET_CAPTION", "SUCCESS", $galleryname, "SAVED IMAGE CAPTION - {$filetoset} - $caption");

				if($vm!='ajax'){
					header("Location: http://$kickmeto");
					break;
				}else{
					echo "<script type='text/javascript'>parent.$.fn.colorbox.close();</script>";
				}       
                        
				break;
				
		case 'confirmsetthumb':

				$galleryname='';
				if(isset($_GET['galleryname'])){
					$galleryname=$_GET['galleryname'];
				}else if(isset($_POST['galleryname'])){
					$galleryname=$_POST['galleryname'];
				}

				if(!can_edit($galleryname, $_SESSION['s_userName'])){
					header("Location: http://$kickmeto");
					break;
				}
                        
				if(is_movie($filetoset)){
					if($vm!='ajax'){
						if($kickmeto!=''){
							header("Location: http://$kickmeto");
							break;
						}else{
							header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php?a=vg&g=$galleryname");
							break;
						}
					}else{
						echo "<script type='text/javascript'>parent.$.fn.colorbox.close();</script>";
					}
				}
     
				$filetoset='';
				if(isset($_GET['filetoset'])){
					$filetoset=$_GET['filetoset'];
				}else if(isset($_POST['filetoset'])){
					$filetoset=$_POST['filetoset'];
				}
				$filetoset=html_entity_decode($filetoset, ENT_QUOTES);

				//Security check. make sure this is a supported file type.
				if(!is_image($filetoset) && !is_movie($filetoset)){
					log_action("SET_THUMBNAIL", "FAILED", $galleryname, "ATTEMPT TO SET GALLERY THUMBNAIL IMAGE TO UNSUPPORTED FILE - {$filetoset}");
					die("NO HACK PROTECTION ACTIVATED");
					exit(0);            
				}

				$filename=$galleriesRoot.$galleryname."/thm.php";

				//we made it here so its time to modify - dont forget the lock!
				$pf=fopen($filename, 'w+');
				$filecontents = "thm_".trim($filetoset);

				flock($pf, LOCK_EX);
				fwrite($pf, "<?php\n");
				fwrite($pf, "\$galleryThumbnail=\"{$filecontents}\";\n");
				fwrite($pf, "?>\n");
				//relinquish the lock
				flock($pf, LOCK_UN);
				fclose($pf);

				log_action("SET_THUMBNAIL", "SUCCESS", $galleryname, "SET GALLERY THUMBNAIL IMAGE - {$filetoset}");

				if($vm!='ajax'){
					header("Location: http://$kickmeto");
				}else{
					echo "<script type='text/javascript'>parent.$.fn.colorbox.close(); </script>";
				}

				break;

		case 'confirmgallerydetails':
			
				$parsedusername=strtolower(str_replace(" ", "", $username));
				
				
				$galleryname=$_POST['galleryname'];
				
				//If we're a new gallery create a beautifully simple gallery name.
				if($galleryname=="NEW"){
					
					$gallery_index=0;
					
					while(file_exists($galleriesRoot.$parsedusername."_".$gallery_index)){
				    	$gallery_index++;
					}
						
					$galleryname=trim($parsedusername."_".$gallery_index);
				}else{
					if(!can_edit($galleryname, $_SESSION['s_userName'])){
						header("Location: http://$kickmeto");
						break;
					}
				}
				
				$gallerytitle=htmlentities(trim(stripslashes($_POST['gallerytitle'])), ENT_QUOTES);
				$gallerydesc=htmlentities(trim(stripslashes($_POST['gallerydesc'])), ENT_QUOTES);
				$date=htmlentities($_POST['gallerydate'], ENT_QUOTES);
				$downloadLink=htmlentities(trim($_POST['downloadLink']), ENT_QUOTES);
				$gallerycopyright=htmlentities(trim(stripslashes($_POST['gallerycopyright'])), ENT_QUOTES);
				$concealPath=htmlentities(trim($_POST['concealPath']), ENT_QUOTES);
				$sortOrder=$_POST['galleryViewSortOrder'];

				//lets avoid trouble and stop these errors in their tracks
				if((!$galleryname)||(!$gallerytitle)||(!$gallerydesc)){
                	header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php?action=editgallerydetails&galleryname={$galleryname}&error=GCblank&vm={$vm}&kickmeto=".urlencode($kickmeto));
                	break;
				}
                        
				$absolute_path = $galleriesRoot.$galleryname;

				if(!file_exists($absolute_path)){
					$posterID=$username;
					$date=currentDate()."@".time();
					$absolute_path = $galleriesRoot.$galleryname;
					$temp=mkdir($absolute_path, $core_settings['directory_permissions']);
					$log_action="GALLERY_CREATED";
				}else{
					$log_action="GALLERY_DETAILS_EDITED";
				}

				if (file_exists($absolute_path."/index.php")) {
					$gallery_options="";
					include $galleriesRoot.$galleryname."/index.php";
					$posterID=trim($gallery_options['poster']);
	
					if($posterID==""){
						$posterID=$username;
					}
				}
				
				if($date=="" || $date=="Unavailable"){
					$date=currentDate()."@".time();
				}
				
                update_gallery_option($galleryname, "poster", $posterID);
                update_gallery_option($galleryname, "date_posted", $date);
                update_gallery_option($galleryname, "title", $gallerytitle);
                update_gallery_option($galleryname, "description", $gallerydesc);
                update_gallery_option($galleryname, "sort_order", $sortOrder);
                update_gallery_option($galleryname, "download_policy", $downloadLink);
                update_gallery_option($galleryname, "copyright", $gallerycopyright);
                update_gallery_option($galleryname, "conceal_paths", $concealPath);
                
				log_action("$log_action", "SUCCESS", $galleryname, "\"".implode("  --  ", $galleryInfo)."\"");

				if($vm!='ajax'){
					header("Location: http://".$kickmeto);
					break;
				}else{
					echo "<script type='text/javascript'>parent.$.fn.colorbox.close();</script>";
				}
				
				break;

		case 'adduser':

				if(is_wordpress()){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php?cms=$cms");
					break;
				}
				
				if( !can_admin() ){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php?cms=$cms");
					break;
				}
				
				require("../conf/users/users.inc.php");
					        
				/* RETRIEVE VARS */
				$requestedUsername=$_POST['requestedUsername'];
				$stripUsername=preg_replace('/[^a-zA-Z0-9]/', '', $requestedUsername );
				$stripUsername=strtolower(str_replace(" ", "", trim($stripUsername)));
				$userEmailAddress=strtolower(trim($_POST['usersEmailAddress']));
				$usersFirstName=trim($_POST['usersFirstName']);
				$usersLastName=trim($_POST['usersLastName']);
				$userLevel="standard";
			
				$requestedPassword = random_password();
			
				//perform our error checking and provide the user with feedback
				$email_exists=false;
				foreach($users_array as $tmp_user){
				    if($tmp_user['email_address'] == $userEmailAddress){
						$email_exists=true;
					}
				}
				
				if($userEmailAddress=='' || $usersFirstName=='' || $usersLastName==''){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php?action=editusers&error=fieldBlank&requestedUsername=".urlencode($requestedUsername)."&usersFirstName=".urlencode($usersFirstName)."&usersLastName=".urlencode($usersLastName)."&userEmailAddress=".urlencode($userEmailAddress)."&cms=$cms");
					break;
				}else if($email_exists){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php?action=editusers&error=emailTaken&requestedUsername=".urlencode($requestedUsername)."&usersFirstName=".urlencode($usersFirstName)."&usersLastName=".urlencode($usersLastName)."&userEmailAddress=".urlencode($userEmailAddress)."&cms=$cms");
					break;
				}else if( isset($users_array[$requestedUsername]) || $users_array[$requestedUsername]['username']==$requestedUsername){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php?action=editusers&error=userTaken&requestedUsername=".urlencode($requestedUsername)."&usersFirstName=".urlencode($usersFirstName)."&usersLastName=".urlencode($usersLastName)."&userEmailAddress=".urlencode($userEmailAddress)."&cms=$cms");
					break;
				}else if($requestedUsername!=$stripUsername){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php?action=editusers&error=userChar&requestedUsername=".urlencode($requestedUsername)."&usersFirstName=".urlencode($usersFirstName)."&usersLastName=".urlencode($usersLastName)."&userEmailAddress=".urlencode($userEmailAddress)."&cms=$cms");
					break;
				}else{
				
					$userEmailAddress=htmlentities($userEmailAddress, ENT_QUOTES);
					$usersFirstName=htmlentities(trim($_POST['usersFirstName']), ENT_QUOTES);
				    $usersLastName=htmlentities(trim($_POST['usersLastName']), ENT_QUOTES);
				
					//add the new user to our users array
					$users_array[$requestedUsername]=array( 'username' => $requestedUsername,
															'password' => crypt($requestedPassword),
															'email_address' => $userEmailAddress,
															'firstname' => $usersFirstName,
															'lastname' => $usersLastName,
															'userlevel' => $userLevel
														   );
			
					write_user_data($users_array);
					
					$requestedPassword=urlencode("$requestedPassword");
					
					//send email with account info
					//send_email( strtolower(trim($userEmailAddress)), $core_settings['gallery_title']." - New User Registration", $usersFirstName.",\n\nYour account information for ".$core_settings['gallery_title']." is as follows.\n\nUsername: ".$requestedUsername."\nPassword: ".$requestedPassword."\n\nYou can login at: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php");
					
					$_SESSION['new_user'] = array( 'username' => $requestedUsername, 'password' => $requestedPassword);
					
					/* REDIRECT */
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php?action=editusers&error=success&cms=$cms");
				}
				
				break;
			
	    case 'chgpass':
	
				if(is_wordpress()){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/admin/index.php?cms=$cms");
					break;
				}
				
		        if(!isset($_SESSION['s_userName'])){
		            header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/index.php?cms=$cms");
		            break;
		        }
		
				require("../conf/users/users.inc.php");
				
				$currentPassword=trim($_POST['currentPassword']);
		        $requestedPassword=trim($_POST['requestedPassword']);
		        $requestedPassword2=trim($_POST['requestedPassword2']);
				$stripPass=trim(str_replace(" ", "", trim($requestedPassword)));
				
		        //perform our error checking and provide the user with feedback
		        if($currentPassword=='' || $requestedPassword=='' || $requestedPassword2==''){
		        	header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/admin/index.php?action=chgpass&error=fieldBlank&cms=$cms");
		        }else if( $users_array[$_SESSION['s_userName']]['password'] != crypt($currentPassword, $users_array[$_SESSION['s_userName']]['password']) ){
		        	header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/admin/index.php?action=chgpass&error=wrongpass&cms=$cms");
		        }else if($requestedPassword!=$requestedPassword2){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/admin/index.php?action=chgpass&error=passMissMatch&cms=$cms");
				}else if($requestedPassword!=$stripPass){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/admin/index.php?action=chgpass&error=passChar&cms=$cms");
				}else if(strlen($requestedPassword) < 8){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/admin/index.php?action=chgpass&error=passShort&cms=$cms");
				}else{
				
					$users_array[$_SESSION['s_userName']]['password']=crypt($requestedPassword);
		
					write_user_data($users_array);
			
					/* REDIRECT */
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/admin/index.php?action=chgpass&error=success&cms=$cms");
				}
				break;
		
		case 'edituser':
			
			if(is_wordpress()){
				header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php?cms=$cms");
				break;
			}
			
			if( !can_admin() ){
				header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php");
				break;
			}

			require("../conf/users/users.inc.php");
			
			$usr=$_POST['usr'];
			$firstname=$_POST['firstname'];
			$lastname=$_POST['lastname'];
			$email_address=$_POST['email_address'];
			$userlevel=$_POST['userlevel'];
			
			if( trim($usr) != "" && trim($userlevel) != "" ){
				
				$users_array[$usr]["firstname"] = $firstname;
				$users_array[$usr]["lastname"] = $lastname;
				$users_array[$usr]["email_address"] = $email_address;
				$users_array[$usr]["userlevel"] = $userlevel;
				
				write_user_data($users_array);
			}
			
			header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php?action=editusers");
			
			break;
								
		case 'deluser':
			
			if(is_wordpress()){
				header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php?cms=$cms");
				break;
			}
			
			if( !can_admin() ){
				header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php");
				break;
			}

			require("../conf/users/users.inc.php");
			
			$usr=$_GET['usr'];
			
			if( trim($usr) != "" ){
				
				unset($users_array[$usr]);
				
				write_user_data($users_array);
			}
			
			header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php?action=editusers");
			
			break;
		
		case 'addgallerycollaborator':
				
				$error='';
				
				$galleryname='';
				if(isset($_POST['galleryname'])){
					$galleryname=$_POST['galleryname'];
				}
				
				$collaborator='';
				if(isset($_POST['collaborator'])){
					$collaborator=trim($_POST['collaborator']);
				}
				
				if(!can_edit($galleryname, $_SESSION['s_userName'])){
					header("Location: http://$kickmeto");
					break;
				}

				get_gallery_details($galleryname);
				
				require("../conf/users/users.inc.php");

				foreach ($users_array as $user) {
					if($user['username'] == $collaborator){
						$add_flag = TRUE;
						$add_user = $user['username'];
					}else if($user['email_address'] == $collaborator){
						$add_flag = TRUE;
						$add_user = $user['username'];
					}
				}
				
				if( $add_flag == true ){
					foreach ($loopvars->get_var("gallery_collaborators") as $existing_collaborator) {
						if( $existing_collaborator == $add_user ){
							$add_flag = FALSE;
							$add_user = "";
							$error='alreadyContributor';
						}
					}
				}
				
				if( $add_flag == true ){
				
					if( $loopvars->get_var("gallery_poster") != $add_user ){
						$ary_collaborators = $loopvars->get_var("gallery_collaborators");
						
						$ary_collaborators[] = $add_user;
						
						update_gallery_option($galleryname, "collaborators", $ary_collaborators);
						
						$error='success';
					}else{
						$error='cantAddSelf';
					}
				}else{
					if( $error != 'alreadyContributor' ){
						$error='notFound';
					}
				}
				
				if($vm!='ajax'){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php?action=editgallerycollaborators&error=".$error."&galleryname=".$galleryname);
					break;
				}else{
					echo "<script type='text/javascript'>//setTimeout('parent.$.fn.colorbox.close();', 1250);  parent.$.fn.colorbox.close();</script>";
				}

				break;

	case 'delgallerycollaborator':

				$galleryname='';
				if(isset($_POST['galleryname'])){
					$galleryname=$_POST['galleryname'];
				}else if(isset($_GET['galleryname'])){
					$galleryname=$_GET['galleryname'];
				}
				
				$collaborator='';
				if(isset($_POST['collaborator'])){
					$collaborator=trim($_POST['collaborator']);
				}else if(isset($_GET['collaborator'])){
					$collaborator=trim($_GET['collaborator']);
				}
				
				if(!can_edit($galleryname, $_SESSION['s_userName'])){
					header("Location: http://$kickmeto");
					break;
				}

				get_gallery_details($galleryname);
				$ary_collaborators = $loopvars->get_var("gallery_collaborators");
				
				require("../conf/users/users.inc.php");

				foreach ($ary_collaborators as $key => $user) {
					if($user == $collaborator){
						unset($ary_collaborators[$key]);
					}
				}
		
				update_gallery_option($galleryname, "collaborators", $ary_collaborators);
				
				if($vm!='ajax'){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."admin/index.php?action=editgallerycollaborators&error=contributorDeleted&galleryname=".$galleryname);
					break;
				}else{
					echo "<script type='text/javascript'>//setTimeout('parent.$.fn.colorbox.close();', 1250);  parent.$.fn.colorbox.close();</script>";
				}

				break;
								
		default:
				header("Location: http://".$kickmeto);
				break;
	}
	
}else{

	switch($action){
	
		case 'reguser':
				/* DONT LET THIS SCRIPT RUN IF THE FUNCTIONALITY IS DISABLED */
				if(!$core_settings['allow_user_registration']){
					die ("This functionality is disabled");
				}
				
				if(is_wordpress()){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/index.php?cms=$cms");
					break;
				}
							
				if(isset($_SESSION['s_userName'])){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/index.php?cms=$cms");
					break;
				}
				
				require("../conf/users/users.inc.php");
					        
				/* RETRIEVE VARS */
				$requestedUsername=$_POST['requestedUsername'];
				$stripUsername=preg_replace('/[^a-zA-Z0-9]/', '', $requestedUsername );
				$stripUsername=strtolower(str_replace(" ", "", trim($stripUsername)));
				$userEmailAddress=strtolower(trim($_POST['usersEmailAddress']));
				$usersFirstName=trim($_POST['usersFirstName']);
				$usersLastName=trim($_POST['usersLastName']);
				$userLevel="standard";
			
				$requestedPassword = random_password();
				
				if(!ISSET($_SESSION['captcha'])){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/index.php?a=reg&error=fieldBlank&requestedUsername=".urlencode($requestedUsername)."&usersFirstName=".urlencode($usersFirstName)."&usersLastName=".urlencode($usersLastName)."&userEmailAddress=".urlencode($userEmailAddress)."&cms=$cms");
					break;
				}else{
					$challenge=strtolower($_SESSION['captcha']);
				}
				$response=strtolower(trim($_POST['response']));
			
				//perform our error checking and provide the user with feedback
				$email_exists=false;
				foreach($users_array as $tmp_user){
				    if($tmp_user['email_address'] == $userEmailAddress){
						$email_exists=true;
					}
				}
				
				if($userEmailAddress=='' || $usersFirstName=='' || $usersLastName==''){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/index.php?a=reg&error=fieldBlank&requestedUsername=".urlencode($requestedUsername)."&usersFirstName=".urlencode($usersFirstName)."&usersLastName=".urlencode($usersLastName)."&userEmailAddress=".urlencode($userEmailAddress)."&cms=$cms");
					break;
				}else if($email_exists){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/index.php?a=reg&error=emailTaken&requestedUsername=".urlencode($requestedUsername)."&usersFirstName=".urlencode($usersFirstName)."&usersLastName=".urlencode($usersLastName)."&userEmailAddress=".urlencode($userEmailAddress)."&cms=$cms");
					break;
				}else if( isset($users_array[$requestedUsername]) || $users_array[$requestedUsername]['username']==$requestedUsername){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/index.php?a=reg&error=userTaken&requestedUsername=".urlencode($requestedUsername)."&usersFirstName=".urlencode($usersFirstName)."&usersLastName=".urlencode($usersLastName)."&userEmailAddress=".urlencode($userEmailAddress)."&cms=$cms");
					break;
				}else if($requestedUsername!=$stripUsername){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/index.php?a=reg&error=userChar&requestedUsername=".urlencode($requestedUsername)."&usersFirstName=".urlencode($usersFirstName)."&usersLastName=".urlencode($usersLastName)."&userEmailAddress=".urlencode($userEmailAddress)."&cms=$cms");
					break;
				}else if($challenge!=$response){
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/index.php?a=reg&error=codeMismatch&requestedUsername=".urlencode($requestedUsername)."&usersFirstName=".urlencode($usersFirstName)."&usersLastName=".urlencode($usersLastName)."&userEmailAddress=".urlencode($userEmailAddress)."&cms=$cms");
					break;
				}else{
				
					$userEmailAddress=htmlentities($userEmailAddress, ENT_QUOTES);
					$usersFirstName=htmlentities(trim($_POST['usersFirstName']), ENT_QUOTES);
				    $usersLastName=htmlentities(trim($_POST['usersLastName']), ENT_QUOTES);
				
					//add the new user to our users array
					$users_array[$requestedUsername]=array( 'username' => $requestedUsername,
															'password' => crypt($requestedPassword),
															'email_address' => $userEmailAddress,
															'firstname' => $usersFirstName,
															'lastname' => $usersLastName,
															'userlevel' => $userLevel
														   );
			
					write_user_data($users_array);
					
					$requestedPassword=urlencode("$requestedPassword");
					
					//send email with account info
					send_email( strtolower(trim($userEmailAddress)), $core_settings['gallery_title']." - New User Registration", $usersFirstName.",\n\nYour account information for ".$core_settings['gallery_title']." is as follows.\n\nUsername: ".$requestedUsername."\nPassword: ".$requestedPassword."\n\nYou can login at: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/admin/index.php");
					
					/* REDIRECT */
					header("Location: http://".$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF']))."/index.php?a=reg&error=success&cms=$cms");
				}
				
				break;

		default:
		
			header("Location: http://".$kickmeto);
			
			break;		
	}
}

?>
