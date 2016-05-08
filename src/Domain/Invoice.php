<?php

namespace Butler\Invoice\Domain;

use Butler\Invoice\Domain\Invoice\Line;

/**
 * @author Patrick van Oostrom <patrick.van.oostrom@freshheads.com>
 */
class Invoice
{

    /**
     * @var integer
     */
    private $invoiceNumber;

    /**
     * @var float
     */
    private $VATPercentage;

    public function __construct(int $invoiceNumber)
    {
        if (0 === $invoiceNumber) {
            throw new \InvalidArgumentException('Invalid invoiceNumber given!');
        }

        $this->invoiceNumber = $invoiceNumber;

        $this->invoiceLines = [];
    }

    public function addInvoiceLine(Invoice\Line $invoiceLine)
    {
        $this->invoiceLines[] = $invoiceLine;

        return $this;
    }

    /**
     * @return Line[]
     */
    public function getInvoiceLines()
    {
        return $this->invoiceLines;
    }

    /**
     * @return float
     */
    public function getVATPercentage()
    {
        return $this->VATPercentage;
    }

    /**
     * @param float $VATPercentage
     * @return $this
     */
    public function setVATPercentage(float $VATPercentage)
    {
        $this->VATPercentage = $VATPercentage;

        return $this;
    }

    /**
     * @return float|int
     */
    public function getTotalAmount()
    {
        $total = 0;

        foreach ($this->getInvoiceLines() as $invoiceLine) {
            $total += $invoiceLine->getLineTotal();
        }

        return $total;
    }

    public function getTotalAmountWithVAT()
    {
        $totalWithVAT = $this->getTotalAmount() * (1 + ($this->getVATPercentage() / 100));
        
        return $totalWithVAT;
    }
}
