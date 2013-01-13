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
        
        //  don't run this if countdown is not enabled
        if (!$ar->countdown) return;

        //  if round has ended, don't go through anymore
        if ($ar->game->roundFinished) return;

        $ais 	= 0;
        $humans = 0;
        $alive 	= 0;

        if (count($ar->players) > 0)
        {
            foreach ($ar->players as $p)
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
        else
        {
            return;
        }
        
        //  perform actions if no one is alive and there are humans players present in the server
        if (($humans > 0) && ($alive == 0) && ($ais == 0))
        {
            $ar->game->roundFinished = true;
            $this->decideWinner();
            return;
        }

        //  perform actions if only one player is alive and in the presence of themselves or more human players
        if (($humans > 0) && ($alive == 1) && ($ais == 0))
        {
            if ($ar->smartTimer)
            {
                if (count($ar->records) > 0)
                {
                    if (count($ar->records) == 0)
                    {
                        if ($ar->countdown_ == -1)
                            $ar->countdown_ = $ar->countdownMax + 1;
                    }
                    elseif (count($ar->records) == 1)
                    {
                        if ($ar->countdown_ == -1)
                        {
                            $record = $ar->records[0];
                            if ($record)
                            {
                                $time = round($record->time);
                                $ar->countdown_ = ($time * 1.2) + 1;
                            }
                        }
                    }
                    elseif (count($ar->records) == 2)
                    {
                        if ($ar->countdown_ == -1)
                        {
                            $recorda = $ar->records[0];
                            $recordb = $ar->records[1];
                            if ($recorda && $recordb)
                            {
                                $time = ((round($recorda->time + $recordb->time)) / 2);
                                $ar->countdown_ = ($time * 1.2) + 1;
                            }
                        }
                    }
                    elseif (count($ar->records) >= 3)
                    {
                        if ($ar->countdown_ == -1)
                        {
                            $recorda = $ar->records[0];
                            $recordb = $ar->records[1];
                            $recordc = $ar->records[2];
                            if ($recorda && $recordb && $recordc)
                            {
                                $time = ((round($recorda->time + $recordb->time + $recordc->time)) / 2);
                                $ar->countdown_ = ($time * 1.2) + 1;
                            }
                        }
                    }                    
                }
            }
            else
            {
                if ($ar->countdown_ == -1)
                    $ar->countdown_ = $ar->countdownMax + 1;
            }
            
            //  make sure there is a 1 second time gap between countdown.
            //  otherwise, it will finish too quickly
            if (($ar->timer->gametimer() - $ar->race_prv_sync) >= 1)
            {
                $ar->countdown_--;

                if ($ar->countdown_ > 0)
                    $ar->game->center("0xff7777".$ar->countdown_."                    ");
                else
                {
                    $ar->game->roundFinished = true;
                    $this->decideWinner();
                }
                
                $ar->race_prv_sync = $ar->timer->gametimer();
            }          
        }
    }

    //	player crossing the finish line
    function crossLine($name)
    {
        global $ar;

        $rPlayer = new Race;
        if ($rPlayer)
        {
            $rPlayer->name = $name;
            $rPlayer->time = $ar->timer->gametimer();

            if (!$ar->firstTime_ == -1)
            {
                $rPlayer->first = true;
                $ar->firstTime_ = $rPlayer->time;

                $player = $ar->p->getPlayer();
                if ($player)
                {
                    $ar->game->cm("race_finish_first", array($player->screen_name, $rPlayer->time));
                }
            }
            else
            {
                $player = $ar->p->getPlayer();
                if ($player)
                {
                    $ar->finishRank++;
                    $ar->game->cm("race_finish_after_first", array($player->screen_name, $ar->finishRank, ($rPlayer->time - $ar->firstTime_)));
                }
            }

            if ($ar->r->recordExists($name))
            {
                $pRecord = $ar->r->getRecord($name);
                if ($pRecord)
                {
                    if ($rPlayer->time < $pRecord->time)
                    {
                        $ar->r->adjustRecords($pRecord, $rPlayer->time);
                    }
                }
            }
            else
                $ar->r->newRecord($name, $rPlayer->time);			

            $ar->races[] = $rPlayer;
        }
    }
    
    //  decicing a winner
    function decideWinner()
    {
        global $ar;
        
        if ((count($ar->races) > 0) && ($ar->firstTime_ != -1))
        {
            foreach ($ar->races as $racer)
            {
                if ($racer)
                {
                    $player = $ar->p->getPlayer($racer->name);
                    if ($player)
                    {
                        $ar->game->declareRoundWinner($player->screen_name);
                    }
                }
            }
        }
    }
};
?>