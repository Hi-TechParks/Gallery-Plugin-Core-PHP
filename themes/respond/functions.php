<?php
//theme functions

/* RETURNS THEME PRE-CORE CONTENT */
function get_theme_before($loopvars, $theme_settings, $core_settings) {
	
	$temp_content="<!DOCTYPE html><html lang='en'><head>".get_core_head()."</head>";
	
	if($loopvars->get_var('is_admin_page')){
		$temp_content.="<body class='admin_body'>";
	}else{
		$temp_content.="<body>";
	}

	
	$temp_content.="<div class='container'>
						<div class='navigation'>";
	
	$temp_content.="<a href='{$loopvars->get_var("gallery_listing_link")}' {$loopvars->get_var("link_target")} class='gallery_home_nav button'>EVENTOS DA ESCOLA</a>";
	
	
	if($core_settings['allow_user_registration'] && !(isset($_SESSION['s_userName'])) && !$loopvars->get_var("wordpress") ){
		$temp_content.="<a href='{$loopvars->get_var("registration_link")}' {$loopvars->get_var("link_target")} class='registration_nav button'>Registrar Usuário</a>";
	}
	
	


	if(isset($_SESSION['s_userName']) && !$loopvars->get_var("wordpress") ){
		$temp_content.="<a href='{$loopvars->get_var("logout_link")}' class='logout_nav button'>Logout</a><a href='{$loopvars->get_var("change_pass_link")}' {$loopvars->get_var("link_target")} class='change_password_nav button'>Password</a>";
	}else{
		if(!$loopvars->get_var("wordpress")){
			$temp_content.="<a {$loopvars->get_var("ajax_login_javascript_block")} href='{$loopvars->get_var("login_link")}' class='login_nav button'>Login</a>";
		}
	}

/*	*************************** REMOVENDO O BOTÃO - GERENCIAR *********************************************

	if((!$loopvars->get_var("wordpress") || is_logged_in()) && !strrpos($loopvars->get_var("current_url"), '/admin/index.php')){
		$temp_content.="<a href='{$loopvars->get_var("manage_link")}' class='manage_home_nav button' ".$loopvars->get_var("manage_link_target").">Gerenciar</a>";
	}
	$temp_content.="</div> <!--end navigation-->";
*/

	
	if(!$loopvars->compare("action", "vg") && !$loopvars->compare("action", "vi") && !$loopvars->compare("action", "reg") && !$loopvars->compare("action", "pr") && !$loopvars->compare("action", "chgpass") && !strrpos($loopvars->get_var("current_url"), '/admin/index.php') && !strrpos($loopvars->get_var("current_url"), '/admin') && !strrpos($loopvars->get_var("current_url"), '/admin/') && !$loopvars->compare("personal_gallery_user", "")){
			
				$temp_content.="<div class='personal_gallery_label'>Exibir <a href='{$loopvars->get_var("personal_gallery_link_gallery_tile")}' {$loopvars->get_var("link_target")}>{$loopvars->get_var("personal_gallery_user")}</a> galeria(s) <a href='{$loopvars->get_var("base_url")}' {$loopvars->get_var("link_target")} class='personal_gallery_return_all button'>GALERIAS</a></div>";
	}
	
	$temp_content.="<div class='action_bar'>";

	//left
	if(!$loopvars->compare("gallery_name", "") && ($loopvars->compare("action", "vg") || $loopvars->compare("action", "vi")) ){
	
			$temp_content.="<div class='gallery_label'>Exibir <a href='{$loopvars->get_var("gallery_link")}' title='{$loopvars->get_var("gallery_description")}' {$loopvars->get_var("link_target")}>{$loopvars->get_var("gallery_title")}</a> Autor(a) <a href='{$loopvars->get_var("personal_gallery_link_gallery_tile")}' title='Clique para exibir mais galerias de {$loopvars->get_var("gallery_poster")}' {$loopvars->get_var("link_target")}>{$loopvars->get_var("gallery_poster")}</a> em {$loopvars->get_var("gallery_date_posted")}<div class='vg_top_admin_options'>{$loopvars->get_var("edit_gallery_block")}{$loopvars->get_var("upload_files_block")}{$loopvars->get_var("delete_gallery_block")}</div></div>";
	}

	if($loopvars->compare("gallery_name", "") && $loopvars->compare("action", "s") ){
		$temp_content.="<div class='search_label'>Resultado da pesquisa: <span>".htmlentities($loopvars->get_var("search_term"))."</span></div>";
	}

	if($loopvars->compare("gallery_name", "") && $loopvars->compare("action", "") && isset($_SESSION['s_userName']) && can_create_gallery()){
		$temp_content.="{$loopvars->get_var("create_gallery_block")}";
	}
	
	
	
	//right
	if($loopvars->compare("action", "vi")){
		$temp_content.="<div class='file_count'>{$loopvars->get_var("viewed_image_count")} / {$loopvars->get_var("total_image_count")}</div>";
	}

	if(($loopvars->compare("action", "vg") || $loopvars->compare("action", "s") || $loopvars->compare("action", "")) && !strrpos($loopvars->get_var("current_url"), '/admin/')){
		$temp_content.="<div class='sort_form'><label>Sort:</label> {$loopvars->get_var("sort_form")}</div>";
	}
	
	$temp_content.="</div>";
	
/* ************************************** REMOVE CAMPO PESQUISA ********************************

	//search
	if($loopvars->compare("gallery_name", "") && ($loopvars->compare("action", "s") || $loopvars->compare("action", "")) ){
		$temp_content.="<div class='search_form'>{$loopvars->get_var("search_form_block")}</div>";
	}
	$temp_content.="<div class='clear'></div>";
	
*/	
	
	//our links differ by where we are
	if($loopvars->compare("action", "vi")){
	
		$temp_content.="<div class='image_nav'>";
		
		if($loopvars->get_var("previous_image_selector")){
			$temp_content.="<a href='{$loopvars->get_var("previous_image_link")}' {$loopvars->get_var("link_target")} class='previous_image_nav button'>VOLTAR</a>";
		}
		
		$temp_content.="<a href='{$loopvars->get_var("back_to_gallery_link")}' {$loopvars->get_var("link_target")} class='back_to_thumbs_nav button'>GALERIAS</a>";
	
		if($loopvars->get_var("next_image_selector")){
	 		$temp_content.="<a href='{$loopvars->get_var("next_image_link")}' {$loopvars->get_var("link_target")} class='next_image_nav button'>AVANÇAR</a>";
		}
	
		$temp_content.="</div> <!--end action_bar-->";
	
	}else if(!$loopvars->compare("action", "reg") && !$loopvars->compare("action", "pr") && !$loopvars->compare("action", "chgpass") && !$loopvars->compare("page_numbers", "")){
	
		
	    //by default our engine gives us arrows for page navigation. If we want something else here lets use str_replace
	    $previouspage=str_replace("&lt;", "<span class='prev_page_nav button'>VOLTAR</span>", $loopvars->get_var("previous_page_selector"));
	    $nextpage=str_replace("&gt;", "<span class='prev_page_nav button'>AVANÇAR</span>", $loopvars->get_var("next_page_selector"));
	
		//our core gives us a string that all old-school and spaced and bracket for the current page. We don't want all that--strip it
		$loopvars->set_var("page_numbers", str_replace("[&nbsp;", "", $loopvars->get_var("page_numbers")));
		$loopvars->set_var("page_numbers", str_replace("&nbsp;]", "", $loopvars->get_var("page_numbers")));
		$loopvars->set_var("page_numbers", str_replace("&nbsp;", "", $loopvars->get_var("page_numbers")));
		
		$temp_content.="<div class='page_nav'>";
	    $temp_content.="<div class='page_prev'>".$previouspage."&nbsp;</div><div class='pages'>".$loopvars->get_var("page_numbers")."</div><div class='page_next'>&nbsp;".$nextpage."</div>";
	    $temp_content.="</div><!--end page_nav--><div class='clear'></div>";
	}
	
	return $temp_content;
}

/* RETURNS THEME POST-CORE CONTENT */
function get_theme_after($loopvars, $theme_settings, $core_settings) {
	
	if(!$loopvars->compare("action", "reg") && !$loopvars->compare("action", "pr") && !$loopvars->compare("action", "chgpass") && !$loopvars->compare("page_numbers", "")){
	
	    //by default our engine gives us arrows for page navigation. If we want something else here lets use str_replace
	    $previouspage=str_replace("&lt;", "<span class='prev_page_nav button'>VOLTAR</span>", $loopvars->get_var("previous_page_selector"));
	    $nextpage=str_replace("&gt;", "<span class='prev_page_nav button'>AVANÇAR</span>", $loopvars->get_var("next_page_selector"));
	
		$temp_content.="<div class='page_nav'>";
	    $temp_content.="<div class='page_prev'>".$previouspage."&nbsp;</div><div class='pages'>".$loopvars->get_var("page_numbers")."</div><div class='page_next'>&nbsp;".$nextpage."</div>";
	    $temp_content.="</div><div class='clear'></div>";
	}
	
	$temp_content.="</div> <!--end container-->";
	
	$temp_content.=get_core_footer();
	
	$temp_content.="</body>
	</html>
	";
	
	return $temp_content;

}


/* RETURNS GALLERY DIRECTORY LISTING TILE TO THE CORE */
function get_theme_gallery_tile($loopvars, $theme_settings, $core_settings) {

	$temp_content="<div class='gallery_listing_tile'>";
	
	$temp_content.="<div class='gallery_thumbnail'><a href='{$loopvars->get_var("gallery_link")}' {$loopvars->get_var("link_target")}><img alt='' src='".$loopvars->get_var("thumbnail_media_path")."' border='4' /></a></div>";
			
	$temp_content.="<div class='gallery_data'>";
	
	if($theme_settings->get_var("show_gallery_title")){
	
		$temp_content.="<a href='".$loopvars->get_var("gallery_link")."' ".$loopvars->get_var("link_target")."><h2 class='gallery_title'>".$loopvars->get_var('gallery_title')."</h2></a>";
		
	}
	if($theme_settings->get_var("show_gallery_description")){
	
		$temp_content.="<h3 class='gallery_description'>".$loopvars->get_var("gallery_description")."</h3>";
	}
	
	if($theme_settings->get_var("show_poster_name")){
		
		$temp_content.="<p class='gallery_posted_by'><span>Autor(a):</span> ";
		
		if($loopvars->get_var("personal_gallery_user")!=$loopvars->get_var("gallery_poster")){
			
			$temp_content.="<a href='{$loopvars->get_var("personal_gallery_link_gallery_tile")}' {$loopvars->get_var("link_target")}>{$loopvars->get_var("gallery_poster")}</a>";
		
		}else{
			
			$temp_content.=$loopvars->get_var("gallery_poster");
		}
		
		$temp_content.="</p>";
	}

	if($theme_settings->get_var("show_posted_date")){
		
		$temp_content.="<p class='gallery_posted_date'><span>Publicado:</span> {$loopvars->get_var("gallery_date_posted")}</p>";

	}

	if(!$loopvars->compare("gallery_copyright", "")){

		$temp_content.="<p class='gallery_copyright'><span>Copyright:</span> {$loopvars->get_var("gallery_copyright")}</p>";

	}

	$temp_content.="</div>";
	
	$temp_content.="<div class='owner_options'>".$loopvars->get_var("edit_gallery_block")." ".$loopvars->get_var("upload_files_block")." ".$loopvars->get_var("delete_gallery_block")."</div>";

	$temp_content.="</div>";
	
	if(!$loopvars->get_var("last_tile")){
		$temp_content.="<div class='gallery_divider'><hr/></div>";
	}else{
		$temp_content.="<div class='gallery_listing_tile shim'></div><div class='gallery_listing_tile shim'></div><div class='gallery_listing_tile shim'></div>";
	}
		
	return $temp_content;
}

/* RETURNS GALLERY THUMBNAIL TILE TO THE CORE */
function get_theme_thumbnail_tile($loopvars, $theme_settings, $core_settings) {

	$temp_content="<div class='thumb_tile'>";
	
	if($core_owner_options!=""){
		$classer="admin";
	}
	
	$temp_content.="<a href='".$loopvars->get_var("thumbnail_link") ."' ".$loopvars->get_var("link_target")."><img border='0' src='".$loopvars->get_var("thumbnail_media_path")."'/></a>";
	
	$temp_content.=$loopvars->get_var("core_owner_options");
	
	$temp_content.="</div>";
	
	if($loopvars->get_var("last_tile")){
		$temp_content.="<div class='thumb_tile shim'></div><div class='thumb_tile shim'></div><div class='thumb_tile shim'></div><div class='thumb_tile shim'></div><div class='thumb_tile shim'></div>";
	}
	
	return $temp_content;
}

/* RETURNS GALLERY THUMBNAIL TILE TO THE CORE */
function get_theme_image($loopvars, $theme_settings, $core_settings) {
	
	switch($loopvars->get_var("media_type")){
	
		case "movie":
		
			$temp_content.="<video class='video_embed' controls><source src='{$loopvars->get_var("view_media_path")}' type='video/mp4'></video>";
			
			if(trim($loopvars->get_var("core_owner_options"))!=""){
				$temp_content.=$loopvars->get_var("core_owner_options");
			}
			$temp_content.="<div class='file_details'>";
			if(trim($loopvars->get_var("file_caption"))!=""){
				$temp_content.="<div class='file_caption'>".$loopvars->get_var("file_caption")."</div>";
			}
			if(trim($loopvars->get_var("gallery_copyright"))!=''){
				$temp_content.="<div class='gallery_copyright'>&copy; ".$loopvars->get_var("gallery_copyright")."</div>";
			}
			$temp_content.="</div>";
			
			if($loopvars->get_var("movie_download_path") && trim($loopvars->get_var("movie_download_path"))!=""){
				$temp_content.="<div class='download_hires'><a href='".$loopvars->get_var("movie_download_path")."' ".$loopvars->get_var("link_target")." class='download_original_nav button'>DOWNLOAD</a></div>";
			}
			
			break;
			
		case "image":
		
			$temp_content.="<div class='display_image_container'><img src='{$loopvars->get_var("view_media_path")}' class='display_image'/></div>";
			
			if(trim($loopvars->get_var("core_owner_options"))!=""){
				$temp_content.=$loopvars->get_var("core_owner_options");
			}
			
			$temp_content.="<div class='file_details'>";
			if(trim($loopvars->get_var("file_caption"))!=""){
				$temp_content.="<div class='file_caption'>".$loopvars->get_var("file_caption")."</div>";
				$share_title = $loopvars->get_var("file_caption");
			}else{
				$share_title = $loopvars->get_var("gallery_title");
			}
			
			if(trim($loopvars->get_var("gallery_copyright"))!=''){
				$temp_content.="<div class='file_copyright'>&copy; ".$loopvars->get_var("gallery_copyright")."</div>";
			}
			$temp_content.="</div>";
				
			$temp_content.="<div class='file_meta_links'>";
				
			if($loopvars->get_var("image_download_path") && trim($loopvars->get_var("image_download_path"))!=""){
				$temp_content.="<a href='".$loopvars->get_var("image_download_path")."' ".$loopvars->get_var("link_target")." class='download_original_nav button'>DOWNLOAD</a>";
			}
			
			if($theme_settings->get_var("show_share_button")){
				$temp_content.="<!-- AddToAny BEGIN -->
								<a class=\"a2a_dd button\" href=\"https://www.addtoany.com/share_save?linkurl=".$loopvars->get_var("current_url")."&amp;linkname=".$share_title."\">Share</a>
								<script type=\"text/javascript\">
								var a2a_config = a2a_config || {};
								a2a_config.linkname = \"".$share_title."\";
								a2a_config.linkurl = \"".$loopvars->get_var("current_url")."\";
								a2a_config.prioritize = [\"twitter\", \"facebook\", \"google_plus\", \"linkedin\", \"tumblr\", \"pinterest\", \"wordpress\", \"email\"];
								a2a_config.onclick = 1;
								</script>
								<script type=\"text/javascript\" src=\"//static.addtoany.com/menu/page.js\"></script>
								<!-- AddToAny END -->";
			}
			$temp_content.="</div>";
			
			break;
	}
	
	return $temp_content;
}


?>