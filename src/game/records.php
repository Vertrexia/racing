<?php
if (!defined(__ROOT__))
	return;

class Record
{
	var $name;		//	the record owner
	var $time;		//	player's best
	var $rank;		//	current rank in the item
	
	function __construct($name)
	{
		$this->name = $name;
	}
};
?>