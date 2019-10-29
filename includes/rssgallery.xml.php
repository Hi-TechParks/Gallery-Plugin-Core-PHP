<?php	
//#######################################################################\\
//# Author: Christopher Schiffner                                       #\\
//# Filename: rssgallery.xml.php                                        #\\
//# Copyright: Christopher Schiffner, All Rights Reserved               #\\
//# Description: Image gallery software, view readme for more info.     #\\
//#                                                                     #\\
//# License: This software is free to use for personal applications.    #\\
//#          There is a small registration fee for commercial           #\\
//#          applications.  Please contact chris@schiffner.com if       #\\
//#          you wish to use this program on a commercial website.      #\\
//#######################################################################\\

include_once '../includes/class/loopvars.php';
$loopvars = new clsLoopVars();
$theme_settings = new clsLoopVars();

include_once 'functions.php';

$server_path = str_replace("/includes", "", dirname($_SERVER['PHP_SELF']));

header('Content-Type: text/xml');

	$gallery='';

	if(ISSET($_GET['g']))
		$gallery=$_GET['g'];

        $g_Directory="../galleries/".$gallery."/";

	$includeFlag='1'; //only way for our included files to know their included.
                
        if(!$gp)
                $gp=1;
                
        //get the directory list, each image gallery is in its own directory
        $g_Listing = dir($g_Directory);
        while($entry = $g_Listing->read()) {
               	if( (!is_dir($g_Directory."/".$entry)) && (substr($entry, 0, 3) != 'thm') && (substr($entry, 0, 7) != 'lowres_')
                        && ( is_movie($entry) || is_image($entry)) ) {

                    //date modified
                    $sortCriteria=filemtime($g_Directory."/".$entry);                            
                    $images[]=array($entry, $sortCriteria);
                }
            
        }
        $g_Listing->close();
        sortByDate($images, "dateModifiedDESC");

echo '<?xml version="1.0" encoding="UTF-8"?>
      <rss version="2.0">
      <channel>';

include '../conf/config.php';

$x=0;
if(count($images) > 9){
    $x=9;
}else{
    $x=count($images);
}

if (file_exists($g_Directory."index.php")){
    include $g_Directory."index.php";
    $posterID=htmlspecialchars(strtolower(trim($gallery_options['poster'])), ENT_QUOTES);
    $datePosted=htmlspecialchars($gallery_options['date_posted'], ENT_QUOTES);
    $title=htmlspecialchars($gallery_options['title'], ENT_QUOTES);
    $description=htmlspecialchars($gallery_options['description'], ENT_QUOTES);
} else {
    $posterID="Unavailable";
    $datePosted="Unavailable";
    $title="Title Unknown";
    $description="No description given";
}

echo "<title>".$title." Image Feed</title>
      <link>http://".$_SERVER['HTTP_HOST'].$server_path."/index.php?a=vg&amp;g=$gallery</link>
      <description>$description</description>";

$y=0;
for ($y = 0 ; $y <= $x; $y++) {
	echo "<item>\n";
	echo "<title>".$images[$y][0]."</title>\n";
	echo "<description>".$images[$y][0]."</description>\n";
	echo "<link>http://".$_SERVER['HTTP_HOST'].$server_path."/index.php?a=vi&amp;g=".$gallery."&amp;i=".$images[$y][0]."</link>\n";
	echo "</item>\n";
}

echo '</channel>
      </rss>';
?>
