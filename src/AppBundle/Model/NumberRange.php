<?php

namespace AppBundle\Model;

class NumberRange
{
    /** @var int */
    private $from;

    /** @var int */
    private $to;

    /**
     * @return int
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param int $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return int
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param int $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }
}
