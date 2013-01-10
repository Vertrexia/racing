<?php
if (!defined(__ROOT__))
	return;

class Player
{
	//	personal info
    var $name;
	var $screen_name;
	
	//	server info
	var $cycle;
	var $records;
	var $queuer;
	
	var $isHuman;
	
	function __construct($name)
	{
		$this->name = $name;
	}
};
?>