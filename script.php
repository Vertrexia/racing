<?php

//	defining constant path
define("__ROOT__", dirname(__FILE__));

require "settings.php";
$ar = new Base;
$ar->Init();

$ar->q->loadQueuers();
$ar->rotation->addRotation();

while (1) {
    $line = rtrim(fgets(STDIN, 1024));
    if ((startswith($line, "PLAYER_ENTERED")) || (startswith($line, "PLAYER_AI_ENTERED"))) {
        $lineExt = explode(" ", $line);
        $screen_name = substr($line, strlen($lineExt[0]) + strlen($lineExt[1]) + strlen($lineExt[2]) + 3);

        if (startswith($line, "PLAYER_ENTERED"))
            $ar->p->playerEntered($lineExt[1], $screen_name, true);
        else
            $ar->p->playerEntered($lineExt[1], $screen_name, false);
    } elseif (startswith($line, "PLAYER_RENAMED")) {
        $lineExt = explode(" ", $line);
        $screen_name = substr($line, strlen($lineExt[0]) + strlen($lineExt[1]) + strlen($lineExt[2]) + strlen($lineExt[3]) + 4);
        $ar->p->playerRenamed($lineExt[2], $lineExt[1], $screen_name);
    } elseif ((startswith($line, "PLAYER_LEFT")) || (startswith($line, "PLAYER_AI_LEFT"))) {
        $lineExt = explode(" ", $line);
        $ar->p->playerLeft($name);
    } elseif (startswith($line, "ROUND_STARTED"))
        $ar->game->roundBegan();
    elseif (startswith($line, "ROUND_ENDED"))
        $ar->game->roundEnded();
    elseif (startswith($line, "CYCLE_CREATED")) {
        $lineExt = explode(" ", $line);
        $ar->c->cycleCreated($lineExt[1], $lineExt[2], $lineExt[3], $lineExt[4], $lineExt[5]);
    } elseif (startswith($line, "CYCLE_DESTROYED")) {
        $lineExt = explode(" ", $line);
        $ar->c->cycleDestroyed($lineExt[1]);	//, $lineExt[2], $lineExt[3], $lineExt[4], $lineExt[5]);
    } elseif ((startswith($line, "DEATH_SUICIDE")) || (startswith($line, "DEATH_DEATHZONE"))) {
        $lineExt = explode(" ", $line);
        $ar->c->cycleDestroyed($lineExt[1]);
    } elseif (startswith($line, "DEATH_FRAG")) {
        $lineExt = explode(" ", $line);
        $ar->c->cycleDestroyed($lineExt[2]);
    } elseif (startswith($line, "PLAYER_GRIDPOS")) {
        $lineExt = explode(" ", $line);
        $ar->c->player_gridpos($lineExt[1], $lineExt[2], $lineExt[3], $lineExt[4], $lineExt[5]);
    } elseif (startswith($line, "NEW_ROUND"))
        $ar->rotation->rotate();	//	rotate item
    elseif (startswith($line, "INVALID_COMMAND")) {
        $lineExt = explode(" ", $line);
    } elseif (startswith($line, "WINZONE_PLAYER_ENTER")) {
        $lineExt = explode(" ", $line);
        $ar->race->crossLine($lineExt[1]);
    }

    //	keep race in sync
    $ar->racesync();
    $prv = $new;
}
?>