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

function playerExists($name)
{
	global $players;
	if (count($players) > 0)
	{
		foreach($players as $p)
		{
			if ($p->name == $name)
				return true;
		}
	}
	return false;
}

function getPlayer($name)
{
	global $players;
	if (count($players) > 0)
	{
		foreach($players as $p)
		{
			if ($p->name == $name)
				return $p;
		}
	}
	return false;
}


function playerEntered($name, $screenName $human)
{
	global $players;
	if (!playerExists($name))
	{
		$player = new Player($name);
		
		if ($player)
		{
			$player->isHuman = $human;
			$player->screen_name = $screenName;
			
			$record = getRecord($name);
			if ($record && $human)
				$player->record = $record;
				
			$queuer = getQueuer($name);
			if ($queuer && $human)
				$player->queuer = $queuer;
			
			$players[] = $player;
		}
	}
	else
	{
		$player = getPlayer($name);
		if ($player)
		{
			$player->isHuman = $human;
			$player->screen_name = $screenName;
			
			$record = getRecord($name);
			if ($record && $human)
				$player->record = $record;
				
			$queuer = getQueuer($name);
			if ($queuer && $human)
				$player->queuer = $queuer;
		}
	}
}
function playerRenamed($old, $new, $screenName)
{
	global $players;
	$player = getPlayer($old);
	
	if ($player)
	{
		$player->name = $new;
		$player->screen_name = $screenName;
		
		//	fetch records and queues related to the new name
		//	this is due to people hacking into other accounts
		
		$record = getRecord($name);
		if ($record && $human)
			$player->record = $record;
		else
			$player->record = null;
			
		$queuer = getQueuer($name);
		if ($queuer && $human)
			$player->queuer = $queuer;
		else
			$player->queuer = null;
	}
}
function playerLeft($name)
{
	global $players;
	if ($players > 0)
	{
		foreach ($players as $key => $p)
		{
			if ($p->name == $name)
			{
				//	remove player from the list
				unlink($players[$key]);
				break;
			}
		}
	}
}
?>