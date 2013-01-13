<?php
if (!defined("__ROOT__")) {
    return;
}

class Timer
{
    var $start = 0;
    var $stop = 0;

    var $isRunning = false;

    function start()
    {
        $this->start = round(microtime(), 4);
        $this->isRunning = true;
    }

    function stop()
    {
        if ($this->isRunning)
        {
            $this->stop = round(microtime(), 4);
            $this->isRunning = false;
        }
    }

    function gametimer()
    {
        $gametime_ = round(microtime(), 4) - $this->start;
        return ($gametime_ - 4);
    }

    //	gets how long round lasted
    function elapsed()
    {
        if (!$this->isRunning)
        {
            $elapsed_ = $this->stop - $this->start;
            return ($elapsed_ - 4);
        }
    }
};
?>