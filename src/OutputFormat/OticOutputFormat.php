<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 16.10.18
 * Time: 16:06
 */

namespace Phore\Datalytics\Core\OutputFormat;


use Otic\OticWriter;

class OticOutputFormat implements OutputFormat
{
    private $header = [];
    private $oticWriter;
    private $tempfile;

    public function __construct()
    {
        $this->oticWriter = new OticWriter();
        $this->tempfile = phore_tempfile();
        $this->oticWriter->open($this->tempfile->getFilename());
    }

    public function mapName(string $signalName, string $headerAlias = null): void
    {
        if ($headerAlias === null) {
            $headerAlias = $signalName;
        }
        $this->header[$signalName] = $headerAlias;
    }

    public function sendData(float $ts, array $data, $unit = null): bool
    {
        if (empty($this->header)) {
            throw new \InvalidArgumentException('No SignalNames set');
        }
        foreach ($this->header as $signalName => $alias) {
            if (!array_key_exists($signalName, $data)) {
                throw new \InvalidArgumentException("Data missing for SignalName: '$signalName'");
            }
        }

        $this->oticWriter->inject($ts, current($data), $data[0], $unit);

        return true;
    }

    public function sendHttpHeaders(): void
    {
        header('Content-Description: File Transfer');
        header('Content-type:  application/octet-stream; charset=utf-8');
        header('Content-Length: ' . filesize($this->tempfile));
    }

    public function close(): void
    {
        $this->oticWriter->close();
        readfile($this->tempfile->getFilename());
        $this->tempfile->unlink();
    }
}
