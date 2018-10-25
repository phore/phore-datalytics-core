<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 16.10.18
 * Time: 16:03
 */

namespace Phore\Datalytics\Core\OutputFormat;


interface OutputFormat
{

    public function sendData(float $ts, array $data);

    public function close();

}
