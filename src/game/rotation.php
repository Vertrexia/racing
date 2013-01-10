<?php
<?php
if (!defined(__ROOT__))
	return;

class Rotation
{
	var $items;
	var $itemKey = 0;
	
	function Rotate()
	{
		if ($itemKey >= count($items))
			$itemKey = 0;
		else
		{
			$item = $items[$itemKey];
			echo "INCLIDE ".$item."\n";
			
			LoadRecords($item);
		}
	}
};
?>