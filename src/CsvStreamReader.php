<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 18.12.18
 * Time: 22:02
 */

namespace Phore\Datalytics\Core;


class CsvStreamReader
{

    private $columns = null;
    private $dataCallback = null;


    public function __construct(callable $dataCallback)
    {
        $this->dataCallback = $dataCallback;
    }


    public function message (string $string)
    {
        if ($this->columns === null) {
            $this->columns = explode("\t", $string);
            return true;
        }


        $data = [];
        $inData = explode("\t", $string);
        for($i=0; $i<count($inData); $i++) {
            $data[$this->columns[$i]] = $inData[$i];
        }
        ($this->dataCallback)($data);
        return true;
    }


}
