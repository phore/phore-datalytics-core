<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 07.12.18
 * Time: 09:56
 */

namespace Test;

use Phore\Datalytics\Core\OutputFormat\JsonOutputFormat;
use PHPUnit\Framework\TestCase;


class JsonOutputFormatTest extends TestCase
{
    public function testOutput()
    {
        $OutputFormat = new JsonOutputFormat();
        $OutputFormat->sendData(1543944000, ["engine_override_control_mode" => 1]);
        $OutputFormat->sendData(1543944000, ["engine_torque_mode" => 1]);
        $OutputFormat->sendData(1543944001, ["engine_override_control_mode" => 2]);
        $OutputFormat->sendData(1543944001, ["engine_torque_mode" => 1]);
        $this->assertTrue($OutputFormat->close());
    }

}
