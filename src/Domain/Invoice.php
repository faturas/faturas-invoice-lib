<?php

namespace Butler\Invoice\Domain;

use Butler\Invoice\Domain\Invoice\Line;
use Butler\Invoice\Domain\Invoice\PaymentTerm;

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

    /**
     * @var \DateTime
     */
    private $sentAt;

    /**
     * @var PaymentTerm
     */
    private $paymentTerm;

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
     * @return \DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * @return bool
     */
    public function isSent()
    {
        return (null !== $this->sentAt);
    }

    /**
     * @return PaymentTerm
     */
    public function getPaymentTerm()
    {
        return $this->paymentTerm;
    }

    /**
     * @param PaymentTerm $paymentTerm
     * @return $this
     */
    public function setPaymentTerm($paymentTerm)
    {
        $this->paymentTerm = $paymentTerm;

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

    public function send()
    {
        if (count($this->invoiceLines) == 0) {
            throw new \DomainException('Unable to send an invoice without invoice lines');
        }

        $this->sentAt = new \DateTime();
    }

    public function isOverdue(\DateTime $date = null)
    {
        if ($date === null) {
            $date = new \DateTime();
        }
        
        if ($this->getSentAt()->diff($date)->days > $this->getPaymentTerm()->getDays()) {
            return true;
        }

        return false;
    }
}
