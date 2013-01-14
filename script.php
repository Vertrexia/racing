<?php

//  scripts integrated with 0.2.9-armagetronad-sty+ct+ap

//	defining constant path
define("__ROOT__", dirname(__FILE__));

require "settings.php";
$ar = new Base;
$ar->init();

$ar->q->loadQueuers();
$ar->rotation->init();

$ar->race_prv_sync = $ar->timer->gametimer();

while (1)
{
    $line = rtrim(fgets(STDIN, 1024));
    if ((startswith($line, "PLAYER_ENTERED")) || (startswith($line, "PLAYER_AI_ENTERED")))
    {
        $lineExt = explode(" ", $line);
        $screen_name = substr($line, strlen($lineExt[0]) + strlen($lineExt[1]) + strlen($lineExt[2]) + 3);

        if (startswith($line, "PLAYER_ENTERED"))
        {
            $ar->p->playerEntered($lineExt[1], $screen_name, true);
        }
        else
        {
            $ar->p->playerEntered($lineExt[1], $screen_name, false);
        }
    }
    elseif (startswith($line, "PLAYER_RENAMED"))
    {
        $lineExt = explode(" ", $line);
        $screen_name = substr($line, strlen($lineExt[0]) + strlen($lineExt[1]) + strlen($lineExt[2]) + strlen($lineExt[3]) + 4);
        $ar->p->playerRenamed($lineExt[2], $lineExt[1], $screen_name);
    }
    elseif ((startswith($line, "PLAYER_LEFT")) || (startswith($line, "PLAYER_AI_LEFT")))
    {
        $lineExt = explode(" ", $line);
        $ar->p->playerLeft($name);
    }
    elseif (startswith($line, "ROUND_STARTED"))
    {
        $ar->game->roundBegan();
    }
    elseif (startswith($line, "ROUND_ENDED"))
    {
        $ar->game->roundEnded();
    }
    elseif (startswith($line, "CYCLE_CREATED"))
    {
        $lineExt = explode(" ", $line);
        $ar->c->cycleCreated($lineExt[1], $lineExt[2], $lineExt[3], $lineExt[4], $lineExt[5]);
    }
    elseif (startswith($line, "CYCLE_DESTROYED"))
    {
        $lineExt = explode(" ", $line);
        $ar->c->cycleDestroyed($lineExt[1]);	//, $lineExt[2], $lineExt[3], $lineExt[4], $lineExt[5]);
    }
    elseif ((startswith($line, "DEATH_SUICIDE")) || (startswith($line, "DEATH_DEATHZONE")))
    {
        $lineExt = explode(" ", $line);
        $ar->c->cycleDestroyed($lineExt[1]);
    }
    elseif (startswith($line, "DEATH_FRAG"))
    {
        $lineExt = explode(" ", $line);
        $ar->c->cycleDestroyed($lineExt[2]);
    }
    elseif (startswith($line, "PLAYER_GRIDPOS"))
    {
        $lineExt = explode(" ", $line);
        $ar->c->player_gridpos($lineExt[1], $lineExt[2], $lineExt[3], $lineExt[4], $lineExt[5], $lineExt[6]);
    }
    elseif (startswith($line, "NEW_MATCH"))
    {
        //  check if the queue is empty to do regular rotation
        if ($ar->queue_items == 0)
        {
            //  rotatie every match
            if ($ar->rotation_type == 2)
            {
                $ar->rotation->rotate();	    //	rotate item
                $ar->rotation->done = true;     //  yup, rotation has done it's job
            }
        }
    }    
    elseif (startswith($line, "NEW_ROUND"))
    {
        //  check if the queue is empty to do regular rotation
        if ($ar->queue_items == 0)
        {    
            //  rotate every round
            if (($ar->rotation_type == 1) && (!$ar->rotation->done))
            {
                $ar->rotation->rotate();	    //	rotate item
                $ar->rotation->done = true;     //  yup, rotation has done it's job
            }
        }
        else
        {
            $item = $ar->queue_items[0];
            if ($item != "")
            {
                if ($ar->rotation_load == 0)
                {
                    echo "INCLUDE ".$item."\n";
                }
                elseif ($ar->rotation_load == 1)
                {
                    echo "SINCLUDE ".$item."\n";
                }
                elseif ($ar->rotation_load == 2)
                {
                    echo "RINCLUDE ".$item."\n";
                }
                
                $ar->game->con("Reading from queue: ".$item);
                $ar->r->loadRecords($item);
            }
            
            //  remove queued item from list afterwards
            unset($ar->queue_items[0]);
        }
    }
    elseif (startswith($line, "MATCH_ENDED"))
    {
        //  reset done for rotation per match
        if ($ar->rotation_type == 2)
            $ar->rotation->done = false;        
    }
    elseif (startswith($line, "INVALID_COMMAND"))
    {
        $lineExt = explode(" ", $line);
        
        $caller = new InvalidCommand();
        if ($caller)
        {
            $caller->command =  $lineExt[1];
            $caller->caller = $lineExt[2];
            $caller->access_level = $lineExt[4];
            
            //  get the rest of the params from ladderlog string
            $extra = substr($line, strlen($lineExt[0]) + strlen($lineExt[1]) + strlen($lineExt[2]) + strlen($lineExt[3]) + strlen($lineExt[4]) + 5);
            $caller->params = $extra;
            
            //  get the invalid command working then!
            $caller->execute();
        }
    }
    elseif (startswith($line, "WINZONE_PLAYER_ENTER"))
    {
        $lineExt = explode(" ", $line);
        $ar->race->crossLine($lineExt[1]);
    }
    elseif ((startswith($line, "ZONE_SPAWNED")) || (startswith($line, "ZONE_CREATED")))
    {
        $lineExt = explode(" ", $line);
        $ar->z->zoneCreated($lineExt[2], $lineExt[3], $lineExt[4]);
    }
    elseif (startswith($line, "ONLINE_PLAYER"))
    {
        $lineExt = explode(" ", $line);
        $player = $ar->p->getPlayer($lineExt[1]);
        if ($player)
        {
            $player->access_level = $lineExt[5];
        }
    }
    elseif (startswith($line, "PLAYER_COLORED_NAME"))
    {
        $lineExt = explode(" ", $line);
        $player = $ar->p->getPlayer($lineExt[1]);
        if ($player)
        {
            $color_name = substr($line, strlen($lineExt[0]) + strlen($lineExt[1]) + 2);
            $player->color_name = $color_name;
        }
    }

    //	keep race in sync
    $ar->race->racesync();
}
?>