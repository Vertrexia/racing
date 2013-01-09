<?php
if (!defined(__ROOT__))
	return;

function roundBegan()
{
	global $timer;
	$timer = new Timer;
	$timer->Start();
}

function roundEnded()
{
	global $timer;
	global $zonesCollapseAfterFinish, $zones;
	
	//	although the timer is stopped, game timer is still running until next round/match starts.
	$timer->Stop();
	
	if ($zonesCollapseAfterFinish)
	{
		for($i = 0; $i < count($zones); $i++)
		{
			$zone = $zones[$i];
			if ($zone)
			{
				echo "COLLAPSE_ZONE ".$zone->name."\n";
			}
			unlink($zones[$i]);
			$i--;
		}
	}
}

function center($message)
{
	echo "CENTER_MESSAGE ".$message."\n";
}
?>