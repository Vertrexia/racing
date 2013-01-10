<?php
if (!defined(__ROOT__))
	return;

class Queuer
{
	var $name;
	var $amount;
	var $list;
	
	function __construct($name)
	{
		$this->name = $name;
	}
};
?>