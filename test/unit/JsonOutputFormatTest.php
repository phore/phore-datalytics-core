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
    public function testOutput(): void
    {
        $OutputFormat = new JsonOutputFormat();
        $OutputFormat->sendData(1543944000, ['engine_override_control_mode' => 1]);
        $OutputFormat->sendData(1543944000, ['engine_torque_mode' => 1]);
        $OutputFormat->sendData(1543944001, ['engine_override_control_mode' => 2]);
        $OutputFormat->sendData(1543944001, ['engine_torque_mode' => 1]);
        $OutputFormat->sendData(1543944002, ['engine_torque_mode' => '{ pos: [8.382391,51.326022,563.490000], utc_time: 39261.000000, pos_mode: 5, tracked_sats: 12, hdop: 0.930000, corr_age: 21.000000, sta_id:111}']);
        $OutputFormat->sendData(1543944003, ['engine_torque_mode' => 1]);
        $this->expectOutputString('{"data":[{"timestamp":1543944000,"engine_override_control_mode":1,"engine_torque_mode":1},{"timestamp":1543944001,"engine_override_control_mode":2,"engine_torque_mode":1},{"timestamp":1543944002,"engine_torque_mode":"{ pos: [8.382391,51.326022,563.490000], utc_time: 39261.000000, pos_mode: 5, tracked_sats: 12, hdop: 0.930000, corr_age: 21.000000, sta_id:111}"},{"timestamp":1543944003,"engine_torque_mode":1}]}');
        $this->assertTrue($OutputFormat->close());
    }

}
