<?php
//#######################################################################\\
//# Author: Christopher Schiffner                                       #\\
//# Filename: panda_dir.php                                             #\\
//# Copyright: Christopher Schiffner, All Rights Reserved               #\\
//# Description: Image gallery software, view readme for more info.     #\\
//#                                                                     #\\
//# License: This software is free to use for personal applications.    #\\
//#          There is a small registration fee for commercial           #\\
//#          applications.  Please contact chris@schiffner.com if       #\\
//#          you wish to use this program on a commercial website.      #\\
//#######################################################################\\

class clsDirListing
{

	// Class Use
	//
	// $obj = new clsDirListing($directory_path, $listing_type)
	//
	// "directory_path" - REQUIRED
	// This string sets the path to get a directory listing on
	//
	// Possible Values
	// Any valid path to a gallery directory
	//
	// "listing_type" - REQUIRED
	// This integer denotes the type of directory listing we are doing.
	//

	// 0 = no directories, files only. $obj->extensionIncludes are REQUIRED. $obj->prefixExcludes and $obj->excludes are honored.
	// 1 = directories only, $obj->prefixExcludes and $obj->excludes are honored.
	// 2 = directories only, retrieve info from index.php inside directory for author and title sort. $obj->prefixExcludes and $obj->excludes are honored.
	// 3 = directories only, for specific users, retrieve info from index.php inside gallery folder. aryPreInclusions is REQUIRED. $obj->prefixExcludes and $obj->excludes are honored.
	// 4 = directories and files - $obj->prefixExcludes and $obj->excludes are honered
	// 5 = files only, $obj->prefixIncludes and $obj->extensionIncludes are REQUIRED. $obj->excludes are honored.


	// $obj->sortOrder(SORT_METHOD) - OPTIONAL
	// This string denotes the sorting to perform on a listing. If no value is entered sorting will be
	// according to dir() output
	//
	// Possible values for SORT_METHOD
	// dateDESC                - date uploaded descending
	// dateASC                 - date uploaded ascending
	// dateModifiedDESC        - last modifiied date descending
	// dateModifiedASC         - last modified date ascending
	// titleAlphabetical       - listing/file Name
	// author                  - the authors username (intListType 2 only)


	// $obj->extensionIncludes(EXTENSIONS_TO_INCLUDE) - REQUIRED for "intListType 0 and 5"
	// This array sets the acceptable file extensions
	//
	// Possible Values for EXTENSIONS_TO_INCLUDE
	// any valid file extension


	// $obj->prefixIncludes(PREFIXES_TO_INCLUDE) - REQUIRED for "intListType 3"
	// This array sets the acceptable file extensions
	//
	// Possible Values for PREFIXES_TO_INCLUDE
	// any valid panda prefix


	// $obj->prefixExcludes(PREFIXES_TO_EXCLUDE) - OPTIONAL
	// This array sets files/path prefix that should not be included in the listing.
	//
	// Possible Values for PREFIXES_TO_EXCLUDE
	// any valid panda prefix


	// $obj->excludes(EXCLUSIONS) "aryExclusions" - OPTIONAL
	// This array sets files/paths that should not be included in the listing.
	//
	// Possible Values
	// any


	// $obj->includes(TERMS_TO_INCLUDE) - REQUIRED for "intListType 6"
	// This array sets the acceptable file extensions
	// 
	// Possible Values for TERMS_TO_INCLUDE
	// any

	// ######################################################################################################### \\

	private $strDirectoryPath="";
	private $intListType="";
	private $strSortOrder="";
	private $aryPreInclusions=array();
	private $aryExtInclusions=array();
	private $aryPreExclusions=array();
	private $aryExclusions=array();
	private $strInclusions="";

	function __construct($directory_path, $listing_type) {
		$this->strDirectoryPath = $directory_path;
		$this->intListType = $listing_type;
	}


	public function sortOrder($sort_order)
	{
		// This string denotes the sorting to perform on a listing. If no value is entered sorting will be
		// according to dir() output
		//
		// Possible values
		// dateDESC                - date uploaded descending
		// dateASC                 - date uploaded ascending
		// dateModifiedDESC        - last modifiied date descending
		// dateModifiedASC         - last modified date ascending
		// titleAlphabetical       - listing/file Name
		// author                  - the authors username (intListType 2 only)

		$this->strSortOrder=$sort_order;
	}


	public function prefixIncludes($prefix_inclusion)
	{
		// This array sets the acceptable panda gallery prefixes
		//
		// Possible Values
		// any valid panda prefix

		$this->aryPreInclusions=$prefix_inclusion;
	}


	public function extensionIncludes($extension_inclusion)
	{
		// This array sets the acceptable file extensions
		//
		// Possible Values
		// any valid extension

		$this->aryExtInclusions=$extension_inclusion;
	}


	public function prefixExcludes($prefix_exclusion)
	{
		// This array sets files/path prefixes that should not be included in the listing.
		//
		// Possible Values
		// any valid panda prefix

		$this->aryPreExclusions=$prefix_exclusion;
	}


	public function excludes($exclusion)
	{
		// This array sets files/paths that should not be included in the listing.
		//
		// Possible Values
		// any

		$this->aryExclusions=$exclusion;
	}

        public function includes($inclusion)
        {
			// This string sets the terms that are acceptable for iunclusion when performing a search.
			//
			// Possible Values
			// any
        
			$this->strInclusions=$inclusion;
        }

	public function getListing()
	{
		$dirOutput=$this->doDirListing();
		$this->sortArray($dirOutput, $this->strSortOrder);
		return $dirOutput;
	}


	private function doDirListing()
	{

		$dirOutput=array();

		//get the directory list, each image gallery is in its own directory
		$g_Listing = dir($this->strDirectoryPath);
		while($entry = $g_Listing->read()) {
			if ($entry != "." && $entry != "..") {

				if( ($this->intListType==0 && (!is_dir($this->strDirectoryPath."/".$entry)))
				|| ($this->intListType==5 && (!is_dir($this->strDirectoryPath."/".$entry)))
				|| ($this->intListType==1 && (is_dir($this->strDirectoryPath."/".$entry)))
				|| ($this->intListType==2 && (is_dir($this->strDirectoryPath."/".$entry)))
				|| ($this->intListType==3 && (is_dir($this->strDirectoryPath."/".$entry)))
				|| ($this->intListType==4) 
				|| ($this->intListType==6) ){
	
					$extpos = strrpos($entry, '.');
					$ext = strtolower(substr($entry, $extpos+1));
				    $prepos = strpos($entry, '_');
					$prefix = strtolower(substr($entry, 0, $prepos+1));
	
				    //This is hard to follow but its necessary to make sure the correct listinggs are obtained.
				    if ( (in_array($ext, $this->aryExtInclusions) && !in_array($prefix, $this->aryPreExclusions) && !in_array($entry, $this->aryExclusions) && $this->intListType==0)
					|| (!in_array($prefix, $this->aryPreExclusions) && !in_array($entry, $this->aryExclusions) && $this->intListType==1)
					|| (!in_array($prefix, $this->aryPreExclusions) && !in_array($entry, $this->aryExclusions) && $this->intListType==2)
					|| (!in_array($prefix, $this->aryPreExclusions) && !in_array($entry, $this->aryExclusions) && in_array($prefix, $this->aryPreInclusions) && $this->intListType==3)
					|| (!in_array($prefix, $this->aryPreExclusions) && !in_array($entry, $this->aryExclusions) && $this->intListType==4)
					|| (in_array($ext, $this->aryExtInclusions) && in_array($prefix, $this->aryPreInclusions) && !in_array($entry, $this->aryExclusions) && $this->intListType==5) )
					{
	
						if (file_exists($this->strDirectoryPath.$entry."/index.php") && ($this->intListType==2 || $this->intListType==3)) {
						    $includeFlag=1;
		
						    ob_start();
							include_once $this->strDirectoryPath.$entry."/index.php";
						    ob_end_clean();
						}
		
						/* This is where we setup image sorting. Possible values include
						dateDESC                - date uploaded descending
						dateASC                 - date uploaded ascending
						dateModifiedDESC	- last modifiied date descending
						dateModifiedASC		- last modified date ascending
						titleAlphabetical       - listing/file name
						author			- author username
						*/
						if($this->strSortOrder=="dateDESC" || $this->strSortOrder=="dateASC"){
		
							if($this->intListType==2 || $this->intListType==3){
								if(is_array($gallery_options)){
									//retrieve date from gallery info, split and create timestamp for sorting
									if(stristr($gallery_options['date_posted'], "@")){
										$sortData=explode("@", $gallery_options['date_posted']);
										$sortData=$sortData[1];
									}else{
										$fileTime=filemtime($this->strDirectoryPath.$entry."/index.php");
										$sortData=$gallery_options['date_posted'];
										$sortData=explode("/",$sortData);
										$sortData=mktime(date("G", $fileTime), date("i", $fileTime), date("s", $fileTime),$sortData[0],$sortData[1],$sortData[2]);
									}
								}else{
									//if this is a not a listing with VALID gallery_options take last modified time
									$sortData=filemtime($this->strDirectoryPath."/".$entry);
								}
							}else{
		
								//if this is a not a listing with gallery_options take last modified time
								$sortData=filemtime($this->strDirectoryPath."/".$entry);
		
							}
		
						}else if($this->strSortOrder=="dateModifiedDESC" || $this->strSortOrder=="dateModifiedASC"){
		
							//retrieve last modified time
							$sortData=filemtime($this->strDirectoryPath."/".$entry);
		
						}else if($this->strSortOrder=="titleAlphabetical"){
		 				    //if this is a listing with gallery info retrieve the the title from gallery_options for sorting
						    if($this->intListType==2 || $this->intListType==3){
		
								if(is_array($gallery_options)){
									$sortData=$gallery_options['title'];
								}else{
									$sortData=$entry;
								}	
		
							}else{
								$sortData=$entry;
							}
		
						}else if($this->strSortOrder=="author"){
						    //if this is a listing with gallery info retrieve the author from gallery_options for sorting
						    if($this->intListType==2 || $this->intListType==3){
		
								if(is_array($gallery_options)){
									$sortData=$gallery_options['poster'];
								}else{
									$sortData=$entry;
								}
		
							}else{
								$sortData=$entry;
							}
		
						}else{
		
						    //nothing met our sort criteria so make the sort criteria a large number to sort to last
							$sortData=999999999999999999999999999999;
		
						}
		
						//add info to dirOutput for user
						$dirOutput[]=array($entry, $sortData);
	
					} else if ($this->intListType==6 && is_dir($this->strDirectoryPath.$entry) && trim($this->strInclusions)!=""){
	
					    //This is a search operation. Retrieve gallery information and then use the same sorting as found above
					    //We'll be searching an array with an array to obtain the results which contain ALL search terms
		
						if (file_exists($this->strDirectoryPath.$entry."/index.php")){
							$includeFlag=1;
		                                                
							ob_start();
							include_once $this->strDirectoryPath.$entry."/index.php";
							ob_end_clean();
						}
						
						//were searching so sanitize the string by removing punctuation
						$strSearchString=strtolower(trim($gallery_options['poster'])." ".trim($gallery_options['title'])." ".trim($gallery_options['description']));
						$strSearchString=preg_replace('/[^a-zA-Z0-9-\s]/', ' ', $strSearchString);
		
						if( $this->array_in_array(explode(" ", strtolower($this->strInclusions)), explode(" ", $strSearchString)) && !in_array($entry, $this->aryExclusions) ){
							/* This is where we setup image sorting. Possible values include
							dateDESC                - date uploaded descending
							dateASC                 - date uploaded ascending
							dateModifiedDESC        - last modifiied date descending
							dateModifiedASC         - last modified date ascending
							titleAlphabetical       - listing/file name
							author                  - author username
							*/
							if($this->strSortOrder=="dateDESC" || $this->strSortOrder=="dateASC"){
								if($this->intListType==2 || $this->intListType==3){
									if(is_array($gallery_options)){
										$sortData=$gallery_options['date_posted'];
										$sortData=explode("/",$sortData);
										$sortData=mktime(0,0,0,$sortData[0],$sortData[1],$sortData[2]);
									}else{
										$sortData=filemtime($this->strDirectoryPath."/".$entry);
									}
								}else{
									$sortData=filemtime($this->strDirectoryPath."/".$entry);
								}
							}else if($this->strSortOrder=="dateModifiedDESC" || $this->strSortOrder=="dateModifiedASC"){
								$sortData=filemtime($this->strDirectoryPath."/".$entry);
							}else if($this->strSortOrder=="titleAlphabetical"){
								if($this->intListType==2 || $this->intListType==3){
									if(is_array($gallery_options)){
										$sortData=$gallery_options['title'];
									}else{
										$sortData=$entry;
									} 
								}else{
									$sortData=$entry;
								}
							}else if($galleryListingSortOrder=="author"){
								if($this->intListType==2 || $this->intListType==3){
									if(is_array($gallery_options)){
										$sortData=$gallery_options['poster'];
									}else{  
										$sortData=$entry;  
									}
								}else{
									$sortData=$entry;
								}
							}else{  
								$sortData=999999999999999999999999999999;
							}
		
							$dirOutput[]=array($entry, $sortData);
						}
					}
		        }
			}
	    }
	    $g_Listing->close();
		return $dirOutput;
	}

	//simple private function to select apprpriate sort
	private function sortArray(&$aryInput, $sortCriteria)
	{
	    if(count($aryInput)>0){
	        switch($sortCriteria){

	            case "dateDESC":
					usort($aryInput, array($this, "numericSortDESC"));
					break;

			    case "dateModifiedDESC":
	                usort($aryInput, array($this, "numericSortDESC"));
	            	break;

	            case "dateASC":
					usort($aryInput, array($this, "numericSortASC"));
					break;

			    case "dateModifiedASC":
	                usort($aryInput, array($this, "numericSortASC"));
	            	break;

	            case "author":
	                usort($aryInput, array($this, "alphabeticalSort"));
	            	break;

	            case "titleAlphabetical":
					if($this->intListType==0 || $this->intListType==5){
						$this->natCaseSortNames($aryInput);
					}else{
					usort($aryInput, array($this, "alphabeticalSort"));
					}
				break;
	        }
	    }
	    $aryInput=$aryInput;
  	}

	private static function numericSortDESC($a, $b)
	{
	    return ($a[1] < $b[1]) ? 1 : 0;
	}

	private static function numericSortASC($a, $b)
	{
	    return ($a[1] < $b[1]) ? 0 : 1;
	}

	private static function alphabeticalSort($a,$b){
	    if(ord(substr(strtolower($a[1]),0,1)) == ord(substr(strtolower($b[1]),0,1))) return 0;
	    return (ord(substr(strtolower($a[1]),0,1)) < ord(substr(strtolower($b[1]),0,1))) ? -1 : 1;
	}

	private static function natCaseSortNames(&$aryInput) {
		//this function will sort an array without maintaining keys
		$aryOutput='';
		$aryTemp='';

		foreach ($aryInput as $value) { //natcasesort will sort our array but it also preserves key associations
		      $aryTemp[] = $value[0];           //this way we step through a create a fresh array with no associations
		}

		if(is_array($aryTemp))
		     natcasesort($aryTemp);

		if(!empty($aryTemp)){
		    foreach ($aryTemp as $value) { //natcasesort will sort our array but it also preserves key associations
		      $aryOutput[]=array($value, $value);           //this way we step through a create a fresh array with no associations
		    }
		}
		$aryInput = $aryOutput;           //return array
	}

	function array_in_array($needles, $haystack) {
	    $matched_needles = 0;
	    foreach ($needles as $needle) {
	        if ( in_array($needle, $haystack) ) $matched_needles++;
	    }
   
	    return ($matched_needles == count($needles)) ? true : false;
	} 

	function __destruct() {
		$this->strDirectoryPath = "";
		$this->intListType = "";
		$this->strSortOrder = "";
		$this->aryPreInclusions = "";
		$this->aryExtInclusions = "";
		$this->aryPreExclusions = "";
	}
}

?>
