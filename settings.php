<?php
if (!defined("__ROOT__")) {
    return;
}

require __ROOT__."/src/engine/cycle.php";
require __ROOT__."/src/engine/player.php";
require __ROOT__."/src/engine/timer.php";

require __ROOT__."/src/tools/coord.php";
require __ROOT__."/src/tools/string.php";

require __ROOT__."/src/game/game.php";
require __ROOT__."/src/game/queue.php";
require __ROOT__."/src/game/race.php";
require __ROOT__."/src/game/records.php";
require __ROOT__."/src/game/rotation.php";
require __ROOT__."/src/game/zones.php";

class Base
{
    //	class decleration variables
    var $players 	= null;		//	holds the list of players in serve
    var $records	= null;		//	holds the list of records of players
    var $queuers	= null;		//	queuers and their list
    var $zones		= null;		//	holds the list of spawned zones
    var $races		= null;		//	keeps track of current race stuff
    var $paths		= null;		//	stores the paths

    //	directory of records
    var $path 			= __ROOT__;
    var $recordsDir 	= "/data/records/";
    var $queueFile 		= "/data/playerqueues.txt";

    //	cycle settings
    var $chances = 0;					//	number of times players can be respawned per round
    var $kill_idle = true;				//	should players get killed for remaining idle in one position
    var $kill_idle_wait = 10;			//	waits for this many seconds before checking on cycle's position

    //	queueing values
    var $queue_increase_time = 0;		//	should the queues each player increase depending on the time they play for in the server
    var $queue_give = 4;				//	the amount of queues each player gets
    var $queue_accesslevel = 2;			//	required access level to activate queue

    //	rotation items to load
    var $rotations = array("config1.cfg", "config2.cfg");
    var $rotation_type = 1;			//	0-no rotation, 1-per round, 2-per match
    var $rotation_load = 0;			//	0-INCLUDE, 1-SINCLUDE, 3-RINCLUDE
    var $rotation_current = "";		//	usually contains the currently loaded rotation item

    //	zone settings
    var $zonesCollapseAfterFinish = false;

    //	race settings
    var $countdown = true;
    var $countdownMax = 80;
    var $smartTimer = true;
    var $countdown_ = -1;
    var $firstTime_ = -1;
    var $finishRank = 1;

    //	constants
    var $p 			= null;	//	player class
    var $c 			= null;	//	cycle class
    var $game		= null;	//	game class
    var $q 			= null;	//	queue class
    var $r 			= null;	//	records class
    var $rotation 	= null;	//	rotation class
    var $z			= null;	//	zone class
    var $race		= null;	//	race class
    var $timer		= null;	//	timer class

    //	load all classes in a constant system
    function Init()
    {
        $this->p 		= new Player("");
        $this->c 		= new Cycle();
        $this->game 	= new Game();
        $this->q 		= new Queuer("");
        $this->r 		= new Record("");
        $this->rotation = new Rotation();
        $this->z 		= new Zone();
        $this->race 	= new Race();
        $this->timer 	= new Timer();
    }
}
?>