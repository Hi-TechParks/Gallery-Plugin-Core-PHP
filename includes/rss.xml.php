<?php	
//#######################################################################\\
//# Author: Christopher Schiffner                                       #\\
//# Filename: rss.xml.php   	                                            #\\
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

include 'functions.php';

$server_path = str_replace("/includes", "", dirname($_SERVER['PHP_SELF']));

header('Content-Type: text/xml');

        $g_Directory="../galleries/";
                
	$includeFlag=1;

        if(!$gp)
                $gp=1;
                
        //get the directory list, each image gallery is in its own directory
        $g_Listing = dir($g_Directory);
        while($entry = $g_Listing->read()) {
            if ($entry != "." && $entry != ".." && $entry !="logs") {
                if(is_dir($g_Directory."/".$entry)) {
                        //To allow for personal gallery display only we add the persons username to the search criteria
                       if (file_exists($g_Directory."/".$entry."/index.php")) {
				$includeFlag='1'; //only way for our included files to know their included.
                                include $g_Directory."/".$entry."/index.php";
         
                                //date posted
                                $sortCriteria=$gallery_options[1];
                                $sortCriteria=explode("/",$sortCriteria);
                                $sortCriteria=mktime(0,0,0,intval($sortCriteria[0]),intval($sortCriteria[1]),intval($sortCriteria[2])) ;
                        } else {
                                $sortCriteria=9999999999999999999;
                        }

                        $galleries[]=array($entry, $sortCriteria, $gallery_options['poster'], $gallery_options['title'], $gallery_options['description'], $gallery_options['date_posted']);
                }
            }
        }
        $g_Listing->close();
        sortByDate($galleries, "dateDESC");

echo '<?xml version="1.0" encoding="UTF-8"?>
      <rss version="2.0">
      <channel>';

include '../conf/config.php';

$x=0;
if(count($galleries) > 9){
    $x=9;
}else{
    $x=count($galleries)-1;
}

echo "<title>".$core_settings['gallery_title']."</title>
      <link>http://".$_SERVER['HTTP_HOST'].$server_path."/index.php</link>
      <description>".$core_settings['gallery_description']."</description>";

$y=0;
for ($y = 0 ; $y <= $x; $y++) {
	echo "<item>\n";
	echo "<title>".$galleries[$y][3].", by ".$galleries[$y][2]."</title>\n";
	echo "<description>".$galleries[$y][4]."</description>\n";
	echo "<link>http://".$_SERVER['HTTP_HOST'].$server_path."/index.php?a=vg&amp;g=".$galleries[$y][0]."</link>\n";
	echo "<pubDate>".$galleries[$y][5]."</pubDate>";
	echo "</item>\n";
}

echo '</channel>
      </rss>';
?>
