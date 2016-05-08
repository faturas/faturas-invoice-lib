<?php

namespace Butler\Invoice\Domain\Invoice;

/**
 * @author Patrick van Oostrom <patrick.van.oostrom@freshheads.com>
 */
class Line
{
    /**
     * @var float
     */
    private $amount;

    /**
     * @var float
     */
    private $price;

    public function __construct(float $amount, float $price)
    {
        $this->amount = $amount;
        $this->price = $price;
    }

    public function getLineTotal()
    {
        return $this->amount * $this->price;
    }
}
