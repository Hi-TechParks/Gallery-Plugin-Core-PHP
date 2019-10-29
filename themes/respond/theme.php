<?php 
/* MAIN THEME FILE
   This is the only file included by the core
   ---------------------------------------------------------*/
   
//include any theme files necessary
require $loopvars->get_var("theme_path")."/functions.php";
require $loopvars->get_var("theme_path")."/options.php";

//register theme styles: register_style(PATH_TO_CSS, CSS_VERSION, MEDIA_TYPE);
register_style($loopvars->get_var("theme_path")."/content.css", "1.0", "screen");

//register theme scripts: register_style(PATH_TO_SCRIPT, SCRIPT_TYPE, CHARACTER_ENCODING);
//register_script($loopvars->get_var("theme_path")."/script.js", "text/javascript", "UTF-8");

//This is the main theme function called by the core. We pass the core content in case the theme author wants to manipulate it.
function do_theme($core_content, $loopvars, $theme_settings, $core_settings) {

	//Call pre-core content
	$theme_content_before=get_theme_before($loopvars, $theme_settings, $core_settings);
	
	//Call post-core content
	$theme_content_after=get_theme_after($loopvars, $theme_settings, $core_settings);
	
	//return all data.
	return $theme_content_before.$core_content.$theme_content_after;
}

?>