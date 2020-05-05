<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 23.10.18
 * Time: 14:53
 */

namespace Phore\Datalytics\Core\OutputFormat;


use Phore\FileSystem\Exception\FileAccessException;
use Phore\FileSystem\FileStream;

/**
 * Class CsvEventOutputFormat
 * @package Phore\Datalytics\Core\OutputFormat
 */
class CsvEventOutputFormat implements OutputFormat
{

    private $outputHandler;
    private $delimiter;
    private $header = [];
    private $eof;

    /**
     * CsvEventOutputFormat constructor.
     * @param FileStream|null $res
     * @param string $delimiter
     * @param bool $eof
     */

    public function __construct(FileStream $res = null, string $delimiter = "\t", bool $eof = false)
    {
        if ($res === null) {
            $res = phore_file('php://output')->fopen('w');
        }
        $this->outputHandler = $res;
        $this->delimiter = $delimiter;
        $this->eof = $eof;
    }

    private function _ensureFooterSend(): void
    {
        if (!$this->eof) {
            return;
        }
        $this->outputHandler->fputcsv(array(0 => 'eof', 1 => 'eof', 2 => 'eof'), $this->delimiter);
    }

    /**
     * @param string $signalName
     * @param string|null $headerAlias
     */
    public function mapName(string $signalName, string $headerAlias = null): void
    {
        if ($headerAlias === null) {
            $headerAlias = $signalName;
        }
        $this->header[$signalName] = $headerAlias;
    }

    /**
     * @param float $ts
     * @param array $data
     * @return bool
     * @throws FileAccessException
     */
    public function sendData(float $ts, array $data): bool
    {
        $arr[0] = $ts;
        foreach ($data as $key => $item) {
            $arr[1] = $key;
            $arr[2] = $item;
            $this->outputHandler->fputcsv($arr, $this->delimiter);
        }
        return true;
    }

    /**
     *
     */
    public function sendHttpHeaders(): void
    {
        header('Content-type: text/csv; charset=utf-8');
    }

    /**
     * @throws FileAccessException
     */
    public function close(): void
    {
        $this->_ensureFooterSend();
        $this->outputHandler->fclose();
    }
}
