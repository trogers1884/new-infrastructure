<?php
namespace App\Helpers;

class Timer
{

    protected $start;
    protected $end;

    public function __construct()
    {
        $this->startTimer();
    }

    public function startTimer()
    {
        $this->start = time();
    }

    public function endTimer()
    {
        $this->end = time();
    }

    public function getStartTime()
    {
        return $this->start;
    }

    public function getEndTime()
    {
        return $this->end;
    }



    public function getElapsedTime()
    {
        $this->endTimer();
        $taskTime = number_format((float)(($this->end - $this->start) / 60),2,'.','');
        return $taskTime;
    }


}
