<?php
//	script is integrated with sty+ct+ap_r837

//	defining constant path
define(__ROOT__, dirname("/path/to/script.php"));

require("settings.php");
$ar = new Base;

require(__ROOT__."/src/tools/string.php");

$ar->LoadQueuers();
$ar->AddRotation();

while(1)
{
	$line = rtrim(fgets(STDIN, 1024));
	if ((startswith($line, "PLAYER_ENTERED")) || (startswith($line, "PLAYER_AI_ENTERED")))
	{
		$lineExt = explode(" ", $line);
		$screen_name = substr($line, strlen($lineExt[0]) + strlen($lineExt[1]) + strlen($lineExt[2]) + 3);
		
		if (startswith($line, "PLAYER_ENTERED"))
			$ar->playerEntered($lineExt[1], $screen_name, true);
		else
			$ar->playerEntered($lineExt[1], $screen_name, false);
	}
	elseif (startswith($line, "PLAYER_RENAMED"))
	{
		$lineExt = explode(" ", $line);
		$screen_name = substr($line, strlen($lineExt[0]) + strlen($lineExt[1]) + strlen($lineExt[2]) + strlen($lineExt[3]) + 4);
		$ar->playerRenamed($lineExt[2], $lineExt[1], $screen_name);
	}
	elseif ((startswith($line, "PLAYER_LEFT")) || (startswith($line, "PLAYER_AI_LEFT")))
	{
		$lineExt = explode(" ", $line);
		$ar->playerLeft($name);
	}
	elseif (startswith($line, "ROUND_STARTED"))
		$ar->roundBegan();
	elseif (startswith($line, "ROUND_ENDED"))
		$ar->roundEnded();
	elseif (startswith($line, "CYCLE_CREATED"))
	{
		$lineExt = explode(" ", $line);
		$ar->cycleCreated($lineExt[1], $lineExt[2], $lineExt[3], $lineExt[4], $lineExt[5]);
	}
	elseif (startswith($line, "CYCLE_DESTROYED"))
	{
		$lineExt = explode(" ", $line);
		$ar->cycleDestroyed($lineExt[1]);	//, $lineExt[2], $lineExt[3], $lineExt[4], $lineExt[5]);
	}
	elseif ((startswith($line, "DEATH_SUICIDE")) || (startswith($line, "DEATH_DEATHZONE")))
	{
		$lineExt = explode(" ", $line);
		$ar->cycleDestroyed($lineExt[1]);
	}
	elseif (startswith($line, "DEATH_FRAG"))
	{
		$lineExt = explode(" ", $line);
		$ar->cycleDestroyed($lineExt[2]);
	}
	elseif (startswith($line, "NEW_ROUND"))
		$ar->rotation->Rotate();	//	rotate item
	elseif (startswith($line, "INVALID_COMMAND"))
	{
		$lineExt = explode(" ", $line);
	}
	elseif (startswith($line, "WINZONE_PLAYER_ENTER"))
	{
		$lineExt = explode(" ", $line);
		$ar->crossLine($lineExt[1]);
	}

	//	keep race in sync
	$ar->racesync();
	$prv = $new;
}
?>