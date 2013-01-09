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

function recordExists($name)
{
	global $records;
	if (count($records) > 0)
	{
		foreach($records as $record)
		{
			if ($record->name == $name)
				return true;
		}
	}
	return false;
}

function getRecord($name)
{
	global $records;
	if (count($records) > 0)
	{
		foreach($records as $record)
		{
			if ($record->name == $name)
				return $record;
		}
	}
	return false;
}

function LoadRecords($item)
{
	global $records;
	global $path, $recordsDir;
	
	if (count($records) > 0)
		unlink($records);
	
	$fpath = $path.$recordsDir.$item;
	if (file_exists($fpath))
	{
		$file = fopen($fpath, "r");
		if (!empty($file))
		{
			$rank = 0;
			while (!feof($file))
			{
				$rank++;
				$line = fread($file);
				if ($line != "")
				{
					$lineExt = explode(" ", $line);
					$record = new Record($lineExt[0]);
					
					if ($record)
					{
						$record->time = $lineExt[1];
						$record->rank = $rank;
						
						$records[] = $record;
					}
				}
			}
		}
	}
}
?>