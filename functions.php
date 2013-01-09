<?php
//	engine
require("./src/engine/player.php");
require("./src/engine/timer.php");
require("./src/engine/cycle.php");

//	tools
require("./src/tools/coord.php");

//	load settings only once
require_once("settings.php");

//	declare global variables from settings
global $players, $timer, $records;
global $path, $file;


//	functions listed from below

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

function getRecord($name)
{
	global $records;
	if (count($records) > 0)
	{
		foreach ($records as $r)
		{
			if ($r->name == $name)
				return $r;
		}
	}
	
	return false;
}

function playerEntered($name, $human)
{
	global $players;
	if (!playerExists($name))
	{
		$players[] = new Player($name);
		$player = getPlayer($name);
		if ($player)
		{
			$player->isHuman = $human;
			$record = getRecord($name);
			if ($record && $human)
				$player->record = $record;
		}
	}
	else
	{
		$player = getPlayer($name);
		if ($player)
		{
			$player->isHuman = $human;
			$record = getRecord($name);
			if ($record && $human)
				$player->record = $record;			
		}
	}
}

function playerLeft($name)
{
	global $players;
	if ($players > 0)
	{
		foreach ($players as $p => $key)
		{
			if ($p->name == $name)
			{
				unlink($players[$key]);
			}
		}
	}
}
?>