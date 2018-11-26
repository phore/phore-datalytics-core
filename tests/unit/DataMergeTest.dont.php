<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 12.11.18
 * Time: 16:51
 */

namespace Test;


use PHPUnit\Framework\TestCase;

class dont extends TestCase
{


    public function testDataMergeWithMultipleDataSources()
    {
        $source1 = new DataSource();
        $source2 = new DataSource();

        $merge = new DataMerge();
        $merge->addSource($source1);
        $merge->addSource($source2);


        


    }


}
