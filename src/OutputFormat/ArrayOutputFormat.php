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
    public $isClosed = false;
    
    public function sendData(float $ts, array $data)
    {
        $row = ["ts" => $ts];
        $row += $data;
        $this->data[] = $row;
    }

    public function close()
    {
        $this->isClosed = true;
    }

    public function mapName(string $name, string $alias = null)
    {
        // TODO: Implement mapName() method.
    }
}
