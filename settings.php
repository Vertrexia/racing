<?php
if (!defined(__ROOT__))
	return;

require(__ROOT__."/src/engine/cycle.php");
require(__ROOT__."/src/engine/player.php");
require(__ROOT__."/src/engine/timer.php");

require(__ROOT__."/src/tools/coord.php");
require(__ROOT__."/src/tools/string.php");

require(__ROOT__."/src/game/queue.php");
require(__ROOT__."/src/game/race.php");
require(__ROOT__."/src/game/records.php");
require(__ROOT__."/src/game/rotation.php");
require(__ROOT__."/src/game/zones.php");

class Base
{
	//	class decleration variables
	const $players 		= null;		//	holds the list of players in server
	const $timer		= null;		//	timer of the game
	const $records		= null;		//	holds the list of records of players
	const $queuers		= null;		//	queuers and their list
	const $rotation 	= null;		//	for rotation
	const $zones		= null;		//	holds the list of spawned zones
	const $races		= null;		//	keeps track of current race stuff
	const $paths		= null;		//	stores the paths

	//	directory of records
	const $path 		= "/path/to/base/";
	const $recordsDir 	= "records";
	const $queueFile 	= "playerqueues.txt";

	//	for respawing players
	const $chances = 0;

	//	queueing values
	const $queue_increase_time = 0;	//	should the queues each player increase depending on the time they play for in the server
	const $queue_give = 4;			//	the amount of queues each player gets
	const $queue_accesslevel = 2;		//	required access level to activate queue

	//	rotation items to load
	const $rotations = array("config1.cfg", "config2.cfg");
	const $rotation_type = 1;			//	0-no rotation, 1-per round, 2-per match
	const $rotation_load = 0;			//	0-INCLUDE, 1-SINCLUDE, 3-RINCLUDE
	const $rotation_current = "";		//	usually contains the currently loaded rotation item

	//	zone settings
	const $zonesCollapseAfterFinish = false;

	//	race settings
	const $countdown = true;
	const $countdownMax = 60;
	const $smartTimer = false;
	const $countdown_ = -1;
	
	function center($message)
	{
		echo "CENTER_MESSAGE ".$message."\n";
	}
	
	function con($message)
	{
		echo "CONSOLE_MESSAGE ".$message."\n";
	}	
	
	function pm($player, $message)
	{
		echo "PLAYER_MESSAGE ".$message."\n";
	}
	
	function cpm($player, $langauge_command, $params)
	{
		echo "CUSTOM_PLAYER_MESSAGE ".$player." ".$langauge_command." ".$params."\n";
	}	
	
	//	functions are listed below
	function cycleCreated($name, $x, $y, $xdir, $ydir)
	{		
		$player = $this->getPlayer($name);
		
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
		$player = $this->getPlayer($name);
		
		if ($player)
		{
			$cycle = $player->cycle;
			
			if ($cycle)
			{
				$cycle->deathTime = $this->timer->GameTimer();
				$cycle->isAlive = false;
				
				//	check if chances are enabled
				//	also check if players have enough chances to be respawned
				if (($this->chances > 0) && ($cycle->chances > 0))
					respawnCycle($cycle);
			}		
		}
	}
	function respawnCycle($cycle)
	{		
		if (($this->chances > 0) && ($cycle->chances > 0))
		{
			echo "RESPAWN_PLAYER ".$player->name." ".$cycle->spawn_pos->x." ".$cycle->spawn_pos->y." ".$cycle->spawn_dir->x." ".$cycle->spawn_dir->y."\n";
			$this->cpm($player->name,"racing_respawn_limit", $cycle->chances);
			
			$cycle->isAlive = true;
			$cycle->chances--;
		}
	}
	
	function player_gridpos($name, $x, $y, $xdir, $ydir)
	{
		$player = $this->getPlayer($name);
		if ($player)
		{
			$cycle = $player;
			if ($cycle)
			{
				if (!$cycle->pos && !$cycle->dir)
				{
					$cycle->pos = Coord($x, $y);
				}				
			}
		}
	}
	
	function playerExists($name)
	{
		if (count($this->players) > 0)
		{
			foreach($this->players as $p)
			{
				if ($p->name == $name)
					return true;
			}
		}
		return false;
	}

	function getPlayer($name)
	{
		if (count($this->players) > 0)
		{
			foreach($this->players as $p)
			{
				if ($p->name == $name)
					return $p;
			}
		}
		return false;
	}

	function playerEntered($name, $screenName $human)
	{
		if (!playerExists($name))
		{
			$player = new Player($name);
			
			if ($player)
			{
				$player->isHuman = $human;
				$player->screen_name = $screenName;
				
				$record = $this->getRecord($name);
				if ($record && $human)
					$player->record = $record;
					
				$queuer = $this->getQueuer($name);
				if ($queuer && $human)
					$player->queuer = $queuer;
				
				$this->players[] = $player;
			}
		}
		else
		{
			$player = getPlayer($name);
			if ($player)
			{
				$player->isHuman = $human;
				$player->screen_name = $screenName;
				
				$record = $this->getRecord($name);
				if ($record && $human)
					$player->record = $record;
					
				$queuer = $this->getQueuer($name);
				if ($queuer && $human)
					$player->queuer = $queuer;
			}
		}
	}
	function playerRenamed($old, $new, $screenName)
	{
		$player = $this->getPlayer($old);
		
		if ($player)
		{
			$player->name = $new;
			$player->screen_name = $screenName;
			
			//	fetch records and queues related to the new name
			//	this is due to people hacking into other accounts
			
			$record = $this->getRecord($name);
			if ($record && $human)
				$player->record = $record;
			else
				$player->record = null;
				
			$queuer = $this->getQueuer($name);
			if ($queuer && $human)
				$player->queuer = $queuer;
			else
				$player->queuer = null;
		}
	}
	function playerLeft($name)
	{
		if ($this->players > 0)
		{
			foreach ($this->players as $key => $p)
			{
				if ($p->name == $name)
				{
					//	remove player from the list
					unset($this->players[$key]);
					break;
				}
			}
		}
	}
	
	function roundBegan()
	{
		$this->timer = new Timer;
		$this->timer->Start();
		unset(this->races);
	}

	function roundEnded()
	{		
		//	although the timer is stopped, game timer is still running until next round/match starts.
		$this->timer->Stop();
		
		if ($this->zonesCollapseAfterFinish)
		{
			for($i = 0; $i < count($this->zones); $i++)
			{
				$zone = $this->zones[$i];
				if ($zone)
				{
					echo "COLLAPSE_ZONE ".$zone->name."\n";
				}
				unset($this->zones[$i]);
				$i--;
			}
		}
	}
	
	function queuerExists($name)
	{
		if (count($this->queuers) > 0)
		{
			foreach($this->queuers as $queuer)
			{
				if ($queuer->name == $name)
					return true;
			}
		}
		return false;
	}

	function getQueuer($name)
	{
		if (count($this->queuers) > 0)
		{
			foreach($this->queuers as $queuer)
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
		//	loading queuers
		$queFilePath = $this->path.$this->queueFile;
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
							
							$this->queuers[] = $queuer;
						}
					}
				}
				
				fclose($file);
			}
		}
	}
	
	function recordExists($name)
	{
		
		if (count($this->records) > 0)
		{
			foreach($this->records as $record)
			{
				if ($record->name == $name)
					return true;
			}
		}
		return false;
	}

	function getRecord($name)
	{
		
		if (count($this->records) > 0)
		{
			foreach($this->records as $record)
			{
				if ($record->name == $name)
					return $record;
			}
		}
		return false;
	}

	function LoadRecords($item)
	{
		if (count($this->records) > 0)
			unset($this->records);
		
		$fpath = $this->path.$this->recordsDir.$item;
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
							
							$this->records[] = $record;
						}
					}
				}
			}
		}
	}
	
	function AddRotation()
	{
		
		$this->rotation = new Rotation;
		foreach($this->rotations as $item)
		{
			$this->rotation->items[] = $item;
		}
	}
	
	function zoneCreated($name, $x, $y)
	{
		
		$zone = new Zone;
		$zone->name = $name;
		$zone->spawn_pos = new Coord($x, $y);
		
		$this->zones[] = $zone;
	}

	//	syncher for racing
	function racesync()
	{
		$ais 	= 0;
		$humans = 0;
		$alive 	= 0;
		
		if (count($this->players) > 0)
		{
			foreach($this->players as $p)
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
		$rPlayer = new Race;
		if ($rPlayer)
		{
			$rPlayer->name = $name;
			$rPlayer->time = $this->timer->GameTimer();
			
			$this->races[] = $rPlayer;
		}
	}
}
?>