<?php
//#######################################################################\\
//# Author: Christopher Schiffner                                       #\\
//# Filename: loopvars.php                                              #\\
//# Copyright: Christopher Schiffner, All Rights Reserved               #\\
//# Description: Image gallery software, view readme for more info.     #\\
//#                                                                     #\\
//# License: This software is free to use for personal applications.    #\\
//#          There is a small registration fee for commercial           #\\
//#          applications.  Please contact chris@schiffner.com if       #\\
//#          you wish to use this program on a commercial website.      #\\
//#######################################################################\\



//This class is designed to manage vars. Variables are actually set as elements in an array identified by a key passed at creation.
class clsLoopVars
{

	public $loopvars;
	private $read_only;

	//during construction unlock vars -- the user can lock all vars by calling the lock function
	function __construct() {
		$this->read_only=false;
	}

	//sets var contents with passed value
	public function set_var($key, $contents)
	{
		if(!$this->read_only){
			$this->loopvars[$key]=$contents;
		}else{
                        return -1;
		}
	}

	//returns var contents
	public function get_var($key)
	{
		if(isset($this->loopvars[$key])){
			return $this->loopvars[$key];
			exit();
		}else{
			return false;
			exit();
		}
		
		return false;
		exit();
	}

	//appends passed value to end of var
	public function append_after($key, $contents){
		if(!$this->read_only){
			$this->loopvars[$key].=$contents;
		}else{
			return -1;
		}
	}

	//appends passed value to beginning of var
	public function append_before($key, $contents){
		if(!$this->read_only){
			$this->loopvars[$key]=$contents.$this->loopvars[$key];
		}else{
			return -1;
		}
	}

	//destroys var
	public function destroy_var($key)
	{
		if(!$this->read_only){
			unset($this->loopvars[$key]);
		}else{
			return -1;
		}
	}

	//echos var contents
	public function echo_var($key){
		echo $this->loopvars[$key];
	}

	//compares var contents with passed value.
	public function compare($key, $search){
		if($this->loopvars[$key]==$search){
			return true;
			exit();
		}else{
			return false;
			exit();
		}

		return false;
	}

	//checks if var was set
	public function var_isset($key){
		if(isset($this->loopvars[$key])){
			return true;
			exit();
		}else{
			return false;
			exit();
		}

		return false;
	}

	//calling the below functions prevents any variable modifications
	public function lock(){
		$this->read_only=true;
	}

}
?>
