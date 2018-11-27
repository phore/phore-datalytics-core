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
     * @var TimeSeries
     */
    private $targetTimeSeries;


    public function __construct(TimeSeries $targetTimeSeries)
    {
        $this->targetTimeSeries = $targetTimeSeries;
    }


    public function getInputChannel() : DataMergeChannel
    {
        return $this->channels[] = new DataMergeChannel($this);
    }


    private function __runSingleQueue(&$allChannelsClosed)
    {
        $lowestChannel = null;
        $lowestTs = null;
        $allChannelsClosed = true;
        foreach ($this->channels as $channel) {
            if ( ! $channel->__isReady()) {
                $allChannelsClosed = false;
                return false;
            }
            $curNextTs = $channel->__getNextTs();
            if ($curNextTs === null)
                continue; // No more data in this channel (closed)

            $allChannelsClosed = false;
            if ($curNextTs < $lowestTs || $lowestTs === null) {
                $lowestChannel = $channel;
                $lowestTs = $channel->__getNextTs();
            }
        }
        if ($lowestChannel === null)
            return false;

        $data = $lowestChannel->__pull();
        foreach ($data[1] as $key => $value)
            $this->targetTimeSeries->push($data[0], $key, $value);
        return true;

    }

    public function __runQueue()
    {
        while ($this->__runSingleQueue($allChannelsClosed) === true);
        if ($allChannelsClosed) {
            $this->targetTimeSeries->close();
        }
        return true;
    }





}
