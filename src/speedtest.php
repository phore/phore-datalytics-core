<?php
/**
 * Created by IntelliJ IDEA.
 * User: oem
 * Date: 24.07.19
 * Time: 11:41
 */


$startpoint = 1560384000;
$endpoint = 1560387000;

echo "start time\n";

$starttime = time();
$i = 0;
while($startpoint < $endpoint){
    $startpoint = bcadd($startpoint, '0.001', 5);
    $i++;
}

$endtime = time();
$timeneeded = $endtime - $starttime;

echo "bcmath =  " . $timeneeded . " Sekunden Additionen: " . $i . "\n";

$starttime = time();

$startpoint = 1560384000;

$i = 0;
while($startpoint < $endpoint){
    $startpoint += 0.001;
    $i++;
}

$endtime = time();

$timeneeded = $endtime - $starttime;

echo "float addition =  " . $timeneeded . " Sekunden Additionen: " . $i . "\n";
