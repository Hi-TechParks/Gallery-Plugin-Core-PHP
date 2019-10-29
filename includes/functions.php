<?php
//#######################################################################\\
//# Author: Christopher Schiffner                                       #\\
//# Filename: functions.php                                             #\\
//# Copyright: Christopher Schiffner, All Rights Reserved               #\\
//# Description: Image gallery software, view readme for more info.     #\\
//#                                                                     #\\
//# License: This software is free to use for personal applications.    #\\
//#          There is a small registration fee for commercial           #\\
//#          applications.  Please contact chris@schiffner.com if       #\\
//#          you wish to use this program on a commercial website.      #\\
//#######################################################################\\

function theme_setting($key, $content){

	global $theme_settings;
	
	$theme_settings->set_var($key, $content);
}

/* queues style sheets for inclusion in the page head */
function register_style($src, $ver, $media_type){

	global $loopvars;
	
	$styles=$loopvars->get_var('styles');
	
	$styles[]=array('src' => $src,
					'ver' => $ver,
					'media' => $media_type,
					);

	$loopvars->set_var('styles', $styles);
}

/* queues scripts for inclusion in the page head */
function register_script($src, $type, $charset){

	global $loopvars;
	
	$scripts=$loopvars->get_var('scripts');

	$scripts[]=array('src' => $src,
					 'type' => $type,
					 'charset' => $charset,
					 );
						
	$loopvars->set_var('scripts', $scripts);
}

/* sets core loop variables for the gallery information */
function get_gallery_details($gallery){
	global $loopvars;
	global $galleriesRoot;
	global $includeFlag;

	if (file_exists($galleriesRoot.$gallery."/index.php")) {
		$gallery_options="";
        
		ob_start();
		include $galleriesRoot.$gallery."/index.php";
		ob_end_clean();

		if(is_array($gallery_options)){

		     //format date/time as required
		     if(stristr($gallery_options["date_posted"], "@")){
				$gallery_options["date_posted"]=explode("@", $gallery_options["date_posted"]);

				if($showPostTime==1){
					$gallery_options["date_posted"][1]=date("g:ia", $gallery_options["date_posted"][1]);
					$gallery_options["date_posted"]=$gallery_options["date_posted"][0]." at ".$gallery_options["date_posted"][1];
				}else{
					$gallery_options["date_posted"]=$gallery_options["date_posted"][0];
				}
			}

		    $loopvars->set_var("gallery_poster", $gallery_options["poster"]);
		    $loopvars->set_var("gallery_date_posted", $gallery_options["date_posted"]);
		    $loopvars->set_var("gallery_title", $gallery_options["title"]);
		    $loopvars->set_var("gallery_description", $gallery_options["description"]);
		    $loopvars->set_var("gallery_sort_order", $gallery_options["sort_order"]);
		    $loopvars->set_var("gallery_download_policy", $gallery_options["download_policy"]);
		    $loopvars->set_var("gallery_copyright", $gallery_options["copyright"]);
			$loopvars->set_var("conceal_paths", $gallery_options["conceal_paths"]);
			$loopvars->set_var("gallery_collaborators", $gallery_options["collaborators"]);
		} else {
			$loopvars->set_var("gallery_poster", "Unavailable");
		    $loopvars->set_var("gallery_date_posted", "Unavailable");
		    $loopvars->set_var("gallery_title", "Title Unknown");
		    $loopvars->set_var("gallery_description", "No description given");
		    $loopvars->set_var("gallery_sort_order", $core_settings['default_image_sort']);
		    $loopvars->set_var("gallery_download_policy", '0');
		    $loopvars->set_var("gallery_copyright", "");
		    $loopvars->set_var("conceal_paths", 0);
		    $loopvars->set_var("gallery_collaborators", array());
		}
	} else {
		$loopvars->set_var("gallery_poster", "Unavailable");
		$loopvars->set_var("gallery_date_posted", "Unavailable");
		$loopvars->set_var("gallery_title", "Title Unknown");
		$loopvars->set_var("gallery_description", "No description given");
		$loopvars->set_var("gallery_download_policy", '0');
		$loopvars->set_var("gallery_sort_order", $core_settings['default_image_sort']);
		$loopvars->set_var("gallery_copyright", "");
		$loopvars->set_var("conceal_paths", 0);
		$loopvars->set_var("gallery_collaborators", array());
	}

	return 0;
}

/* returns head code to the theme */
function get_core_head(){

	global $styleSheetPath;
	global $scriptsPath;
	global $title;
	global $a;
	global $gallery;
	global $loopvars;
	global $theme_settings;
	global $core_settings;

	$temp_content="<title>".$core_settings['gallery_title']." - powered by: Panda Image Gallery</title>
			  <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
			  <meta name='viewport' content='width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;' />
              <meta name='description' content='".$core_settings['gallery_description']." - powered by: Panda Image Gallery' />
              <link rel='alternate' type='application/rss+xml' title='".$core_settings['gallery_title']." - Main' href='includes/rss.xml.php' />
              
              <!-- THEME/PLUGIN REGISTERED STYLES -->
              ";
	
	if($loopvars->get_var('styles')!=""){
	    foreach($loopvars->get_var('styles') as $style){
	    	$temp_content.="<link rel='stylesheet' TYPE='text/css' href='".$style['src']."' media='".$style['media']."' />\n";
	    }
    }
    
    if($loopvars->get_var('is_admin_page')){
    	if(is_wordpress()){
    		$temp_content.="<link rel='stylesheet' TYPE='text/css' href='admin_styles.php?wp=true' media='' />\n";
    	}else{
    		$temp_content.="<link rel='stylesheet' TYPE='text/css' href='admin_styles.php' media='' />\n";
    	}
    }
              
	$temp_content.="\n	      <!-- JQUERY UI CSS-->
              <link type='text/css' rel='stylesheet' href='{$scriptsPath}jquery/jquery-ui-1.11.2.min.css' media='screen' />
              
              <!-- PLUPLOAD STYLES -->
              <link type='text/css' rel='stylesheet' href='{$scriptsPath}plupload/jquery.ui.plupload.css' media='screen' />
              
              <!-- JQUERY -->
              <script  src='{$scriptsPath}jquery/jquery-1.11.1.min.js'></script>
              <script  src='{$scriptsPath}jquery/jquery-ui-1.11.2.min.js'></script>

              <!-- PLUPLOAD -->
              <script type='text/javascript' src='{$scriptsPath}plupload/plupload.full.min.js' charset='UTF-8'></script>
              <script type='text/javascript' src='{$scriptsPath}plupload/jquery.ui.plupload.min.js' charset='UTF-8'></script>
              
              <!-- colorbox integration -->
              <script type='text/javascript' src='{$scriptsPath}colorbox/jquery.colorbox-min.js'></script>
              
              <!-- THEME/PLUGIN REGISTERED SCRIPTS -->
              ";
	
	if($loopvars->get_var('scripts')!=""){
		foreach($loopvars->get_var('scripts') as $style){
			$temp_content.="<script type='".$style['type']."' src='".$style['src']."' charset='".$style['charset']."'></script>\n";
		}
	}
	
    $temp_content.="\n";          

	if($a=='vg' || $a=='vi'){
		$temp_content.="<link rel='alternate' type='application/rss+xml' title='$title Image Feed' href='includes/rssgallery.xml.php?g={$gallery}' />";
	}

	$loopvars->set_var("get_head", true);
	
	return $temp_content;
}


/* returns foooter code to the theme */
function get_core_footer(){

	global $core_settings;
	global $sitemapURL;
	global $target;
	global $loopvars;
	global $scriptsPath;
	global $vm;
	global $cms;
	global $benchmark_timer;

	if( $core_settings['license']=="free" || ( $core_settings['display_sitemap_link']==1 && $vm!="ajax") ){
		$temp_content.="<div class='gallery_info'>";
	}
	if( $core_settings['display_sitemap_link']!=1 ){
		$center_copyright = "center_copyright";
	}else{
		$center_copyright = "";
	}
	if( $core_settings['license']=="free" ){
		$temp_content.="<div class='copyright ".$center_copyright."'><a href='http://www.schiffner.com/software/panda-image-gallery/'>Panda Image Gallery ".$core_settings['version']."</a> - &copy; ".date("Y")." Christopher Schiffner - All Rights Reserved</div>";
	}
	if($core_settings['display_sitemap_link']==1 && $vm!="ajax"){
		$temp_content.="<div class='sitemap'><a href='{$sitemapURL}' $target>Sitemap</a></div>";
	}
	if( $core_settings['license']=="free" || ( $core_settings['display_sitemap_link']==1 && $vm!="ajax") ){
		$temp_content.="</div>";
	}
	
	//$temp_content.="<img src='/includes/images/loading.gif' style='display: ;' />";
	
	$temp_content.="<!-- Search Watermark -->
					<script type='text/javascript' src='{$scriptsPath}jquery/jquery.watermark.min.js'></script>
				  
					<script language='JavaScript' type='text/javascript'>
						jQuery(function($){
							//Add in watermarks for various fields.
							$(\".search_input\").watermark(\" search \");
							$(\"#image_caption\").watermark(\" enter an image caption \");
							$(\".gallery_title_field\").watermark(\" enter a gallery title \");
							$(\".gallery_description_field\").watermark(\" enter a gallery description \");
							$(\".gallery_copyright_field\").watermark(\" enter gallery copyright data \");
						});
					</script>
				  
					<script type='text/javascript'>
						if ('undefined' == typeof(PandaImageGallery)) {
							var PandaImageGallery = {};
						};
						PandaImageGallery.pageRefresh=0;
					</script>";
					
	if($cms){
		$temp_content.="<script src='{$scriptsPath}jquery/iframeResizer.contentWindow.js'></script>";
	}
	
	//Benchmark code
	if($core_settings['benchmark']){
		$benchmark_total = $benchmark_timer->gettime();
		$temp_content.="<div class='execution_timer'>Total execution time: $benchmark_total</div>";
	}
	
	$loopvars->set_var("get_footer", true);

	return $temp_content;
}

function is_wordpress() {
	global $auto_conf;
		
	if(isset($auto_conf['wordpress_plugin'])){
		if($auto_conf['wordpress_plugin']==true){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}

//used to disable script/css head output from Wordpress includes.
function WP_KILL_ALL_SCRIPTS() {
    global $wp_scripts;
    $wp_scripts->queue = array();
}
function WP_KILL_ALL_STYLES() {
    global $wp_styles;
    $wp_styles->queue = array();
}

/* This is where we detect Wordpress logins */
function is_logged_in(){
	
	if(is_wordpress()){
		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$current_user=strtolower(trim($current_user->user_login));
			
			if(!isset($_SESSION['s_userName']) || $_SESSION['s_userName']!=$current_user){
				$_SESSION['s_userName']=$current_user;
			}
			
			return true;
			exit(); //test code;
		}else{
			unset($_SESSION['s_userName']);
		}
	}else{
		if(isset($_SESSION['s_userName'])){
			return true;
			exit(); //test code;
		}
	}
	
	return false;
}

//This function checks whether a user can manip a gallery.
//Admins can edit any gallery. Regular users can only edit their own gallery.
function can_create_gallery(){
	
	if(is_wordpress()){
		if(current_user_can( 'publish_posts' )){
			return true;
			exit(); //test code;
		}else{
			return false;
			exit(); //test code;
		}
	}else{
		if(isset($_SESSION['s_userName'])){
			return true;
			exit(); //test code;
		}
	}
	
	return false;
}

//This function checks whether a user can manip a gallery.
//Admins can edit any gallery. Regular users can only edit their own gallery.
function can_edit($gallery, $user){
	
	$includeFlag=1;
	if(file_exists("galleries/".$gallery."/index.php")){
		include "galleries/".$gallery."/index.php";
	}else if(file_exists("../galleries/".$gallery."/index.php")){
		include "../galleries/".$gallery."/index.php";
	}
	
	$return = FALSE;
	
	if(is_wordpress()){
		$parsedusername=str_replace(" ", "", $user);
		//if( (trim($user)!="" && substr($gallery, 0, strlen($parsedusername))==$parsedusername) || (current_user_can( 'manage_options' ))){
		if( (trim($user)!="" && $gallery_options['poster'] == $parsedusername ) || (current_user_can( 'manage_options' ))){
			$return = TRUE;
		}
	}else{
	
		if(file_exists("conf/auto_conf.php")){
			include "conf/users/users.inc.php";
		}else if(file_exists("../conf/auto_conf.php")){
			include "../conf/users/users.inc.php";
		}
		
		$parsedusername=str_replace(" ", "", $user);
		//if( (trim($user)!="" && substr($gallery, 0, strlen($parsedusername))==$parsedusername) || (trim($user)!="" && $users_array[$user]['userlevel']=="admin")){
		if( (trim($user)!="" && $gallery_options['poster'] == $parsedusername ) || (trim($user)!="" && $users_array[$user]['userlevel']=="admin")){
			$return = TRUE;
		}
	}
	
	return $return;
}

//This function checks whether a user can manip a gallery.
//Admins can edit any gallery. Regular users can only edit their own gallery.
function can_collaborate($gallery, $user){
	
	global $core_settings;
	$includeFlag = 1;
	
	if(file_exists("galleries/".$gallery."/index.php")){
		include "galleries/".$gallery."/index.php";
	}else if(file_exists("../galleries/".$gallery."/index.php")){
		include "../galleries/".$gallery."/index.php";
	}
	
	$return = FALSE;
	
	$parsedusername=str_replace(" ", "", $user);
	if( is_array($gallery_options['collaborators']) && $core_settings['license'] == "paid" ){
		foreach ($gallery_options['collaborators'] as $collaborator) {
			if( $collaborator == $parsedusername ){
				$return = TRUE;
			}
		}
	}
	
	return $return;
}

function can_manipulate_image($gallery, $image_name, $user){
	
	global $core_settings;
	$includeFlag = 1;
	
	$image_name = urldecode($image_name);
	
	if(file_exists("galleries/".$gallery."/".$image_name.".php")){
		include "galleries/".$gallery."/".$image_name.".php";
	}else if(file_exists("../galleries/".$gallery."/".$image_name.".php")){
		include "../galleries/".$gallery."/".$image_name.".php";
	}
	
	$return = FALSE;
	
	if( isset($image_options) ){
		$parsedusername=str_replace(" ", "", $user);
		if( can_collaborate($gallery, $user) && (trim($user)!="" && $image_options['poster'] == $parsedusername ) && $core_settings['license'] == "paid" ){
			$return = TRUE;
		}
	}
	
	return $return;
}

//This function checks whether a user can manip a gallery.
//Admins can edit any gallery. Regular users can only edit their own gallery.
function can_admin(){

	if(is_wordpress()){
		return false;
		exit(); //test code;
	}else{
			
		$user = $_SESSION['s_userName'];
		
		if(file_exists("conf/users/users.inc.php")){
			include "conf/users/users.inc.php";
		}else if(file_exists("../conf/users/users.inc.php")){
			include "../conf/users/users.inc.php";
		}
		
		$parsedusername=str_replace(" ", "", $user);
		
		if( trim($user)!="" && $users_array[$user]['userlevel']=="admin" ){
			return true;
			exit(); //test code;
		}else{
			return false;
			exit(); //test code;
		}
	}
	
	return false;
}

/* checks if the file type is a supported movie format */
function is_movie($filename){

	$ext=strtolower(substr($filename, strrpos($filename, ".") +1));
	if( $ext == 'mp4'
		|| $ext == 'ogg'                                                    
		|| $ext == 'webm'){

		return true;
		exit();
	}else{
		return false;
		exit();
	}

	return false;
	exit();
}

/* checks if the file type is a supported image fortmat */
function is_image($filename){
	
	$ext=strtolower(substr($filename, strrpos($filename, ".") +1));
	if( $ext == 'jpg'
		|| $ext == 'jpeg'
		|| $ext == 'gif'
		|| $ext == 'png'){

		return true;
		exit();
	}else{
		return false;
		exit();
	}
        
	return false;
	exit();
}

/* returns file extension */
function get_file_extension($filename){
	$ext=strtolower(substr($filename, strrpos($filename, ".") +1));
	return $ext;
}

/* writes image option to image option file */
function update_image_option($gallery_name, $file, $key, $data){

	global $galleriesRoot;
	
	if(!can_edit($gallery_name, $_SESSION['s_userName']) && !can_manipulate_image($gallery_name, $file, $_SESSION['s_userName']) ){
		header("Location: http://$kickmeto");
		exit(); //test code;
	}

	$filename=$galleriesRoot.$gallery_name."/".$file.".php";
	
	if(file_exists($filename)){
		include $filename;
	}else{
		$image_options=array(
			"poster" => $_SESSION['s_userName'],
			"caption" => ""
		);
	}
	
	$image_options[$key]=$data;
	         
	//we made it here so its time to modify - dont forget the lock!
	$pf=fopen($filename, 'w+');      
	flock($pf, LOCK_EX);   
	fwrite($pf, "<?php\n");
	fwrite($pf, "\$image_options=".var_export($image_options, TRUE).";\n");
	fwrite($pf, "?>\n"); 
	flock($pf, LOCK_UN);
	fclose($pf);

}

/* writes gallery option to gallery data file */
function update_gallery_option($gallery_name, $key, $data){

	global $includeFlag;
	global $galleriesRoot;
	
	$filename=$galleriesRoot.$gallery_name."/index.php";
	
	if(file_exists($filename)){
		
		if(!can_edit($gallery_name, $_SESSION['s_userName'])){
			header("Location: http://$kickmeto");
			exit(); //test code;
		}
		
		ob_start();
		include $filename;
		ob_end_clean();
	}else{
		$gallery_options=array(
			"poster" => $_SESSION['s_userName'],
			"date_posted" => currentDate()."@".time(),
			"title" => "",
			"description" => "",
			"sort_order" => "dateDESC",
			"download_policy" => 0,
			"copyright" => "",
			"conceal_paths" => 0,
			"collaborators" => array(),
		);
	}
	
	if(!isset($gallery_options) || !is_array($gallery_options)){
		$gallery_options=array(
			"poster" => $_SESSION['s_userName'],
			"date_posted" => currentDate()."@".time(),
			"title" => "",
			"description" => "",
			"sort_order" => "dateDESC",
			"download_policy" => 0,
			"copyright" => "",
			"conceal_paths" => 0,
			"collaborators" => array(),
		);
	}
	
	$gallery_options[$key]=$data;
	         
	//we made it here so its time to modify - dont forget the lock!
	$pf=fopen($filename, 'w+');      
	flock($pf, LOCK_EX);   
	fwrite($pf, "<?php\n");
	fwrite($pf, "if(ISSET(\$includeFlag)){");
	fwrite($pf, "\n     \$gallery_options=".var_export($gallery_options, TRUE).";\n");
	fwrite($pf, "}else{ \n     echo \"<html><head><title>Access Denied</title></head><body>Access Denied</body></html>\"; \n}");
	fwrite($pf, "?>\n"); 
	flock($pf, LOCK_UN);
	fclose($pf);

}

//this function writes the user array to the users database conf/users/users.inc.php
function write_user_data($users_array){
	if(file_exists("./conf/users/users.inc.php")){
		$filename="./conf/users/users.inc.php";
	}else if(file_exists("../conf/users/users.inc.php")){
		$filename="../conf/users/users.inc.php";
	}
	
	$pf=fopen($filename, 'w+');
	      
	flock($pf, LOCK_EX);   
	fwrite($pf, "<?php\n");
	fwrite($pf, "\$users_array=".var_export($users_array, TRUE).";\n");
	fwrite($pf, "?>\n"); 
	flock($pf, LOCK_UN);
	fclose($pf);
}

//sends email form the configured email address
function send_email($mail_to, $subject, $body) {
	global $core_settings;
	
	$eol="\r\n";
	$headers .= 'From: '.$core_settings['password_retrieval_email'].$eol;
	$headers .= 'Reply-To: '.$core_settings['password_retrieval_email'].$eol;
	$headers .= 'Return-Path: '.$core_settings['password_retrieval_email'].$eol;
	$headers .= "Message-ID: <".$now.$core_settings['password_retrieval_email'].">".$eol;
	$headers .= "X-Mailer: PHP/".phpversion().$eol;
	$headers .= 'MIME-Version: 1.0'.$eol;
	$headers .= "Content-type: text/plain; charset=UTF-8".$eol;
	
	mail($mail_to, $subject, $body, $headers);
}

//this function returns the current date in M/D/YYYY format
function currentDate(){ 
	$swap = getdate();
	$today = "{$swap['mon']}/{$swap['mday']}/{$swap['year']}";
	return $today;
}                       
                        
function natcasesort2d(&$aryInput) {
//this function will sort an array without maintaining keys
  $aryOut='';                   
                        
  $aryTemp = $aryInput;         
                                        
  if(is_array($aryTemp))                
     natcasesort($aryTemp);
                        
  if(!empty($aryTemp)){
    foreach ($aryTemp as $value) { //natcasesort will sort our array but it also preserves key associations
      $aryOut[] = $value;           //this way we step through a create a fresh array with no associations
    }                   
  }                     
  $aryInput = $aryOut;           //return array
}
function SortByDate(&$Files, $sortCriteria)
{        
    if($sortCriteria=="dateDESC" || $sortCriteria=="dateModifiedDESC")
        usort($Files, "numericSortDESC");
        
    if($sortCriteria=="dateASC" || $sortCriteria=="dateModifiedASC")
        usort($Files, "numericSortASC");
        
    if($sortCriteria=="titleAlphabetical" || $sortCriteria=="author")
        usort($Files, "alphabeticalSort");
}
function numericSortDESC($a, $b)
{ 
    return ($a[1] < $b[1]) ? 1 : 0;
}         

/* returns array position for image file name passed */        
function locateCurrentImagePosition($i, $images){
    $x=0;
    for ($x = 0; $x <= count($images); $x++) {
        if($images[$x][0]==$i)
            return $x;
    }   
        
    return -1;   
}

function format_bytes($size) {
    $units = array(' B', ' KB', ' MB', ' GB', ' TB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
    return round($size, 0).$units[$i];
}

//generate random password
function random_password() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

/* returns gallery thumbnail/master images */
function get_gallery_action_image($loopvars, $core_settings, $galleriesRoot, $galleryname){

			$clsDirListingSample = new clsDirListing($galleriesRoot.'/'.$galleryname, 0);
			$clsDirListingSample->sortOrder("titleAlphabetical");
			$clsDirListingSample->extensionIncludes(array("jpg", "gif", "png", "jpeg"));
			$clsDirListingSample->prefixExcludes(array("thm_", "lowres_"));
			$imagesingallery=$clsDirListingSample->getListing();
			$clsDirListingSample="";
			
			$thumbnailimg="";
			
			if (file_exists($galleriesRoot.$galleryname."/thm.php")) {

				include $galleriesRoot.$galleryname."/thm.php";

				if(file_exists($galleriesRoot.$galleryname."/".$galleryThumbnail)){
					$thumbnailimg=$galleryThumbnail;
				}
				if($thumbnailimg!=""){
					$thumbnailimg=str_replace("thm_", "", $thumbnailimg);

					if(file_exists($galleriesRoot."/".$galleryname."/lowres_".$thumbnailimg)){
					
						if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
							$temp_content="<img class='action_image' src='../view.php/".$galleryname."/lowres_".$thumbnailimg."' alt='' border='0' />";
						}else{
							$temp_content="<img class='action_image' src='".$galleriesRoot.$galleryname."/lowres_".$thumbnailimg."' alt='' border='0' />";
						}
						
					}else if(file_exists($galleriesRoot."/".$galleryname."/".$thumbnailimg)){
					
						if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
							$temp_content="<img class='action_image' src='../view.php/".$galleryname."/".$thumbnailimg."' alt='' border='0' />>";
						}else{
							$temp_content="<img class='action_image' src='".$galleriesRoot.$galleryname."/".$thumbnailimg."' alt='' border='0' />";
						}
						
					}else{
					
						if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
							$temp_content="<img class='action_image' src='../view.php/".$galleryname."/thm_".$thumbnailimg."' alt='' border='0' />";
						}else{
							$temp_content="<img class='action_image' src='".$galleriesRoot.$galleryname."/thm_".$thumbnailimg."' alt='' border='0' />";

						}
						
					}
				}else{
					if(file_exists($galleriesRoot."/".$galleryname."/".$imagesingallery[0][0]) && $imagesingallery[0][0]!=''){
						if(file_exists($galleriesRoot."/".$galleryname."/lowres_".$imagesingallery[0][0])){
							if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
								$temp_content="<img class='action_image' src='../view.php/".$galleryname."/lowres_".$imagesingallery[0][0]."' alt='' border='0' />";
							}else{
								$temp_content="<img class='action_image' src='".$galleriesRoot.$galleryname."/lowres_".$imagesingallery[0][0]."' alt='' border='0' />";
							}
						}else{
							if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
		                            			$temp_content="<img class='action_image' src='../view.php/".$galleryname."/".$imagesingallery[0][0]."' alt='' border='0' /><br />";
							}else{
								$temp_content="<img class='action_image' src='".$galleriesRoot.$galleryname."/".$imagesingallery[0][0]."' alt='' border='0' />";
							}
						}
					}else{
						$temp_content="<img class='action_image' src='../themes/".$loopvars->get_var("theme_path")."/images/na.png' alt='' border=0 />";
					}
				}
			}else{
				if(file_exists($galleriesRoot."/".$galleryname."/".$imagesingallery[0][0]) && $imagesingallery[0][0]!=''){
					if(file_exists($galleriesRoot."/".$galleryname."/lowres_".$imagesingallery[0][0])){
						if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
							$temp_content="<img class='action_image' src='../view.php/".$galleryname."/lowres_".$imagesingallery[0][0]."' alt='' border='0' />";
						}else{
							$temp_content="<img class='action_image' src='".$galleriesRoot.$galleryname."/lowres_".$imagesingallery[0][0]."' alt='' border='0' />";
						}
					}else{
						if($core_settings['protect_image_source'] || $loopvars->get_var("conceal_paths")){
							$temp_content="<img class='action_image' src='../view.php/".$galleryname."/".$imagesingallery[0][0]."' alt='' border='0' />";
						}else{
							$temp_content="<img class='action_image' src='".$galleriesRoot.$galleryname."/".$imagesingallery[0][0]."' alt='' border='0' />";
						}
					}
				}else{
					$temp_content="<img class='action_image' src='../themes/".$loopvars->get_var("theme_path")."/images/na.png' alt='' border=0 />";
				}
			}
			
			return $temp_content;
}

/* create thumbnail image based on theme settings */
function create_thumbnail_image($gallery_name, $filename) {
	
	global $theme_settings;
	global $core_settings;
	global $galleriesRoot;
	
	$sourcepath=$galleriesRoot.$gallery_name."/".$filename;
	$createpath=$galleriesRoot.$gallery_name."/thm_".$filename;
	$ext=get_file_extension($filename);
	
	$imagedimensions = getimagesize($sourcepath);

	/* determine which dimension to creat thumbnail by */
	if($imagedimensions[0] > $imagedimensions[1]){
		$modifier=($theme_settings->get_var("thumbnail_width")/$imagedimensions[0]);
		$resized_height=intval($modifier*$imagedimensions[1]);
			
		if($resized_height <= $theme_settings->get_var("thumbnail_height")){
			$create_by="width";
		}else if($resized_height > $theme_settings->get_var("thumbnail_height")){
			$create_by="height";
		}
	}else if($imagedimensions[1] > $imagedimensions[0]){
		$modifier=($theme_settings->get_var("thumbnail_height")/$imagedimensions[1]);
		$resized_width=intval($modifier*$imagedimensions[0]);
		
		if($resized_width <= $theme_settings->get_var("thumbnail_width")){
			$create_by="height";
		}else if($resized_width > $theme_settings->get_var("thumbnail_width")){
			$create_by="width";
		}
	}
		
	switch($create_by){
		case 'width':
			switch ($ext){
				case 'jpg':
					resizeJPG($sourcepath, $createpath, $theme_settings->get_var("thumbnail_width"), '0');
					break;
					
				case 'jpeg':
					resizeJPG($sourcepath, $createpath, $theme_settings->get_var("thumbnail_width"), '0');
					break;
						
				case 'png':
					resizePNG($sourcepath, $createpath, $theme_settings->get_var("thumbnail_width"), '0');
					break;
						
				case 'gif':
					resizeGIF($sourcepath, $createpath, $theme_settings->get_var("thumbnail_width"), '0');
					break;
			}
	
		case 'height':
			switch ($ext){
				case 'jpg':
					resizeJPG($sourcepath, $createpath, '0', $theme_settings->get_var("thumbnail_height"));
					break;
					
				case 'jpeg':
					resizeJPG($sourcepath, $createpath, '0', $theme_settings->get_var("thumbnail_height"));
					break;
						
				case 'png':
					resizePNG($sourcepath, $createpath, '0', $theme_settings->get_var("thumbnail_height"));
					break;
						
				case 'gif':
					resizeGIF($sourcepath, $createpath, '0', $theme_settings->get_var("thumbnail_height"));
					break;
			}
	}
			
	@chmod($createpath, $core_settings['file_permissions']);
	
}

/* create lowres image based on theme settings */
function create_lowres_image($gallery_name, $filename) {
	
	global $theme_settings;
	global $core_settings;
	global $galleriesRoot;
	
	$sourcepath=$galleriesRoot.$gallery_name."/".$filename;
	$createpath=$galleriesRoot.$gallery_name."/lowres_".$filename;
	$ext=get_file_extension($filename);
	
	$imagedimensions = getimagesize($sourcepath);

	/* determine which dimension to creat thumbnail by */
	if($imagedimensions[0] > $imagedimensions[1]){
		$modifier=($theme_settings->get_var("image_display_width")/$imagedimensions[0]);
		$resized_height=intval($modifier*$imagedimensions[1]);
			
		if($resized_height <= $theme_settings->get_var("image_display_height")){
			$create_by="width";
		}else if($resized_height > $theme_settings->get_var("image_display_height")){
			$create_by="height";
		}
	}else if($imagedimensions[1] > $imagedimensions[0]){
		$modifier=($theme_settings->get_var("image_display_height")/$imagedimensions[1]);
		$resized_width=intval($modifier*$imagedimensions[0]);
		
		if($resized_width <= $theme_settings->get_var("image_display_width")){
			$create_by="height";
		}else if($resized_width > $theme_settings->get_var("image_display_width")){
			$create_by="width";
		}
	}

	switch($create_by){
		case 'width':
			switch ($ext){
				case 'jpg':
					resizeJPG($sourcepath, $createpath, $theme_settings->get_var("image_display_width"), '0');
					break;
					
				case 'jpeg':
					resizeJPG($sourcepath, $createpath, $theme_settings->get_var("image_display_width"), '0');
					break;
						
				case 'png':
					resizePNG($sourcepath, $createpath, $theme_settings->get_var("image_display_width"), '0');
					break;
						
				case 'gif':
					resizeGIF($sourcepath, $createpath, $theme_settings->get_var("image_display_width"), '0');
					break;
			}
	
		case 'height':
			switch ($ext){
				case 'jpg':
					resizeJPG($sourcepath, $createpath, '0', $theme_settings->get_var("image_display_height"));
					break;
					
				case 'jpeg':
					resizeJPG($sourcepath, $createpath, '0', $theme_settings->get_var("image_display_height"));
					break;
						
				case 'png':
					resizePNG($sourcepath, $createpath, '0', $theme_settings->get_var("image_display_height"));
					break;
						
				case 'gif':
					resizeGIF($sourcepath, $createpath, '0', $theme_settings->get_var("image_display_height"));
					break;
			}
	}
			
	@chmod($createpath, $core_settings['file_permissions']);
	
}	

function resizeJPG($sourceimage, $destinationimage, $newwidth, $newheight){

	//Variables used in this script
	//IT IS ONLY NECESSARY TO PASS ONE OF THE NEW DIMENSIONS AS THE OTHER
	//DIMENSION IS CACULATED.
	//
	//Pass the unknown dimension as the number 0
	//
	//$newwidth   <--Width of resized image
	//$newheight  <--Height of resized image
	//$sourceimage <--Source to resize
	//$destinationimage <--Image name to output


	//get the current image dimensions
	$imagedimensions = getimagesize($sourceimage);

	//see if a dimension was passed, if so then calculate the other dim.
	//if both dims were passed then do nothing.
	if((!$newwidth && !$newheight) || ($newwidth==0 && $newheight==0)){
		die ('must specify at least one dimension');
	}

	if(!$newwidth || $newwidth==0){
		$temph=($newheight/$imagedimensions[1]);
		$newwidth=intval($temph*$imagedimensions[0]);
	}

	if(!$newheight || $newheight==0){
		$temph=($newwidth/$imagedimensions[0]);
		$newheight=intval($temph*$imagedimensions[1]);
	}

	//create pointers to work with
	$img_src=imagecreatefromjpeg($sourceimage);
	$img_dst=imagecreatetruecolor($newwidth,$newheight);

	//resize and output the image
	imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $newwidth, $newheight, $imagedimensions[0], $imagedimensions[1]);
	imagejpeg($img_dst, $destinationimage);

	//clean up after ourselves.
	imagedestroy($img_dst);
	imagedestroy($img_src);
	$destinationimage="";
	$temph="";
	$tempwidth="";
	$tempheight="";
	$sourceimage="";
	$newwidth="";
	$newheight="";
}

function resizePNG($sourceimage, $destinationimage, $newwidth, $newheight){

	//Variables used in this script
	//IT IS ONLY NECESSARY TO PASS ONE OF THE NEW DIMENSIONS AS THE OTHER
	//DIMENSION IS CACULATED.
	//
	//Pass the unknown dimension as the number 0
	//
	//$newwidth   <--Width of resized image
	//$newheight  <--Height of resized image
	//$sourceimage <--Source to resize
	//$destinationimage <--Image name to output


	//get the current image dimensions
	$imagedimensions = getimagesize($sourceimage);


	//see if a dimension was passed, if so then calculate the other dim.
	//if both dims were passed then do nothing.
	if((!$newwidth && !$newheight) || ($newwidth==0 && $newheight==0)){
		die ('must specify at least one dimension');
	}

	if(!$newwidth || $newwidth==0){
		$temph=($newheight/$imagedimensions[1]);
		$newwidth=intval($temph*$imagedimensions[0]);
	}

	if(!$newheight || $newheight==0){
		$temph=($newwidth/$imagedimensions[0]);
		$newheight=intval($temph*$imagedimensions[1]);
	}

	//create pointers to work with
	$img_src=imagecreatefrompng($sourceimage);
	$img_dst=imagecreatetruecolor($newwidth,$newheight);

	//resize and output the image
	imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $newwidth, $newheight, $imagedimensions[0], $imagedimensions[1]);
	imagepng($img_dst, $destinationimage);
	imagedestroy($img_dst);
}


//This was a workaround to no gif write support until the patent expiration
//in JULY 2004, it reads in a gif but saves the gif as a jpg.  The result
//is a gif resized and saved in jpeg format
function resizeGIF2JPEG($sourceimage, $destinationimage, $newwidth, $newheight){

	//Variables used in this script
	//IT IS ONLY NECESSARY TO PASS ONE OF THE NEW DIMENSIONS AS THE OTHER
	//DIMENSION IS CACULATED.
	//
	//Pass the unknown dimension as the number 0
	//
	//$newwidth   <--Width of resized image
	//$newheight  <--Height of resized image
	//$sourceimage <--Source to resize
	//$destinationimage <--Image name to output


	//get the current image dimensions
	$imagedimensions = getimagesize($sourceimage);


	//see if a dimension was passed, if so then calculate the other dim.
	//if both dims were passed then do nothing.
	if((!$newwidth && !$newheight) || ($newwidth==0 && $newheight==0)){
		die ('must specify at least one dimension');
	}

	if(!$newwidth || $newwidth==0){
		$temph=($newheight/$imagedimensions[1]);
		$newwidth=intval($temph*$imagedimensions[0]);
	}

	if(!$newheight || $newheight==0){
		$temph=($newwidth/$imagedimensions[0]);
		$newheight=intval($temph*$imagedimensions[1]);
	}

	//create pointers to work with
	$img_src=imagecreatefromgif($sourceimage);
	$img_dst=imagecreatetruecolor($newwidth,$newheight);

	//resize and output the image
	imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $newwidth, $newheight, $imagedimensions[0], $imagedimensions[1]);
	imagejpeg($img_dst, $destinationimage);
	imagedestroy($img_dst);
}

function resizeGIF($sourceimage, $destinationimage, $newwidth, $newheight){

	//Variables used in this script
	//IT IS ONLY NECESSARY TO PASS ONE OF THE NEW DIMENSIONS AS THE OTHER
	//DIMENSION IS CACULATED.
	//
	//Pass the unknown dimension as the number 0
	//
	//$newwidth   <--Width of resized image
	//$newheight  <--Height of resized image
	//$sourceimage <--Source to resize
	//$destinationimage <--Image name to output

	//get the current image dimensions
	$imagedimensions = getimagesize($sourceimage);


	//see if a dimension was passed, if so then calculate the other dim.
	//if both dims were passed then do nothing.
	if((!$newwidth && !$newheight) || ($newwidth==0 && $newheight==0)){
		die ('must specify at least one dimension');
	}

	if(!$newwidth || $newwidth==0){
		$temph=($newheight/$imagedimensions[1]);
		$newwidth=intval($temph*$imagedimensions[0]);
	}

	if(!$newheight || $newheight==0){
		$temph=($newwidth/$imagedimensions[0]);
		$newheight=intval($temph*$imagedimensions[1]);
	}

	//create pointers to work with
	$img_src=imagecreatefromgif($sourceimage);
	$img_dst=imagecreatetruecolor($newwidth,$newheight);

	//resize and output the image
	imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $newwidth, $newheight, $imagedimensions[0], $imagedimensions[1]);
	imagegif($img_dst, $destinationimage);
	imagedestroy($img_dst);
}

function log_action($action, $status, $galleryname, $details){

	global $imggallerypath;
	
	//LOG OUR UPLOAD
	$addr = gethostbyaddr($_SERVER['REMOTE_ADDR']);   
            
	if(!file_exists($imggallerypath."logs/".$galleryname.".log.csv")){
		$firstline="DATE_TIME, USERNAME, ACTION, STATUS, HOST, DETAILS";
	}else{
		$firstline=false;
	}
	$log = @fopen($imggallerypath."logs/".$galleryname.".log.csv", 'a');
	if($firstline){
		@fputs($log, $firstline."\n");
	}
	@fputs($log, date("m/d/y g:i a").", {$_SESSION['s_userName']}, {$action}, {$status}, {$addr}, {$details}\n" );
	@fclose($log);

}

/* opens files, returns contents */
function openFile($filename){
	$readsize=4096;
    
	//open file and aquire shared lock
	$fv=fopen($filename, 'r');
	flock($fv, LOCK_SH);
  
	//initialize vars and get array of file
	$temp=array();
	$counter=0;
	while ($line=fgets($fv, $readsize)) {
          $temp[$counter] = $line;
          $counter=$counter+1;
	}
        
	//relinquish lock and close file
	flock($fv, LOCK_UN);
	fclose($fv);
        
	//return our array
	return $temp;
}


$loopvars->set_var("version", "4.0.0");
$is_premium=0;
$copyright='<a href="http://www.schiffner.com/software/panda-image-gallery/" target="_blank">Panda Image Gallery v'.$loopvars->get_var("version").'</a> - &copy; '.date('Y').' Christopher Schiffner';

?>
