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

    public function lineAmountsProvider()
    {
        return [
            [ 2, 7.50, 15 ],
            [ 5, 12, 60 ],
            [ 17, 12.50, 212.50]
        ];
    }

}
