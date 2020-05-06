<?php
/**
 * User: Jan Zimmermann
 * Date: 04.05.2020
 * Time: 15:00
 */

namespace Phore\Datalytics\Core\OutputFormat;

use Otic\OticWriter;

class OticOutputFormat implements OutputFormat
{
    private $header = [];
    private $oticWriter;
    private $tempFile;

    public function __construct()
    {
        $this->oticWriter = new OticWriter();
        $this->tempFile = phore_tempfile();
        $this->oticWriter->open($this->tempFile->getFilename());
    }

    public function mapName(string $signalName, string $headerAlias = null): void
    {
        if ($headerAlias === null) {
            $headerAlias = $signalName;
        }
        $this->header[$signalName] = $headerAlias;
    }

    public function sendData(float $ts, array $data): bool
    {
        if (empty($this->header)) {
            throw new \InvalidArgumentException('No SignalNames set');
        }
        foreach ($this->header as $signalName => $alias) {
            if (!array_key_exists($signalName, $data)) {
                throw new \InvalidArgumentException("Data missing for SignalName: '$signalName'");
            }
        }
        foreach ($data as $key => $item) {
            $this->oticWriter->inject($ts, $key, $item, '');
        }
        return true;
    }

    public function sendHttpHeaders(): void
    {
        header('Content-Description: File Transfer');
        header('Content-type:  application/octet-stream; charset=utf-8');
        header('Content-Length: ' . filesize($this->tempFile));
    }

    public function close(): void
    {
        $this->oticWriter->close();
        readfile($this->tempFile->getFilename());
        $this->tempFile->unlink();
    }
}
