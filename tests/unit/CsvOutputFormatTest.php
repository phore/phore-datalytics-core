<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 24.10.18
 * Time: 09:41
 */

namespace Test;

use Phore\Datalytics\Core\OutputFormat\CsvEventOutputFormat;
use Phore\Datalytics\Core\OutputFormat\CsvOutputFormat;
use Phore\FileSystem\PhoreFile;
use Phore\FileSystem\PhoreTempFile;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CsvOutputFormatTest extends TestCase
{
    public function testAddHeaderSendData()
    {
        $tmp = phore_file("/tmp/testCsvOutput.csv")->fopen("w+");
        $csvOutputFormat = new CsvOutputFormat($tmp);
        $csvOutputFormat->mapName("erstes");
        $csvOutputFormat->mapName("zweites");
        $csvOutputFormat->mapName("drittes");
        Assert::assertEquals(true, $csvOutputFormat->sendData("1234",["neu"=>2, "neu2"=>3, "neu3" =>4]));
        Assert::assertEquals(true, $csvOutputFormat->sendData("1234",["neu"=>4, "neu2"=>5, "neu3" =>6]));
        $csvOutputFormat->close();
        $testFile = phore_file("/tmp/testCsvOutput.csv")->fopen("r");
        Assert::assertEquals("ts\terstes\tzweites\tdrittes\n1234\t2\t3\t4\n1234\t4\t5\t6\n", $testFile->fread(1024));
        $testFile->fclose();
    }
}
