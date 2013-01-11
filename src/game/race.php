<?php
if (!defined("__ROOT__")) {
    return;
}

//	class to store racing stats for the round, temporary
class Race
{
    var $name;
    var $time;

    var $first = false;	//	did this player cross the finish line, first?

    //	syncher for racing
    function racesync()
    {
        global $ar;

        if ($ar->game->roundFinished) {
            return;
        }

        $ais 	= 0;
        $humans = 0;
        $alive 	= 0;

        if (!empty($ar->players)) {
            foreach ($ar->players as $p) {
                if ($p) {
                    if ($p->isHuman) {
                        $humans++;
                    } else {
                        $ais++;
                    }

                    $cycle = $p->cycle;
                    if ($cycle && $cycle->isAlive) {
                        $alive++;
                    }
                }
            }
        } else {
            return;
        }

        if (($humans > 0) && ($alive == 1) && ($ais == 0) && $ar->countdown) {
            if ($ar->smartTimer) {
                //	TODO: code lader
            } else {
                if ($ar->countdown_ == -1) {
                    $ar->countdown_ = $ar->countdownMax + 1;
                }

                $ar->countdown_--;

                if ($ar->countdown_ > 0) {
                    center("0xff7777".$ar->countdown_."                    ");
                } else {
                    $ar->game->roundFinished = true;
                }
            }
        }
    }

    //	player crossing the finish line
    function crossLine($name)
    {
        global $ar;

        $rPlayer = new Race;
        if ($rPlayer) {
            $rPlayer->name = $name;
            $rPlayer->time = $ar->timer->gametimer();

            if (!$rPlayer->first) {
                $rPlayer->first = true;
                $ar->firstTime_ = $rPlayer->time;

                $player = $ar->p->getPlayer();
                if ($player) {
                    $ar->game->cm("race_finish_first", $player->screen_name." ".$rPlayer->time);
                }
            } else {
                $player = $ar->p->getPlayer();
                if ($player) {
                    $ar->finishRank++;
                    $ar->game->cm("race_finish_after_first", $player->screen_name." ".$ar->finishRank." ".($rPlayer->time - $ar->firstTime_));
                }
            }

            if ($ar->r->recordExists($name)) {
                $pRecord = $ar->r->getRecord($name);
                if ($pRecord) {
                    if ($rPlayer->time < $pRecord->time) {
                        $ar->r->adjustRecords($pRecord, $rPlayer->time);
                    }
                }
            } else {
                $ar->r->newRecord($name, $rPlayer->time);
            }			

            $ar->races[] = $rPlayer;
        }
    }
};
?>