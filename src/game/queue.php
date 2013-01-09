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


//	functions are listed below
function queuerExists($name)
{
	global $queuers;
	if (count($queuers) > 0)
	{
		foreach($queuers as $queuer)
		{
			if ($queuer->name == $name)
				return true;
		}
	}
	return false;
}

function getQueuer($name)
{
	global $queuers;
	if (count($queuers) > 0)
	{
		foreach($queuers as $queuer)
		{
			if ($queuer->name == $name)
				return $queuer;
		}
	}
	return false;
}

//	load the data onto the server
function LoadQueuers()
{
	global $queuers;
	global $path, $recordsDir, $queueFile;
	
	//	loading queuers
	$queFilePath = $path.$queueFile;
	if (file_exists($queFilePath))
	{
		$file = fopen($queFilePath, "r");
		if (!empty($file))
		{
			while (!feof($file))
			{
				$line = fread($fopen);
				if ($line != "")
				{
					$lineExt = explode(" ", $line);
					$queuer = new Queuer($lineExt[0]);
					
					if ($queuer)
					{
						$queuer->amount = $lineExt[1];
						
						$queuers[] = $queuer;
					}
				}
			}
			
			fclose($file);
		}
	}
}
?>