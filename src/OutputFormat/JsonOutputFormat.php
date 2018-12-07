<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 06.12.18
 * Time: 11:00
 */

namespace Phore\Datalytics\Core\OutputFormat;


use Phore\FileSystem\FileStream;

class JsonOutputFormat implements OutputFormat
{
    private $outputJson = [];
    private $jsonTs = null;
    private $data = [];


    public function __construct(FileStream $res = null, string $delimiter = "\t")
    {
        $this->outputJson["data"] = [];
    }

    public function mapName(string $signalName, string $headerAlias = null)
    {
        return;
    }

    public function sendData(float $ts, array $data)
    {
        if($this->jsonTs === null){
            $this->jsonTs = $ts;
        }
        if($this->jsonTs < $ts){
            $this->outputJson["data"][] = $this->data;
            $this->jsonTs = $ts;
            $this->data = [];
        }
        if(empty($this->data)){
            $this->data["timestamp"] = $ts;
        }
        foreach ($data as $key => $item) {
            $regex = '/{\s?pos:\s?\[(\d+\.\d+),(\d+\.\d+),(\d+\.\d+)\],\s?utc_time:\s?(\d+.\d+),\s?pos_mode:\s?(\d+),\s?tracked_sats:\s?(\d+),\s?hdop:\s?(\d+.\d+),\s?corr_age:\s?(\d+.\d+),\ssta_id:\s?(\d+)\s?}/i';

            $replacer = '{"lat": $2, "lng": $1, "alt": $3, "utc_time": $4, "pos_mode": $5, "tracked_sats": $6, "hdop": $7, "corr_age": $8, "sta_id": $9}';

            $item = preg_replace($regex, $replacer, $item);
            $this->data[$key] = json_decode($item);
        }
        return true;
    }

    public function close()
    {
        $this->outputJson["data"][]= $this->data;
        echo json_encode($this->outputJson);
        return true;
    }
}
