<?php

namespace AppBundle\Utils;

class Stats
{
    private $success;

    private $error;

    private $skipped;

    /**
     * Stats constructor.
     */
    public function __construct()
    {
        $this->skipped = 0;
        $this->error = 0;
        $this->success = 0;
    }

    public function success()
    {
        $this->success = $this->success + 1;
    }

    public function error()
    {
        $this->error = $this->error + 1;
    }

    public function skipped()
    {
        $this->skipped = $this->skipped + 1;
    }

    /**
     * @return int
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return int
     */
    public function getSkipped()
    {
        return $this->skipped;
    }
}
