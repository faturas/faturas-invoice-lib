<?php

namespace Faturas\Invoice\Domain;

use Faturas\Invoice\Domain\Invoice\Line;
use Faturas\Invoice\Domain\Invoice\PaymentTerm;

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
    public function addInvoiceLine(Line $invoiceLine): Invoice
    {
        $this->invoiceLines[] = $invoiceLine;

        return $this;
    }

    /**
     * @return Line[]
     */
    public function getInvoiceLines(): array
    {
        return $this->invoiceLines;
    }

    /**
     * @return float
     */
    public function getVATPercentage(): float
    {
        return $this->VATPercentage;
    }

    /**
     * @param float $VATPercentage
     * @return $this
     */
    public function setVATPercentage(float $VATPercentage): Invoice
    {
        $this->VATPercentage = $VATPercentage;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getSentAt(): \DateTime
    {
        return $this->sentAt;
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return (null !== $this->sentAt);
    }

    /**
     * @return PaymentTerm
     */
    public function getPaymentTerm(): PaymentTerm
    {
        return $this->paymentTerm;
    }

    /**
     * @param PaymentTerm $paymentTerm
     * @return $this
     */
    public function setPaymentTerm($paymentTerm): Invoice
    {
        $this->paymentTerm = $paymentTerm;

        return $this;
    }

    /**
     * @return float|int
     */
    public function getTotalAmount(): float
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
    public function getTotalAmountWithVAT(): float
    {
        $totalWithVAT = $this->getTotalAmount() * (1 + ($this->getVATPercentage() / 100));

        return $totalWithVAT;
    }

    /**
     * @return float|int
     */
    public function getVATAmount(): float
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

    public function isOverdue(\DateTime $date = null): bool
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
