<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 24.10.18
 * Time: 09:41
 */

namespace Test;

use InvalidArgumentException;
use Phore\Datalytics\Core\OutputFormat\CsvOutputFormat;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CsvOutputFormatTest extends TestCase
{
    public function testAddHeaderSendData(): void
    {
        system('sudo rm -R /tmp/*');
        $tmp = phore_file('/tmp/testCsvOutput.csv')->fopen('w+');
        $csvOutputFormat = new CsvOutputFormat($tmp);
        $csvOutputFormat->mapName('sigName', 'first');
        $csvOutputFormat->mapName('sigName2', 'second');
        $csvOutputFormat->mapName('sigName3', 'third');
        Assert::assertEquals(true, $csvOutputFormat->sendData('1234',['sigName' =>2, 'sigName2' =>3, 'sigName3' =>4]));
        Assert::assertEquals(true, $csvOutputFormat->sendData('1234',['sigName' =>4, 'sigName2' =>5, 'sigName3' =>6]));
        $csvOutputFormat->close();
        $testFile = phore_file('/tmp/testCsvOutput.csv')->fopen('r');
        $testFile->fclose();
    }

    public function testFooterSend(): void
    {
        system('sudo rm -R /tmp/*');
        $tmp = phore_file('/tmp/testCsvOutput.csv')->fopen('w+');
        $csvOutputFormat = new CsvOutputFormat($tmp, "\t", 'true');
        $csvOutputFormat->mapName('sigName', 'first');
        $csvOutputFormat->mapName('sigName2', 'second');
        $csvOutputFormat->mapName('sigName3', 'third');
        $csvOutputFormat->close();
        $testFile = phore_file('/tmp/testCsvOutput.csv')->fopen('r');
        Assert::assertEquals("ts\tfirst\tsecond\tthird\neof\teof\teof\teof\n", $testFile->fread(1024));
        $testFile->fclose();
    }

    public function testException(): void
    {
        system('sudo rm -R /tmp/*');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Data missing for SignalName: 'test'");
        $tmp = phore_file('/tmp/testCsvOutput.csv')->fopen('w+');
        $csvOutputFormat = new CsvOutputFormat($tmp);
        $csvOutputFormat->mapName('test', 'first');
        Assert::assertEquals(true, $csvOutputFormat->sendData('1234',['sigName' =>2, 'sigName2' =>3, 'sigName3' =>4]));
    }

    public function testNoSignalNameException(): void
    {
        system('sudo rm -R /tmp/*');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No SignalNames set');
        $tmp = phore_file('/tmp/testCsvOutput.csv')->fopen('w+');
        $csvOutputFormat = new CsvOutputFormat($tmp);
        Assert::assertEquals(true, $csvOutputFormat->sendData('1234',['sigName' =>2, 'sigName2' =>3, 'sigName3' =>4]));
    }

    public function testSkipHeaders(): void
    {
        system('sudo rm -R /tmp/*');
        $tmp = phore_file('/tmp/testCsvOutput.csv')->fopen('w+');
        $csvOutputFormat = new CsvOutputFormat($tmp, "\t", true, true);
        $csvOutputFormat->mapName('sigName', 'first');
        $csvOutputFormat->mapName('sigName2', 'second');
        $csvOutputFormat->mapName('sigName3', 'third');
        Assert::assertEquals(true, $csvOutputFormat->sendData('1234',['sigName' =>2, 'sigName2' =>3, 'sigName3' =>4]));
        Assert::assertEquals(true, $csvOutputFormat->sendData('1234',['sigName' =>4, 'sigName2' =>5, 'sigName3' =>6]));
        $csvOutputFormat->close();
        $testFile = phore_file('/tmp/testCsvOutput.csv')->fopen('r');
        Assert::assertEquals("1234\t2\t3\t4\n1234\t4\t5\t6\neof\teof\teof\teof\n", $testFile->fread(1024));
        $testFile->fclose();
    }

}
