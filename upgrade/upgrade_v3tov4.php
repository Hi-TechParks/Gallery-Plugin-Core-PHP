<?php 

include_once '../includes/class/panda_dir.php';

$galleriesRoot="../galleries/";

$includeFlag=1;

//Get all galleries
$clsDirListingDefault = new clsDirListing($galleriesRoot, 2);
$clsDirListingDefault->sortOrder($galleryListingSortOrder);
$clsDirListingDefault->excludes(array("logs"));
$galleries=$clsDirListingDefault->getListing();
$clsDirListingDefault="";

echo "<strong>Upgrading galleries...</strong><br/><br/>";

$i=0;
foreach($galleries as $gallery){
	if(file_exists($galleriesRoot.$gallery[0]."/index.php")){
		include $galleriesRoot.$gallery[0]."/index.php";
	
	
		if(isset($gallery_options)){
			$status="Upgrade not required";
		}else{
			if(isset($galleryInfo)){
				if(isset($galleryInfo[0])){
					update_gallery_option($gallery[0], 'poster', $galleryInfo[0]);
				}
				if(isset($galleryInfo[1])){
					update_gallery_option($gallery[0], 'date_posted', $galleryInfo[1]);
				}
				if(isset($galleryInfo[2])){
					update_gallery_option($gallery[0], 'title', $galleryInfo[2]);
				}
				if(isset($galleryInfo[3])){
					update_gallery_option($gallery[0], 'description', $galleryInfo[3]);
				}
				if(isset($galleryInfo[4])){
					update_gallery_option($gallery[0], 'sort_order', $galleryInfo[4]);
				}
				if(isset($galleryInfo[5])){
					update_gallery_option($gallery[0], 'download_policy', $galleryInfo[5]);
				}
				if(isset($galleryInfo[6])){
					update_gallery_option($gallery[0], 'copyright', $galleryInfo[6]);
				}
				if(isset($galleryInfo[7])){
					update_gallery_option($gallery[0], 'conceal_paths', $galleryInfo[7]);
				}
				
				$status="Upgraded";
			}else{
				$status="Error: gallery info not found. 'index.php' corrupt.";
			}
		}
		
		unset($galleryInfo);
	}
	
	$i++;
	echo $i.") ".$gallery[0]."...  <strong>".$status."</strong><br/>";
	
	
	$clsDirListingDefault = new clsDirListing($galleriesRoot.$gallery[0], 0);
	$clsDirListingDefault->sortOrder($galleryViewSortOrder);
	$clsDirListingDefault->extensionIncludes(array("jpg", "jpeg", "gif", "png", "ogg", "webm", "mp4"));
	$clsDirListingDefault->prefixExcludes(array("thm_", "lowres_"));
	$images=$clsDirListingDefault->getListing();
	$clsDirListingDefault="";
	
	foreach ($images as $image){
		if(file_exists($galleriesRoot.$gallery[0]."/".$image[0].".php")){
			include $galleriesRoot.$gallery[0]."/".$image[0].".php";
		
			if(isset($image_options)){
				$status="Upgrade not required";
			}else{
				if(isset($imageDetails)){
					if(isset($imageDetails[0])){
						update_image_option($gallery[0], $image[0], 'caption', $imageDetails[0]);
					}
					
					$status="Upgraded";
				}else{
					$status="Error: image info not found. '".$image[0].".php' corrupt.";
				}
			}
			
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$image[0]."...  <strong>".$status."</strong><br/>";
		}
		
		unset($imageDetails);
	}
	echo "<br/><br/>";
}

echo "<br/><strong>Gallery upgrade complete!</strong><br/><br/>";


echo "<br/><strong>Upgrading user data...</strong>";

	include "../conf/users/users.inc.php";

	if(isset($userarray)){
		$status="Upgrade not required";
	}else{
		if(isset($users)){
			
			$userarray=array();
			
			foreach ($users as $user) {
				$users_array[$user[0]]=array( 'username' => $user[0],
											  'password' => crypt($user[1]),
											  'email_address' => $user[2],
											  'firstname' => $user[3],
											  'lastname' => $user[4],
											  'userlevel' => $user[5],
											 );
			}
			
			$filename="../conf/users/users.inc.php";
						         
			//we made it here so its time to modify - dont forget the lock!
			$pf=fopen($filename, 'w+');      
			flock($pf, LOCK_EX);   
			fwrite($pf, "<?php\n");
			fwrite($pf, "\$users_array=".var_export($users_array, TRUE).";\n");
			fwrite($pf, "?>\n"); 
			flock($pf, LOCK_UN);
			fclose($pf);
			
			$status="Upgraded";
		}else{
			$status="Upgrade not required.";
		}
	}

echo "<br/><strong>User data upgrade status: </strong>".$status."<br/><br/>";


/* writes gallery option to gallery data file */
function update_gallery_option($gallery_name, $key, $data){

	global $galleriesRoot;
	
	global $includeFlag;
	
	$filename=$galleriesRoot.$gallery_name."/index.php";
	
	if(file_exists($filename)){
		ob_start();
		include $filename;
		ob_end_clean();
	}else{
		$gallery_options=array(
			"poster" => "",
			"date_posted" => currentDate()."@".time(),
			"title" => "",
			"description" => "",
			"sort_order" => "dateDESC",
			"download_policy" => 0,
			"copyright" => "",
			"conceal_paths" => 0,
		);
	}
	
	if(!isset($gallery_options) || !is_array($gallery_options)){
		$gallery_options=array(
			"poster" => "",
			"date_posted" => currentDate()."@".time(),
			"title" => "",
			"description" => "",
			"sort_order" => "dateDESC",
			"download_policy" => 0,
			"copyright" => "",
			"conceal_paths" => 0,
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

/* writes image option to image option file */
function update_image_option($gallery_name, $file, $key, $data){

	global $galleriesRoot;

	$filename=$galleriesRoot.$gallery_name."/".$file.".php";
	
	if(file_exists($filename)){
		include $filename;
	}else{
		$image_options=array(
			"caption" => "",
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

function currentDate(){ 
	//this function produces the date in the format of 4/3/2003
	//the date is then returned
	$swap = getdate();
	$today = "{$swap['mon']}/{$swap['mday']}/{$swap['year']}";
	return $today;
} 
?>