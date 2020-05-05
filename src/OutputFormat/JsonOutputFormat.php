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
    private $jsonTs;
    private $data = [];

    public function __construct()
    {
        $this->outputJson['data'] = [];
    }

    public function mapName(string $signalName, string $headerAlias = null): void
    {
        return;
    }

    public function sendData(float $ts, array $data): bool
    {
        if ($this->jsonTs === null) {
            $this->jsonTs = $ts;
        }
        if ($this->jsonTs < $ts) {
            $this->outputJson['data'][] = $this->data;
            $this->jsonTs = $ts;
            $this->data = [];
        }
        if (empty($this->data)) {
            $this->data['timestamp'] = $ts;
        }
        foreach ($data as $key => $item) {
            $this->data[$key] = $item;
        }
        return true;
    }

    public function sendHttpHeaders(): void
    {
        header('Content-Type: application/json; charset=utf-8');
    }

    public function close(): bool
    {
        $this->outputJson['data'][] = $this->data;
        echo json_encode($this->outputJson);
        return true;
    }
}
