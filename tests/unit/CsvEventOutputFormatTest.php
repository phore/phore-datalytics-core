<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 24.10.18
 * Time: 12:33
 */

namespace Test;

use Phore\Datalytics\Core\OutputFormat\CsvEventOutputFormat;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CsvEventOutputFormatTest extends TestCase
{
    public function testSendDataClose()
    {
        system("sudo rm -R /tmp/*");
        $tmp = phore_file("/tmp/testCsvEventOutput.csv")->fopen("w+");
        $csvEventOutputFormat = new CsvEventOutputFormat($tmp);
        $csvEventOutputFormat->mapName("sig1");
        $csvEventOutputFormat->mapName("sig2");
        $csvEventOutputFormat->mapName("sig3");
        Assert::assertEquals(true, $csvEventOutputFormat->sendData("1234",["sig1"=>2, "sig2"=>3, "sig3" =>4]));
        Assert::assertEquals(true, $csvEventOutputFormat->sendData("1234",["sig1"=>5, "sig2"=>6, "sig3" =>7]));
        $testFile = phore_file("/tmp/testCsvEventOutput.csv")->fopen("r");
        Assert::assertEquals("1234\tsig1\t2\n1234\tsig2\t3\n1234\tsig3\t4\n1234\tsig1\t5\n1234\tsig2\t6\n1234\tsig3\t7\n", $testFile->fread(1024));
        $testFile->fclose();
    }

    public function testFooterSend()
    {
        system("sudo rm -R /tmp/*");
        $tmp = phore_file("/tmp/testCsvOutput.csv")->fopen("w+");
        $csvEventOutputFormat = new CsvEventOutputFormat($tmp, "\t", true);
        $csvEventOutputFormat->mapName("sig1");
        $csvEventOutputFormat->mapName("sig2");
        $csvEventOutputFormat->mapName("sig3");
        $csvEventOutputFormat->sendData("1234",["sig1"=>2, "sig2"=>3, "sig3" =>4]);
        $csvEventOutputFormat->sendData("1234",["sig1"=>5, "sig2"=>6, "sig3" =>7]);
        $csvEventOutputFormat->close();
        $testFile = phore_file("/tmp/testCsvOutput.csv")->fopen("r");
        Assert::assertEquals("1234\tsig1\t2\n1234\tsig2\t3\n1234\tsig3\t4\n1234\tsig1\t5\n1234\tsig2\t6\n1234\tsig3\t7\neof\teof\teof\n", $testFile->fread(1024));
        $testFile->fclose();
    }
}
