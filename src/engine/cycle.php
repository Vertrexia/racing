<?php
if (!defined("__ROOT__")) {
    return;
}

class Cycle
{
    var $player;

    var $spawn_pos;
    var $spawn_dir;

    var $pos;
    var $dir;
    var $speed = 0;

    var $isAlive = false;
    var $deathTime = -1;

    var $chances = 0;

    var $kill_idle_break = -1;
    var $kill_idle_activated = false;
    var $idle_begin = -1;
    var $idle_limit = -1;
    var $idle_warn = false;

    function cycleCreated($name, $x, $y, $xdir, $ydir)
    {
        global $ar;
        $player =  $ar->p->getPlayer($name);

        if ($player)
        {
            $cycle = new Cycle;

            if ($cycle)
            {
                $cycle->isAlive = true;
                $cycle->spawn_pos = new Coord($x, $y);
                $cycle->spawn_dir = new Coord($xdir, $ydir);
                $cycle->chances = $ar->chances;
                $cycle->player = $player;

                $player->cycle = $cycle;
            }
        }
    }

    function cycleDestroyed($name)
    {
        global $ar;
        $player =  $ar->p->getPlayer($name);

        if ($player)
        {
            $cycle = $player->cycle;

            if ($cycle)
            {
                $cycle->deathTime = $ar->timer->gametimer();
                $cycle->isAlive = false;

                //	check if chances are enabled
                //	also check if players have enough chances to be respawned
                if (($ar->chances > 0) && ($cycle->chances > 0))
                    respawnCycle($cycle);
            }
        }
    }

    function respawnCycle($cycle)
    {
        global $ar;
        if (($ar->chances > 0) && ($cycle->chances > 0))
        {
            $ar->game->respawnPlayer($cycle->player->screen_name, $cycle->spawn_pos->x, $cycle->spawn_pos->y, $cycle->spawn_dir->x, $cycle->spawn_dir->y);
            $ar->game->cpm($cycle->player->screen_name,"racing_respawn_limit", array($cycle->chances));

            $cycle->isAlive = true;
            $cycle->chances--;
        }
    }

    function player_gridpos($name, $x, $y, $xdir, $ydir, $speed)
    {
        global $ar;
        $player = $ar->p->getPlayer($name);
        if ($player)
        {
            $cycle = $player->cycle;
            if ($cycle)
            {
                $cycle->speed = $speed;
                if ($cycle->isAlive && empty($cycle->pos) && empty($cycle->dir))
                {
                    //	set the new pos, dir and speed of the cycle
                    $cycle->pos = new Coord($x, $y);
                    $cycle->dir = new Coord($xdir, $ydir);
                }
                else
                {
                    if ($cycle->isAlive && $ar->kill_idle)
                    {
                        /*if (($cycle->pos->x == $x) && ($cycle->pos->y == $y)) {
                            $breakTime = $ar->timer->gametimer();
                            if (!$cycle->kill_idle_activated) {
                                $cycle->kill_idle_break = $ar->timer->gametimer() + $ar->kill_idle_wait;
                                $cycle->kill_idle_activated = true;
                            } elseif (($breakTime >= $cycle->kill_idle_break) && ($cycle->kill_idle_break != -1)) {
                                $ar->game->killPlayer($player->name);

                                $cycle->kill_idle_activated = false;
                                $cycle->kill_idle_break = -1;
                            }
                        }*/
                        
                        if ($cycle->isAlive && ($cycle->speed <= $ar->kill_idle_speed))
                        {
                            if (!$cycle->kill_idle_activated)
                            {
                                if ($cycle->idle_begin == -1)
                                {
                                    //  waits this many seconds before rechecking if player is still moving or stopped
                                    $cycle->idle_limit = $ar->timer->gametimer() + $ar->kill_idle_wait;
                                    $cycle->idle_begin = $ar->timer->gametimer();                                    
                                }
                                
                                if ($ar->timer->gametimer() >= $cycle->idle_limit)
                                    $cycle->kill_idle_activated = true;
                            }
                            else
                            {
                                if (!$cycle->idle_warn)
                                {
                                    //  send the player a warning
                                    $ar->game->cpm($player->screen_name, "race_idle_warning");
                                    
                                    //  we warned them
                                    $cycle->idle_warn = true;
                                    
                                    //  resetting this for after warning purpose
                                    $cycle->idle_begin = -1; 
                                }
                                else
                                {
                                    if ($cycle->idle_begin == -1)
                                    {
                                        $cycle->idle_limit = $ar->timer->gametimer() + $ar->kill_idle_wait;
                                        $cycle->idle_begin = $ar->timer->gametimer();
                                    }
                                    
                                    //  after all that, they aren't reacting?
                                    if ($ar->timer->gametimer() >= $cycle->idle_limit)
                                    {
                                        //  no choice, let's remove them from the grid
                                        $ar->game->killPlayer($player->screen_name);
                                    }
                                }
                            }
                        }
                        else
                        {
                            //  if cycle is moving faster, no need for the activation of idle killing
                            //  reset values to default
                            $cycle->kill_idle_activated = false;
                            $cycle->kill_idle_break = -1;
                            $cycle->idle_begin = -1;
                            $cycle->idle_limit = -1;
                            $cycle->idle_warn = false;
                        }
                    }

                    //	set the new pos, dir and speed of the cycle
                    $cycle->pos = new Coord($x, $y);
                    $cycle->dir = new Coord($xdir, $ydir);
                }
            }
        }
    }
}
?>