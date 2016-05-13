<?php

namespace Butler\Invoice\Domain\Invoice;

/**
 * @author Patrick van Oostrom <patrick@meukinc.nl>
 */
class PaymentTerm
{

    /**
     * @var int
     */
    private $days;

    public function __construct(int $days)
    {
        if ($days < 0) {
            throw new \InvalidArgumentException("Payment term days should not be negative");
        }

        $this->days = $days;
    }

    /**
     * @return int
     */
    public function getDays()
    {
        return $this->days;
    }

}
