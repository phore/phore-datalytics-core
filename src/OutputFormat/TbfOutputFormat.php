<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 27.02.19
 * Time: 11:28
 */

namespace Phore\Datalytics\Core\OutputFormat;


use Phore\FileSystem\FileStream;
use Talpa\BinFmt\V1\TCLDataWriter;
use Talpa\BinFmt\V1\TMachineWriter;

class TbfOutputFormat implements OutputFormat
{

    /**
     * @var TCLDataWriter
     */
    private $writer;

    public function __construct()
    {
        if ( ! class_exists(TCLDataWriter::class))
            throw new \InvalidArgumentException("Missing class TCLDataWriter. Is talpa-binfmt installed?");
        $this->writer = new TCLDataWriter(new FileStream("php://output", "w"));
    }


    public function mapName(string $name, string $alias = null)
    {
        // TODO: Implement mapName() method.
    }

    public function sendData(float $ts, array $data)
    {
        foreach ($data as $name => $val) {
            $this->writer->inject($ts, $name, $val);
        }
    }

    public function sendHttpHeaders()
    {
        header('Content-Type: application/tbf; charset=utf-8');
    }

    public function close()
    {
        $this->writer->close();
    }
}
