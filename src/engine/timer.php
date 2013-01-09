<?php
if (!defined(__ROOT__))
	return;

class Timer
{
	var $start = 0;
	var $stop = 0;
	
	var $isRunning = false;
	
	function Start()
	{
		$this->start = round(microtime(), 4);
		$this->isRunning = true;
	}
	
	function Stop()
	{
		if ($this->$isRunning)
		{
			$this->stop = round(microtime(), 4);
			$this->isRunning = false;
		}
	}
	
	function GameTimer()
	{
		$gametime_ = round(microtime(), 4) - $this->start;
		return ($gametime_ - 4);
	}
	
	function Elapsed()
	{
		if (!$this->isRunning)
		{
			$elapsed_ = $this->stop - $this->start;
			return ($elapsed_ - 4);
		}
	}
};
?>