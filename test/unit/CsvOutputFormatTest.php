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
        system("sudo rm -R /tmp/*");
        $tmp = phore_file("/tmp/testCsvOutput.csv")->fopen("w+");
        $csvOutputFormat = new CsvOutputFormat($tmp);
        $csvOutputFormat->mapName("neu","erstes");
        $csvOutputFormat->mapName("neu2","zweites");
        $csvOutputFormat->mapName("neu3","drittes");
        Assert::assertEquals(true, $csvOutputFormat->sendData("1234",["neu"=>2, "neu2"=>3, "neu3" =>4]));
        Assert::assertEquals(true, $csvOutputFormat->sendData("1234",["neu"=>4, "neu2"=>5, "neu3" =>6]));
        $csvOutputFormat->close();
        $testFile = phore_file("/tmp/testCsvOutput.csv")->fopen("r");
        $testFile->fclose();
    }

    public function testFooterSend(): void
    {
        system("sudo rm -R /tmp/*");
        $tmp = phore_file("/tmp/testCsvOutput.csv")->fopen("w+");
        $csvOutputFormat = new CsvOutputFormat($tmp, "\t", "true");
        $csvOutputFormat->mapName("neu","erstes");
        $csvOutputFormat->mapName("neu2","zweites");
        $csvOutputFormat->mapName("neu3","drittes");
        $csvOutputFormat->close();
        $testFile = phore_file("/tmp/testCsvOutput.csv")->fopen("r");
        Assert::assertEquals("ts\terstes\tzweites\tdrittes\neof\teof\teof\teof\n", $testFile->fread(1024));
        $testFile->fclose();
    }

    public function testException(): void
    {
        system("sudo rm -R /tmp/*");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Data missing for SignalName: 'test'");
        $tmp = phore_file("/tmp/testCsvOutput.csv")->fopen("w+");
        $csvOutputFormat = new CsvOutputFormat($tmp);
        $csvOutputFormat->mapName("test", "erster");
        Assert::assertEquals(true, $csvOutputFormat->sendData("1234",["neu"=>2, "neu2"=>3, "neu3" =>4]));
    }

    public function testNoSignalNameException(): void
    {
        system("sudo rm -R /tmp/*");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("No SignalNames set");
        $tmp = phore_file("/tmp/testCsvOutput.csv")->fopen("w+");
        $csvOutputFormat = new CsvOutputFormat($tmp);
        Assert::assertEquals(true, $csvOutputFormat->sendData("1234",["neu"=>2, "neu2"=>3, "neu3" =>4]));
    }

    public function testSkipHeaders(): void
    {
        system("sudo rm -R /tmp/*");
        $tmp = phore_file("/tmp/testCsvOutput.csv")->fopen("w+");
        $csvOutputFormat = new CsvOutputFormat($tmp, "\t", true, true);
        $csvOutputFormat->mapName("neu","erstes");
        $csvOutputFormat->mapName("neu2","zweites");
        $csvOutputFormat->mapName("neu3","drittes");
        Assert::assertEquals(true, $csvOutputFormat->sendData("1234",["neu"=>2, "neu2"=>3, "neu3" =>4]));
        Assert::assertEquals(true, $csvOutputFormat->sendData("1234",["neu"=>4, "neu2"=>5, "neu3" =>6]));
        $csvOutputFormat->close();
        $testFile = phore_file("/tmp/testCsvOutput.csv")->fopen("r");
        Assert::assertEquals("1234\t2\t3\t4\n1234\t4\t5\t6\neof\teof\teof\teof\n", $testFile->fread(1024));
        $testFile->fclose();
    }

}