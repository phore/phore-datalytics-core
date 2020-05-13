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

    private $buffer = null;
    private $isClosed = false;

    private $reader;

    public function __construct(callable $reader)
    {
        $this->reader = $reader;
    }


    /**
     * Set the reader to read a dataset. The reader must return a
     * array with a timestamp at index 1 and any data at index 2
     *
     * ```
     * function() {
     *  return [ (int)$ts, ["some"=>"data"] ];
     * }
     * ```
     *
     * The reader should return NULL if no more data is available
     *
     * @param callable $fn
     */
    public function setReader(callable $fn)
    {
        $this->reader = $fn;
    }


    /**
     * Reads the next dataset from the reader and
     * stores it into the buffer.
     *
     * @return bool
     */
    public function readNext()
    {
        $this->buffer = ($this->reader)();
        if ($this->buffer === null) {
            $this->isClosed = true;
            return false;
        }
        return true;
    }

    /**
     * Read until the buffer
     *
     * @param float $ts
     */
    public function bufferReadUntil(float $ts)
    {
        while ($this->buffer !== null && $this->buffer[0] < $ts)
            $this->readNext();
    }


    /**
     * Return the current ts in the buffer
     *
     * Reads the first dataset if the buffer is
     * empty.
     *
     * @return float|null
     */
    public function getBufferTs() : ?float
    {
        if ($this->buffer === null && $this->isClosed === false) {
            // First read attempt
            $this->readNext();
        }

        if ($this->buffer !== null)
            return $this->buffer[0];
        return null;
    }


    /**
     * return the contents of the current Buffer
     *
     * @return array
     */
    public function getData() : array
    {
        return $this->buffer;
    }


}
