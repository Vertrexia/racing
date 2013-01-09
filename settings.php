<?php

//	class decleration variables
$players 	= null;		//	holds the list of players in server
$timer		= null;		//	timer of the game
$records	= null;		//	holds the list of records of players
$queuers	= null;		//	queuers and their list
$rotation 	= null;		//	for rotation
$zones		= null;		//	holds the list of spawned zones
$races		= null;		//	keeps track of current race stuff

//	directory of records
$path = "/path/to/base/";
$recordsDir = "records";
$queueFile = "playerqueues.txt";

//	for respawing players
$chances = 0;

//	queueing values
$queue_increase_time = 0;	//	should the queues each player increase depending on the time they play for in the server
$queue_give = 4;			//	the amount of queues each player gets
$queue_accesslevel = 2;		//	required access level to activate queue

//	rotation items to load
$rotations = array("config1.cfg", "config2.cfg");
$rotation_type = 1;			//	0-no rotation, 1-per round, 2-per match
$rotation_load = 0;			//	0-INCLUDE, 1-SINCLUDE, 3-RINCLUDE
$rotation_current = "";		//	usually contains the currently loaded rotation item

//	zone settings
$zonesCollapseAfterFinish = false;

//	race settings
$countdown = true;
$countdownMax = 60;
$smartTimer = true;
$countdown_ = -1;
?>