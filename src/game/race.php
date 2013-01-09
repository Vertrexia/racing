<?php
//	class to store racing stats for the round, temporary
class Race
{
	var $name;
	var $time;
	
	var $first = false;	//	did this player cross the finish line, first?
};

//	syncher for racing
function racesync()
{
	global $players, $timer, $records, $races;
	global $countdown, $countdownMax, $smartTimer, $countdown_;
	
	$ais 	= 0;
	$humans = 0;
	$alive 	= 0;
	
	if (count($players) > 0)
	{
		foreach($players as $p)
		{
			if ($p)
			{
				if ($p->isHuman)
					$humans++;
				else
					$ais++;
				
				$cycle = $p->cycle;
				if ($cycle && $cycle->isAlive)
					$alive++;
			}
		}
	}
	else return;
	
	if (($humans > 0) && ($alive == 1) && ($ais == 0) && $countdown)
	{
		if ($smartTimer)
		{
			//	TODO: code lader
		}
		else
		{
			if ($countdown_ == -1)
				$countdown_ = $countdownMax + 1;
			
			$countdown_--;
			
			center("0xff7777".$countdown_."                    ");
		}
	}
}

//	player crossing the finish line
function crossLine($name)
{

}
?>