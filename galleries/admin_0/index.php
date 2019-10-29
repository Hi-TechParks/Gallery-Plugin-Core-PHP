<?php
if(ISSET($includeFlag)){
     $gallery_options=array (
  'poster' => 'admin',
  'date_posted' => '10/16/2019@1571185502',
  'title' => 'What is Lorem Ipsum?',
  'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
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
