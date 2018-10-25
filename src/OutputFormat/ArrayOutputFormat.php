<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 18.10.18
 * Time: 11:40
 */

namespace Phore\Datalytics\Core\OutputFormat;


use Phore\Datalytics\Core\OutputFormat\OutputFormat;

class ArrayOutputFormat implements OutputFormat
{

    public $data = [];

    
    public function sendData(float $ts, array $data)
    {
        $this->data["$ts"] = $data;
    }

    public function close()
    {
        // TODO: Implement close() method.
    }
}
