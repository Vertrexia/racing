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

    var $isAlive = false;
    var $deathTime = -1;

    var $chances = 0;

    var $kill_idle_break = -1;
    var $kill_idle_activated = false;

    //	functions are listed below
    function cycleCreated($name, $x, $y, $xdir, $ydir)
    {
        global $ar;
        $player =  $ar->p->getPlayer($name);

        if ($player) {
            $cycle = new Cycle;

            if ($cycle) {
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

        if ($player) {
            $cycle = $player->cycle;

            if ($cycle) {
                $cycle->deathTime = $ar->timer->gametimer();
                $cycle->isAlive = false;

                //	check if chances are enabled
                //	also check if players have enough chances to be respawned
                if (($ar->chances > 0) && ($cycle->chances > 0)) {
                    respawnCycle($cycle);
                }
            }
        }
    }

    function respawnCycle($cycle)
    {
        global $ar;
        if (($ar->chances > 0) && ($cycle->chances > 0)) {
            $ar->game->respawnPlayer($cycle->player->name, $cycle->spawn_pos->x, $cycle->spawn_pos->y, $cycle->spawn_dir->x, $cycle->spawn_dir->y);
            $ar->game->cpm($cycle->player->name,"racing_respawn_limit", $cycle->chances);

            $cycle->isAlive = true;
            $cycle->chances--;
        }
    }

    function player_gridpos($name, $x, $y, $xdir, $ydir)
    {
        global $ar;
        $player = $ar->p->getPlayer($name);
        if ($player) {
            $cycle = $player->cycle;
            if ($cycle) {
                if ($cycle->isAlive && empty($cycle->pos) && empty($cycle->dir)) {
                    //	set the new pos and dir of the cycle
                    $cycle->pos = new Coord($x, $y);
                    $cycle->dir = new Coord($xdir, $ydir);
                } else {
                    if ($cycle->isAlive && $ar->kill_idle) {
                        if (($cycle->pos->x == $x) && ($cycle->pos->y == $y)) {
                            $breakTime = $ar->timer->gametimer();
                            if (!$cycle->kill_idle_activated) {
                                $cycle->kill_idle_break = $ar->timer->gametimer() + $ar->kill_idle_wait;
                                $cycle->kill_idle_activated = true;
                            } elseif (($breakTime >= $cycle->kill_idle_break) && ($cycle->kill_idle_break != -1)) {
                                $ar->game->killPlayer($player->name);

                                $cycle->kill_idle_activated = false;
                                $cycle->kill_idle_break = -1;
                            }
                        }
                    }

                    //	set the new pos and dir of the cycle
                    $cycle->pos = new Coord($x, $y);
                    $cycle->dir = new Coord($xdir, $ydir);
                }
            }
        }
    }
}
?>