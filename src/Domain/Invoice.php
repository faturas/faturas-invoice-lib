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

    /**
     * @var Line[]
     */
    private $invoiceLines;

    /**
     * @var \DateTime
     */
    private $createdAt;

    public function __construct(int $invoiceNumber)
    {
        if (0 === $invoiceNumber) {
            throw new \InvalidArgumentException('Invalid invoiceNumber given!');
        }

        $this->invoiceNumber = $invoiceNumber;

        $this->createdAt = new \DateTime();
        $this->invoiceLines = [];
    }

    /**
     * @param Line $invoiceLine
     * @return $this
     */
    public function addInvoiceLine(Line $invoiceLine)
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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

    /**
     * @return float|int
     */
    public function getTotalAmountWithVAT()
    {
        $totalWithVAT = $this->getTotalAmount() * (1 + ($this->getVATPercentage() / 100));

        return $totalWithVAT;
    }

    /**
     * @return float|int
     */
    public function getVATAmount()
    {
        $totalVAT = $this->getTotalAmount() * ($this->getVATPercentage() / 100);

        return $totalVAT;
    }

}
