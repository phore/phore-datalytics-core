<?php
/**
 * User: Jan Zimmermann
 * Date: 04.05.2020
 * Time: 15:00
 */

namespace Phore\Datalytics\Core\OutputFormat;

use InvalidArgumentException;
use Otic\OticWriter;
use Phore\FileSystem\Exception\FileAccessException;
use Phore\FileSystem\PhoreTempFile;

/**
 * Class OticOutputFormat
 * @package Phore\Datalytics\Core\OutputFormat
 */
class OticOutputFormat implements OutputFormat
{
    /**
     * @var array
     */
    private $header = [];
    /**
     * @var OticWriter
     */
    private $oticWriter;
    /**
     * @var PhoreTempFile
     */
    private $tempFile;

    /**
     * OticOutputFormat constructor.
     */
    public function __construct()
    {
        $this->oticWriter = new OticWriter();
        $this->tempFile = phore_tempfile();
        $this->oticWriter->open($this->tempFile);
    }

    /**
     * maps the signalName and an alias if it is set
     *
     * Example:
     * ```
     * // maps the names 'sigName' and 'sigName1' which has the alias 'firstName'
     * $oticOutputFormat->mapName('sigName');
     * $oticOutputFormat->mapName('sigName1', 'firstName');
     * ```
     *
     * @param string $signalName
     * @param string|null $headerAlias
     * @return void
     */
    public function mapName(string $signalName, string $headerAlias = null): void
    {
        if ($headerAlias === null) {
            $headerAlias = $signalName;
        }
        $this->header[$signalName] = $headerAlias;
    }

    /**
     * fills the otic tempfile with data
     *
     * Example:
     * ```
     * // fills the file with data for the timestamp '1234' and the sensor names 'sigName', 'sigName2', 'sigName3'
     *  $oticOutputFormat = new OticOutputFormat();
     *  $oticOutputFormat->mapName('sigName');
     *  $oticOutputFormat->mapName('sigName2');
     *  $oticOutputFormat->mapName('sigName3');
     *  $oticOutputFormat->sendData('1234', ['sigName' => 2, 'sigName2' => 3, 'sigName3' => 4]);
     * ```
     *
     * @param float $ts
     * @param array $data
     * @throws InvalidArgumentException
     * @return bool returns true if everything worked
     */
    public function sendData(float $ts, array $data): bool
    {
        if (empty($this->header)) {
            throw new InvalidArgumentException('No SignalNames set');
        }
        foreach ($this->header as $signalName => $alias) {
            if (!array_key_exists($signalName, $data)) {
                throw new InvalidArgumentException("Data missing for SignalName: '$signalName'");
            }
        }
        foreach ($data as $key => $item) {
            $this->oticWriter->inject($ts, $key, $item, '');
        }
        return true;
    }

    /**
     *  sets additional http headers for a file-download
     * @return void
     */
    public function sendHttpHeaders(): void
    {
        header('Content-Description: File Transfer');
        header('Content-type:  application/octet-stream; charset=utf-8');
        header('Content-Length: ' . $this->tempFile->getFilesize());
    }

    /**
     * closes the otic-tempfile and sends it through the output
     *
     * @throws FileAccessException
     * @return void
     */
    public function close(): void
    {
        $this->oticWriter->close();
        readfile($this->tempFile);
        $this->tempFile->unlink();
    }
}
