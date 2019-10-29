<?php
//define the level of error reporting
//error_reporting(-1);
//ini_set('error_reporting', E_ALL);
error_reporting(0);

// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//include core files
include_once '../conf/config.php';
include_once '../includes/class/loopvars.php';
include_once '../includes/version.php';


$loopvars = new clsLoopVars();
$theme_settings = new clsLoopVars();
include '../includes/functions.php';

$galleriesRoot="../galleries/";

//retrieve session id passed by uploader
$sessionid='';
if(isset($_GET['sessionid'])){
     $sessionid=$_GET['sessionid'];    
}else if(isset($_POST['sessionid'])){   
     $sessionid=$_POST['sessionid'];
}else{
	die("Session Hand-off Failed");	
}

//restart session using passed session id from uploader
session_name($core_settings['session_identifier']);
session_id($sessionid);
session_start();

//include theme file
$loopvars->set_var("theme_path", "../themes/".$core_settings['theme_name']);
include_once '../themes/'.$core_settings['theme_name'].'/theme.php';

//retrieve gallery name passed by uploader
$galleryname='';
if(isset($_GET['galleryname'])){
     $galleryname=$_GET['galleryname'];
}else{
	die("Gallery Not Found");	
}



//relative path to the gallery directory
$gallery_path=$galleriesRoot.$galleryname."/";

//make sure the user is logged in
if(!isset($_SESSION['s_userName'])) {
	header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php");
	exit();
}

//make sure the user has permission to upload to the specified gallery
if(!can_edit($galleryname, $_SESSION['s_userName']) && !can_collaborate($galleryname, $_SESSION['s_userName'])){
	header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php");
	exit();
}

// 5 minutes execution time
@set_time_limit(5 * 60);

//Temporary directory for uploaded file
$targetDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "plupload";
$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds

//Create target dir if it does not exist
if (!file_exists($targetDir)) {
	@mkdir($targetDir);
}

// Get the file name
if (isset($_REQUEST["name"])) {
	$fileName = $_REQUEST["name"];
} elseif (!empty($_FILES)) {
	$fileName = $_FILES["file"]["name"];
} else {
	$fileName = uniqid("file_");
}

//path to temporary file
$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

// Chunking might be enabled
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;


// Remove old temp files	
if ($cleanupTargetDir) {
	if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	}

	while (($file = readdir($dir)) !== false) {
		$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		// If temp file is current file proceed to the next
		if ($tmpfilePath == "{$filePath}.part") {
			continue;
		}

		// Remove temp file if it is older than the max age and is not the current file
		if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
			@unlink($tmpfilePath);
		}
	}
	closedir($dir);
}	


// Open temp file
if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

if (!empty($_FILES)) {
	if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	}

	// Read binary input stream and append it to temp file
	if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
} else {	
	if (!$in = @fopen("php://input", "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
}

//write uploaded file
while ($buff = fread($in, 4096)) {
	fwrite($out, $buff);
}

@fclose($out);
@fclose($in);

// Check if file has been uploaded
if (!$chunks || $chunk == $chunks - 1) {
	rename("{$filePath}.part", $filePath);
}

//make sure we want to process this file. If not we're being hacked. Die.
if(!is_image($fileName) && !is_movie($fileName)){
	//LOG OUR UPLOAD
	log_action("UPLOAD", "FAILED", $galleryname, "ATTEMPT TO UPLOAD UNSUPPORTED FILE - {$fileName}) {$_FILES['file']['size']} byte(s)");
	die("NO HACK PROTECTION ACTIVATED");
}

//sanitize our file name so it doesnt come back to bite us in the butt
$search= array("lowres_", "thm_");
$destroy = array("_", "_");
$fileName=str_replace($search, $destroy, $fileName);
$fileName=strip_tags($fileName);
$fileName=html_entity_decode($fileName);
$fileName=urldecode($fileName);

//if the file name exists do not overwrite it. Resolve the conflict gracefully.
$ext=substr($fileName, strrpos($fileName, ".") +1);
$tmpfilename=substr($fileName, 0, strrpos($fileName, "."));
if(file_exists($gallery_path.$_FILES['photos']['name'])){
	$x=1;

	while(file_exists($gallery_path.$fileName)){
		if(!file_exists($gallery_path.$tmpfilename."_".$x.$ext)){
			$fileName=$tmpfilename."_".$x.".".$ext;
		}
		$x++;
	}
}

//move file to the user's gallery	
rename($filePath, $gallery_path.$fileName);


//create image options file
if(file_exists($gallery_path.$fileName.".php")){
	include $gallery_path.$fileName.".php";
}else{
	$image_options=array(
		"poster" => $_SESSION['s_userName'],
		"caption" => ""
	);
}
         
//we made it here so its time to modify - dont forget the lock!
$pf=fopen($gallery_path.$fileName.".php", 'w+');      
flock($pf, LOCK_EX);   
fwrite($pf, "<?php\n");
fwrite($pf, "\$image_options=".var_export($image_options, TRUE).";\n");
fwrite($pf, "?>\n"); 
flock($pf, LOCK_UN);
fclose($pf);

//we've uploaded a file--let's create the thumbnail image
create_thumbnail_image($galleryname, $fileName);

$imagedimensions = getimagesize($galleriesRoot.$galleryname."/".$fileName);
if( (($imagedimensions[0] > $theme_settings->get_var("image_display_width")) && ($imagedimensions[0] > $imagedimensions[1])) 
||  (($imagedimensions[1] > $theme_settings->get_var("image_display_height")) && ($imagedimensions[1] > $imagedimensions[0])) ){
	create_lowres_image($galleryname, $fileName);
}

//LOG OUR UPLOAD
log_action("UPLOAD", ($error ? 'FAILED' : 'SUCCESS'), $galleryname, "{$tmpfilename}.{$ext} (stored as {$fileName}) {$_FILES['file']['size']} byte(s)");

// Return Success JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
