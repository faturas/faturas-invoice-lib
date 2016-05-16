<?php

namespace Faturas\Test\Invoice\Domain\Invoice\Line;

use Faturas\Invoice\Domain\Invoice\PaymentTerm;

class PaymentTermTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function paymentTermShouldNotBeNegative()
    {
        new PaymentTerm(-2);
    }

    /**
     * @test
     * @dataProvider paymentTermProvider
     */
    public function getDaysAfterConstruct($days)
    {
        $this->assertEquals($days, (new PaymentTerm($days))->getDays());
    }

    public function paymentTermProvider()
    {
        return [
            [ 2 ],
            [ 21 ],
            [ 14 ],
            [ 30 ],
            [ 0 ]
        ];
    }
}
