<?php
if (!defined(__ROOT__))
	return;

class Cycle
{
	var $player;
	
	var $spawn_pos;
	var $spawn_dir;
	
    var $isAlive = false;
	var $deathTime = 0;
	
	var $chances = 0;
};

//	functions are listed below
function cycleCreated($name, $x, $y, $xdir, $ydir)
{
	global $chances;
	$player = getPlayer($name);
	
	if ($player)
	{
		$cycle = new Cycle;
		
		if ($cycle)
		{
			$cycle->isAlive = true;
			$cycle->spawn_pos = new Coord($x, $y);
			$cycle->spawn_dir = new Coord($xdir, $ydir);
			$cycle->chances = $chances;
			$cycle->player = $player->name;
			
			$player->cycle = $cycle;
		}
	}
}

function cycleDestroyed($name)
{
	global $chances, $timer;
	$player = getPlayer($name);
	
	if ($player)
	{
		$cycle = $player->cycle;
		
		if ($cycle)
		{
			$cycle->deathTime = $timer->GameTimer();
			$cycle->isAlive = false;
			
			//	check if chances are enabled
			//	also check if players have enough chances to be respawned
			if (($chances > 0) && ($cycle->chances > 0))
				respawnCycle($cycle);
		}		
	}
}
function respawnCycle($cycle)
{
	global $chances;
	
	if (($chances > 0) && ($cycle->chances > 0))
	{
		echo "RESPAWN_PLAYER ".$player->name." ".$cycle->spawn_pos->x." ".$cycle->spawn_pos->y." ".$cycle->spawn_dir->x." ".$cycle->spawn_dir->y."\n";
		echo "CUSTOM_PLAYER_MESSAGE ".$player->name." racing_respawn_limit ".$cycle->chances."\n";
		
		$cycle->isAlive = true;
		$cycle->chances--;
	}
}
?>