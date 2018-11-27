<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 27.11.18
 * Time: 12:20
 */

namespace Phore\Datalytics\Core\DataMerge;



class DataMergeChannel
{

    private $buffer = [];
    private $isClosed = false;

    /**
     * @var DataMerger
     */
    private $dataMerger;

    public function __construct(DataMerger $dataMerger)
    {
        $this->dataMerger = $dataMerger;
    }


    public function push(float $ts, $data)
    {
        if ($this->isClosed)
            throw new \InvalidArgumentException("Channel is already closed.");
        $this->buffer[] = [$ts, $data];
        $this->dataMerger->__runQueue();
    }


    public function close()
    {
        $this->isClosed = true;
        $this->dataMerger->__runQueue();
    }



    public function __isReady()
    {
        if (count ($this->buffer) > 0 || $this->isClosed)
            return true;
        return false;
    }


    public function __getNextTs()
    {
        if (count ($this->buffer) === 0)
            return null;
        return $this->buffer[count($this->buffer)-1][0];
    }

    public function __pull() : array
    {
        return array_pop($this->buffer);
    }



}
