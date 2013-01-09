<?php
//	script is integrated with sty+ct+ap_r837

//	defining constant path
define(__ROOT__, dirname("/path/to/script.php"));

require("settings.php");

//	declare global variables from settings
global $players, $timer, $records, $queuers, $rotation, $zones, $races;
global $path, $recordsDir, $queueFile;
global $chances;
global $queue_increase_time, $queue_give, $quque_accesslevel;
global $rotations, $rotation_type, $rotation_load, $rotation_current;
global $zonesCollapseAfterFinish;
global $countdown, $countdownMax, $smartTimer, $countdown_;

require(__ROOT__."/src/engine/cycle.php");
require(__ROOT__."/src/engine/player.php");
require(__ROOT__."/src/engine/timer.php");

require(__ROOT__."/src/tools/coord.php");
require(__ROOT__."/src/tools/string.php");

require(__ROOT__."/src/game/game.php");
require(__ROOT__."/src/game/queue.php");
require(__ROOT__."/src/game/race.php");
require(__ROOT__."/src/game/records.php");
require(__ROOT__."/src/game/rotation.php");
require(__ROOT__."/src/game/zones.php");

LoadQueuers();
AddRotation();

$prv = round(microtime());
$new = 0;

while(1)
{
	$line = rtrim(fgets(STDIN, 1024));
	if ((startswith($line, "PLAYER_ENTERED")) || (startswith($line, "PLAYER_AI_ENTERED")))
	{
		$lineExt = explode(" ", $line);
		$screen_name = substr($line, strlen($lineExt[0]) + strlen($lineExt[1]) + strlen($lineExt[2]) + 3);
		
		if (startswith($line, "PLAYER_ENTERED"))
			playerEntered($lineExt[1], $screen_name, true);
		else
			playerEntered($lineExt[1], $screen_name, false);
	}
	elseif (startswith($line, "PLAYER_RENAMED"))
	{
		$lineExt = explode(" ", $line);
		$screen_name = substr($line, strlen($lineExt[0]) + strlen($lineExt[1]) + strlen($lineExt[2]) + strlen($lineExt[3]) + 4);
		playerRenamed($lineExt[2], $lineExt[1], $screen_name);
	}
	elseif ((startswith($line, "PLAYER_LEFT")) || (startswith($line, "PLAYER_AI_LEFT")))
	{
		$lineExt = explode(" ", $line);
		playerLeft($name);
	}
	elseif (startswith($line, "ROUND_STARTED"))
		roundBegan();
	elseif (startswith($line, "ROUND_ENDED"))
		roundEnded();
	elseif (startswith($line, "CYCLE_CREATED"))
	{
		$lineExt = explode(" ", $line);
		cycleCreated($lineExt[1], $lineExt[2], $lineExt[3], $lineExt[4], $lineExt[5]);
	}
	elseif (startswith($line, "CYCLE_DESTROYED"))
	{
		$lineExt = explode(" ", $line);
		cycleDestroyed($lineExt[1]);	//, $lineExt[2], $lineExt[3], $lineExt[4], $lineExt[5]);
	}
	elseif (startswith($line, "NEW_ROUND"))
		$rotation->Rotate();	//	rotate item
	elseif (startswith($line, "INVALID_COMMAND"))
	{
		$lineExt = explode(" ", $line);
	}
	elseif (startswith($line, "WINZONE_PLAYER_ENTER"))
	{
		$lineExt = explode(" ", $line);
		crossLine($lineExt[1]);
	}

	//	this little code is for synching every second
	$new = round(microtime());
	if (($new - $prv) == 1)
	{
		//	keep race in sync
		racesync();
		$prv = $new;
	}
}
?>