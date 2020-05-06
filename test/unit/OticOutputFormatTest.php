<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 24.10.18
 * Time: 09:41
 */

namespace Test;

use Otic\OticReader;
use Otic\OticWriter;
use Phore\Datalytics\Core\OutputFormat\OticOutputFormat;
use PHPUnit\Framework\TestCase;

class OticOutputFormatTest extends TestCase
{
    protected function setUp(): void
    {
        system('sudo rm -R /tmp/*');
    }

    protected function tearDown(): void
    {
        system('sudo rm -R /tmp/*');
    }

    public function testOticOutputFormat(): void
    {
        $oticOutputFormat = new OticOutputFormat();
        $oticOutputFormat->mapName('sigName', 'first');
        $oticOutputFormat->mapName('sigName2', 'second');
        $oticOutputFormat->mapName('sigName3', 'third');
        $oticOutputFormat->sendData('1234', ['sigName' => 2, 'sigName2' => 3, 'sigName3' => 4]);

        $oticWriter = new OticWriter();
        $oticWriter->open($file = phore_file('/tmp/test.otic'));
        $oticWriter->inject('1234', 'sigName', 2, 'test');
        $oticWriter->inject('1234', 'sigName2', 3, 'test');
        $oticWriter->inject('1234', 'sigName3', 4, 'test');
        $oticWriter->close();
        $contents = $file->get_contents();
        $this->expectOutputString($contents);

        $oticOutputFormat->close();
    }

}
