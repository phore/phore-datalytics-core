<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 27.11.18
 * Time: 12:17
 */

namespace Phore\Datalytics\Core\DataMerge;


use Phore\Datalytics\Core\TimeSeries;

class DataMerger
{

    /**
     * @var DataMergeChannel[]
     */
    private $channels = [];

    /**
     * @var callable
     */
    private $writer;


    public function __construct()
    {
    }


    public function addInputChannel(DataMergeChannel $dataMergeChannel) : DataMergeChannel
    {
        return $this->channels[] = $dataMergeChannel;
    }

    /**
     * Set the writer function
     *
     * The writer function is called for each dataset from each channel
     *
     *
     * ```
     * $c->setWriter(function($data) {
     *      echo "ts: " . $data[0];
     *      echo "data: " . print_r($data[1], true);
     * });
     * ```
     *
     * @param callable $writer
     */
    public function setWriter(callable $writer)
    {
        $this->writer = $writer;
    }

    public function run()
    {

        while(true) {
            $lowestChannel = null;
            $lowestTs = PHP_INT_MAX;

            // Find the channel with the lowest timestamp in buffered data
            foreach ($this->channels as $idx => $channel) {
                $curChannelTs = $channel->getBufferTs();
                if ($curChannelTs === null)
                    continue; // No more data in channel

                if ($curChannelTs < $lowestTs) {
                    $lowestTs = $curChannelTs;
                    $lowestChannel = $idx;
                }
            }

            if ($lowestChannel === null) {
                // No more data found
                return true;
            }

            $chan = $this->channels[$lowestChannel];
            ($this->writer)($chan->getData());
            // Move to the next dataset
            $chan->readNext();
        }
    }

}
