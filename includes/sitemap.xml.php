<?php
//#######################################################################\\
//# Author: Christopher Schiffner                                       #\\
//# Filename: sitemap.xml.php                                           #\\
//# Copyright: Christopher Schiffner, All Rights Reserved               #\\
//# Description: Image gallery software, view readme for more info.     #\\
//#                                                                     #\\
//# License: This software is free to use for personal applications.    #\\
//#          There is a small registration fee for commercial           #\\
//#          applications.  Please contact chris@schiffner.com if       #\\
//#          you wish to use this program on a commercial website.      #\\
//#######################################################################\\

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';

if(!file_exists("../conf/config.php")){
    die("<b>You must configure Panda Image Gallery! <br/><br/>Please review config.php.sample, configure as necessary, 
	and rename config.php.sample to config.php. <br/><br/>See the README or installation notes for installation details.</b>");
}

$galleriesRoot="../galleries/";

include_once '../includes/class/loopvars.php';
$loopvars = new clsLoopVars();
$theme_settings = new clsLoopVars();
$core_settings = new clsLoopVars();

include_once 'functions.php';

include_once '../conf/config.php';
$core_settings['theme_name']='../themes/'.$core_settings['theme_name'];
include_once $core_settings['theme_name'].'/options.php';

	$g_Directory = $galleriesRoot;
        $g_Listing = dir($g_Directory);
        while($entry = $g_Listing->read()) {
            if ($entry != "." && $entry != ".." && $entry != "logs") {
                if(is_dir($g_Directory."/".$entry)) {
                        /* PERSONAL GALLERY CHECK */
                        if(($pg!='' && $pg==$galleryInfo[0]) || $pg==''){
                                $galleries[]=$entry;
                        }
         
                }
            }
        }
        $g_Listing->close();
           
        $maxgalleriesperpage=$theme_settings->get_var("gallery_listing_columns")*$theme_settings->get_var("gallery_listing_rows");
        $pages=intval((count($galleries)-1) / $theme_settings->get_var("galleries_per_page"));
        if($pages < ((count($galleries)) / $theme_settings->get_var("galleries_per_page"))){
                $extrapage=1;
        } else {
                $extrapage=0;
        }
        $totalpages=$pages+$extrapage;

	echo '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';


        //compile directory page links
        for($x=1; $x < $totalpages+1; $x++){
		echo "<url>
			<loc>http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php?gp=$x</loc>
			<lastmod>".date('Y-m-d', time())."</lastmod>
			<changefreq>weekly</changefreq>
			<priority>0.8</priority>
			</url>";
        }
        
	echo '</urlset>';
?>