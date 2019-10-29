<?php
//#######################################################################\\
//# Author: Christopher Schiffner                                       #\\
//# Filename: view.php                                                  #\\
//# Copyright: Christopher Schiffner, All Rights Reserved               #\\
//# Description: Image gallery software, view readme for more info.     #\\
//#                                                                     #\\
//# License: This software is free to use for personal applications.    #\\
//#          There is a small registration fee for commercial           #\\
//#          applications.  Please contact chris@schiffner.com if       #\\
//#          you wish to use this program on a commercial website.      #\\
//#######################################################################\\

if(!file_exists("../conf/config.php")){
    die("<b>You must configure Panda Image Gallery! <br/><br/>Please review config.php.sample, configure as necessary,
	and rename config.php.sample to config.php. <br/><br/>See the README or installation notes for installation details.</b>");
}else{
	include_once '../conf/config.php';
}
include_once '../includes/class/loopvars.php';
$loopvars = new clsLoopVars();
$theme_settings = new clsLoopVars();
include_once '../includes/class/panda_dir.php';
include 'functions.php';

$pathInfo=$_SERVER['PATH_INFO'];
$pathInfo=substr($pathInfo, 1, strlen($pathInfo));
$pathInfo=explode("/", $pathInfo);

$g=$pathInfo[0]; //directory
$filename=$pathInfo[1]; //filename
$pos = (isset($_GET["pos"])) ? intval($_GET["pos"]): 0;

if(!is_movie($filename) && !is_image($filename)){
	die("NO HACK PROTECTION ACTIVATED");
	exit(0);	
}

if( $g!="" && $filename!="" && file_exists("../galleries/".$g."/".$filename) ){

	$completepath="../galleries/".$g."/".$filename;

	//get last mod time for the file
	$filemodtime=gmdate("D, d M Y H:i:s", filemtime ($completepath));

	//set content type of image
	$ext=strtolower(substr($filename, strrpos($filename, ".") +1));
	switch($ext){
	
	//supported image formats
		case 'jpg':
			header('Content-type: image/jpeg');
			break;

		case 'jpeg':
			header('Content-type: image/jpeg');
			break;

		case 'gif':
			header('Content-type: image/gif');
			break;

		case 'png':
			header('Content-type: image/png');
			break;

		case 'tiff':
			header('Content-type: image/tiff');
			break;

		case 'ico':
			header('Content-type: image/ico');
			break;

		case 'bmp':
			header('Content-type: image/bmp');
			break;

	//supported movie formats
		case 'avi':
			header('Content-type: video/x-msvideo');
			break;

		case 'flv':
			header('Content-type: video/x-flv');
			break;

		case 'mpeg':
			header('Content-type: video/mpeg');
			break;

		case 'mpg':
			header('Content-type: video/mpeg');
			break;

		case 'mov':
			header('Content-type: video/quicktime');
			break;

		case 'mp4':
			header('Content-type: video/mp4');
			break;

		case 'wmv':
			header('Content-type: video/x-msvideo');
			break;
			
		case 'ogg':
			header('Content-type: video/ogg');
			break;
			
		case 'webm':
			header('Content-type: video/webm');
			break;

		default:
			header('Content-type: image/jpeg');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize("unknown.jpg"));
			readfile("unknown.jpg");
			exit(0);
            break;
	}

	// fix for IE catching or PHP bug issue
	header("Pragma: public");
	header("Expires: 0"); // set expiration time
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	// browser must download file from server instead of cache

	// force download
	//	header("Content-Type: application/force-download");
	//	header("Content-Type: application/octet-stream");
	//	header("Content-Type: application/download");                
	//  use the Content-Disposition header to supply a recommended filename and
	//  force the browser to display the save dialog.
	//	$filename=str_replace(" ", "_", $filename);
	//	header("Content-Disposition: attachment; filename=".$filename.";");
	
	/*
	The Content-transfer-encoding header should be binary, since the file will be read
	directly from the disk and the raw bytes passed to the downloading computer.
	The Content-length header is useful to set for downloads. The browser will be able to
	show a progress meter as a file downloads. The content-lenght can be determines by
	filesize function returns the size of a file.
	*/
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($completepath));

	//implement proper caching
	if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) and (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == strtotime($filemodtime." GMT"))) {
		header("Last-Modified: ".$filemodtime." GMT", true, 304);
		exit(0);
	} else {
		header("Last-Modified: ".$filemodtime." GMT", true, 200);
		readfile($completepath);
	}

//sample seek
//  $fh = fopen($completepath,"rb");
//	fseek($fh, $pos);
//	fpassthru($fh);
//	fclose($fh);
            
	@readfile($completepath);

	exit(0);

}else{
	header('Content-type: image/jpeg');
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize("unknown.jpg"));
	readfile("unknown.jpg");
}

?>
