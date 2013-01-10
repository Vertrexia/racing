<?php
if (!defined(__ROOT__))
	return;

class Cycle
{
	var $player;
	
	var $spawn_pos;
	var $spawn_dir;
	
	var $pos;
	var $dir;
	
    var $isAlive = false;
	var $deathTime = 0;
	
	var $chances = 0;
};
?>