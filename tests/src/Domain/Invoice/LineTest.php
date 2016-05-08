<?php

namespace Butler\Test\Invoice\Domain\Invoice\Line;

use Butler\Invoice\Domain\Invoice\Line;

class LineTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider lineAmountsProvider
     * @test
     */
    public function calculateLineTotal($amount, $price, $total)
    {
        $line = new Line($amount, $price);
        $this->assertEquals($line->getLineTotal(), $total);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function setEmptyLineDescription()
    {
        $line = new Line(1, 1);
        $line->setDescription('');
    }

    /**
     * @test
     * @dataProvider descriptionProvider
     */
    public function setAndGetDescription($description)
    {
        $line = new Line(1,1);
        $line->setDescription($description);

        $this->assertEquals($line->getDescription(), $description);
    }


    /**
     * @test
     * @dataProvider summaryProvider
     */
    public function setAndGetSummary($summary)
    {
        $line = new Line(1,1);
        $line->setSummary($summary);

        $this->assertEquals($line->getSummary(), $summary);
    }

    public function lineAmountsProvider()
    {
        return [
            [ 2, 7.50, 15 ],
            [ 5, 12, 60 ],
            [ 17, 12.50, 212.50]
        ];
    }

    public function descriptionProvider()
    {
        return [
            ['Setting up API Client, wrote documentation'],
            ['Agile coaching and support, story writing'],
            ['Server maintenance, installed new packages and updates']
        ];
    }

    public function summaryProvider()
    {
        return [
            ['API Development'],
            ['Coaching'],
            ['Server Maintenance']
        ];
    }
}
