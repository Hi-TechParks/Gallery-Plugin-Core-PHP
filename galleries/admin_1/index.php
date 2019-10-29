<?php
if(ISSET($includeFlag)){
     $gallery_options=array (
  'poster' => 'admin',
  'date_posted' => '10/16/2019@1571186035',
  'title' => 'Where does it come from?',
  'description' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
  'sort_order' => 'dateDESC',
  'download_policy' => '0',
  'copyright' => '',
  'conceal_paths' => '0',
  'collaborators' => 
  array (
  ),
);
}else{ 
     echo "<html><head><title>Access Denied</title></head><body>Access Denied</body></html>"; 
}?>
