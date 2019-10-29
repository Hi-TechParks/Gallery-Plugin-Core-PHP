<?php
$imgNameHTMLCommentOpen='';
$imgNameHTMLCommentClose='';

if((isset($_GET['cms']) && $_GET['cms']==1 && $core_settings['unified_mode']==1) || (isset($_POST['cms']) && $_POST['cms']==1 && $core_settings['unified_mode']==1) || ($_SESSION['cms']==1 && $core_settings['unified_mode']==1) || $core_settings['unified_mode']==2){
    $cms=1;
    $_SESSION['cms']=1;
    $target=" target='_top' "; 
    $loopvars->set_var("link_target", " target='_top' ");
    $pageFileName=$core_settings['base_url'];
    $cms_link="&cms=1";
    $cms_link2="?cms=1";
    $current_url_vars = explode("?", $_SERVER['REQUEST_URI'])[1];
    $loopvars->set_var("current_url", $core_settings['base_url']."?".$current_url_vars);
    $loopvars->set_var("current_url_linksafe", urlencode($core_settings['base_url']."?".$current_url_vars));
}else{
    $cms=0;
    $_SESSION['cms']=0;
    $target=""; 
    $pageFileName = substr($_SERVER['PHP_SELF'], (strrpos($_SERVER['PHP_SELF'], "/")+1), (strlen($_SERVER['PHP_SELF'])));
    $cms_link="";
    $cms_link2="";
    $loopvars->set_var("current_url", $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    $loopvars->set_var("current_url_linksafe", urlencode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
}

//detect wordpress login--fallback to regular login check
$logged_in=is_logged_in();

$core_settings['theme_name']='themes/'.$core_settings['theme_name'];
$styleSheetPath=$core_settings['theme_name'].'/';
$templateImages=$core_settings['theme_name']."/images";
$scriptsPath="scripts/";
$registrationPath=$pageFileName."?a=reg";
$sitemapURL='includes/sitemap.xml.php';
//$loopvars->set_var("ajax_login_link", "admin/index.php?action=plainlogin&amp;cms=$cms");

if(is_wordpress()){
	$loopvars->set_var("manage_link", '/wp-admin/admin.php?page=panda_image_gallery/wp_integration.php');
	$loopvars->set_var("manage_link_target", ' target=\'_top\' ');
}else{
	$loopvars->set_var("manage_link", 'admin/index.php'.$cms_link2);
	$loopvars->set_var("manage_link_target", '');
}
$loopvars->set_var("login_link", 'admin/index.php?kickmeto='.urlencode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
$loopvars->set_var("logout_link", 'admin/logout.php?kickmeto='.urlencode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
$loopvars->set_var("password_recovery_link", "index.php?a=pr&amp;cms=$cms");
$loopvars->set_var("registration_link", "index.php?a=reg&amp;cms=$cms");
$loopvars->set_var("change_pass_link", "admin/index.php?action=chgpass");
$loopvars->set_var("edit_config_link", "admin/index.php?action=editconf");
$loopvars->set_var("edit_users_link", "admin/index.php?action=editusers");
$loopvars->set_var("base_url", $pageFileName);
$loopvars->set_var("unified_mode", $cms);
$loopvars->set_var("theme_path", $core_settings['theme_name']);
$loopvars->set_var("user_registration_enabled", $core_settings['allow_user_registration']);
$loopvars->set_var("is_admin_page", false);

$a='';
$currentpage='';
$previouspage='';
$nextpage='';
$pageoutstringA='';
$iptc='';
$gallerycount='';
$imagesize='';
$i='';
$pg='';
$topLeftCorner=1;
$gp='';
$appendGP='';
$appendPG1='';
$appendPG2='';
$username='';
$galleryListingSortOrder='';
$galleryViewSortOrder='';
$r='';
$searchTerm='';
$templateUpload='';
$templateEditGallery='';
$templateCreateGallery='';
$templateDownloadImage='';
$templateSearchForm='';
$templateSortBy='';

if(isset($_GET['a'])){
	
	if( $_GET['a'] == "s" ){
		
		if( isset($_GET['q']) ){
			if( trim($_GET['q']) != "" ){
		    	$searchTerm=trim($_GET['q']);
		    	$a=$_GET['a'];
		    	$loopvars->set_var("action", $_GET['a']);
			}
		}
		
	}else{
	    $a=$_GET['a'];
	    $loopvars->set_var("action", $_GET['a']);
	}
}

if(isset($_GET['g'])){
    $gallery=$_GET['g'];
}

if(isset($_GET['r'])){
    $r=$_GET['r'];
}

if(isset($_GET['image'])){
    $image=$_GET['image'];
}

if(isset($_GET['cp'])){
    $currentpage=$_GET['cp'];
}

if(isset($_GET['i'])){
    $i=html_entity_decode($_GET['i'], ENT_QUOTES);
}

if(isset($_GET['gp'])){
    $gp=$_GET['gp'];
}

if(isset($_GET['pg'])){
    $pg=$_GET['pg'];
}

if(isset($_POST['galleryListingSortOrder'])){
    $_SESSION['s_galleryListingSortOrder']=$_POST['galleryListingSortOrder'];
}

if(isset($_POST['galleryViewSortOrder'])){
    $_SESSION['s_galleryViewSortOrder']=$_POST['galleryViewSortOrder'];
}

if(isset($_SESSION['s_userName'])){
    $username=$_SESSION['s_userName'];
	$loopvars->set_var("current_user", $_SESSION['s_userName']);
}

//set default gallery sort order is none is selected
if(!isset($_SESSION['s_galleryListingSortOrder'])){
	$_SESSION['s_galleryListingSortOrder']=$core_settings['default_gallery_sort'];
}
//set default view gallery sort order is none is selected
if(!isset($_SESSION['s_galleryViewSortOrder'])){
	if( ($loopvars->compare("action", "vi") || $loopvars->compare("action", "vg")) && isset($gallery) && $gallery != "" ){
	
		if (file_exists("galleries/".$gallery."/index.php")) {
			$gallery_options="";
		    $includeFlag='1';
			ob_start();
			include "galleries/".$gallery."/index.php";
			ob_end_clean();
			$includeFlag='0';
		}
			
		$_SESSION['s_galleryViewSortOrder']=$gallery_options["sort_order"];
		
		$gallery_options="";
	}else{
		$_SESSION['s_galleryViewSortOrder']=$core_settings['default_image_sort'];
	}
}
$galleryListingSortOrder=$_SESSION['s_galleryListingSortOrder'];
$galleryViewSortOrder=$_SESSION['s_galleryViewSortOrder'];

//maintain gallery listing page for link backs
if($gp!=''){
    $appendGP="&gp=$gp";
    $appendGP2="?gp=$gp";
    $appendPG="&pg=$pg";
}else{
	$appendGP2="";
    $appendPG="?pg=$pg";
}

//personal gallery capability
if($pg!=''){
    $appendPG1="&pg=$pg";
    $appendPG2="?pg=$pg";
    $backtogallerylink=$pageFileName.$appendGP2.$appendPG;
}else{
    $backtogallerylink=$pageFileName.$appendGP2;
}

//$_SESSION['viewimage']=1;

$templateSearchForm="<form method='get' action='{$pageFileName}' enctype='multipart/form-data' $target>
                        <input type='hidden' name='cms' value='{$cms}' /> 
                        <input type='hidden' name='a' value='s' />
                        <input type='search' name='q' value='{$searchTerm}' class='search_input' />
                        <input type='submit' value='search' class='search_button' />
		     		</form>";


function prepare_theme_vars(){

	global $loopvars;
	global $core_settings;
	global $backtogallerylink;
	global $currentpage;
	global $appendGP;
	global $currentimage;
	global $maximagecount;
	global $searchTerm;
	global $previmage;
	global $previmagelink;
	global $nextimage;
	global $nextimagelink;
	global $previouspage;
	global $nextpage;
	global $core_settings;
	global $templateSearchForm;
	global $templateCreateGallery;
	global $templateDeleteGallery;
	global $templateEditGallery;
	global $templateUpload;
	global $templateGalleryCollaborators;
	global $currentpage;
	global $thm_set;
	global $concealPath;
	global $pageFileName;
	global $username;
	global $pageFileName;
	global $pageoutstringA;
	global $gallery;
	global $galleriesRoot;

	$loopvars->set_var("gallery_root", $galleriesRoot);
	$loopvars->set_var("gallery_listing_link", $backtogallerylink);
	$loopvars->set_var("back_to_gallery_link", $pageFileName."?a=vg&amp;g={$gallery}&amp;cp={$currentpage}{$appendGP}");
	$loopvars->set_var("viewed_image_count", $currentimage+1);
	$loopvars->set_var("total_image_count", $maximagecount+1);
	$loopvars->set_var("search_term", $searchTerm);
	$loopvars->set_var("previous_image_selector", $previmage);
	$loopvars->set_var("previous_image_link", $previmagelink);
	$loopvars->set_var("next_image_selector", $nextimage);
	$loopvars->set_var("next_image_link", $nextimagelink);
	$loopvars->set_var("previous_page_selector", $previouspage);
	$loopvars->set_var("next_page_selector", $nextpage);
	$loopvars->set_var("theme_path", $core_settings['theme_name']);
	$loopvars->set_var("search_form_block", $templateSearchForm);
	$loopvars->set_var("create_gallery_block", $templateCreateGallery);
	$loopvars->set_var("delete_gallery_block", $templateDeleteGallery);
	$loopvars->set_var("edit_gallery_block", $templateEditGallery);
	$loopvars->set_var("upload_files_block", $templateUpload);
	$loopvars->set_var("edit_collaborators_block", $templateGalleryCollaborators);
	$loopvars->set_var("gallery_link", $pageFileName."?a=vg&amp;g={$gallery}&amp;cp={$currentpage}");
	$loopvars->set_var("is_thumbnail_set", $thm_set);
	$loopvars->set_var("protect_image_source", $core_settings['protect_image_source']);
	$loopvars->set_var("conceal_paths", $concealPath);
	$loopvars->set_var("personal_gallery_link", $pageFileName."?pg=".$username);
	$loopvars->set_var("personal_gallery_link_gallery_tile", $pageFileName."?pg=".$loopvars->get_var("gallery_poster"));
	$loopvars->set_var("page_numbers", $pageoutstringA);
	$loopvars->set_var("gallery_name", $gallery);
	$loopvars->set_var("scripts_path", $scriptsPath);
	
	unset($loopvars);
	unset($backtogallerylink);
	unset($currentpage);
	unset($appendGP);
	unset($currentimage);
	unset($maximagecount);
	unset($searchTerm);
	unset($previmage);
	unset($previmagelink);
	unset($nextimage);
	unset($nextimagelink);
	unset($previouspage);
	unset($nextpage);
	unset($templateSearchForm);
	unset($templateCreateGallery);
	unset($templateDeleteGallery);
	unset($templateEditGallery);
	unset($templateUpload);
	unset($currentpage);
	unset($thm_set);
	unset($concealPath);
	unset($pageFileName);
	unset($username);
	unset($pageFileName);
	unset($pageoutstringA);
	unset($gallery);
	unset($sortForm);
}

?>