<?php 
//simple execution timer used for benchmarking execution times
class Timer {
	function getmicrotime() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}       
	function starttime() {
		$this->st = $this->getmicrotime();
	}
	function gettime() {
		$this->et = $this->getmicrotime();
		return round(($this->et - $this->st), 5);
	}
}
?>