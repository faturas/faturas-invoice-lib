<?php

namespace Faturas\Invoice\Domain;

use Faturas\Invoice\Domain\Customer\Customer;
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
     * @var \DateTimeInterface
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface
     */
    private $sentAt;

    /**
     * @var PaymentTerm
     */
    private $paymentTerm;

    /**
     * @var Customer
     */
    private $customer;

    public function __construct(int $invoiceNumber)
    {
        if (0 === $invoiceNumber) {
            throw new \InvalidArgumentException('Invalid invoiceNumber given!');
        }

        $this->invoiceNumber = $invoiceNumber;

        $this->createdAt = new \DateTimeImmutable();
        $this->invoiceLines = [];
    }

    /**
     * @param Line $invoiceLine
     * @return Invoice
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
     * @return Invoice
     */
    public function setVATPercentage(float $VATPercentage): Invoice
    {
        $this->VATPercentage = $VATPercentage;

        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getSentAt(): \DateTimeInterface
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
     * @return Invoice
     */
    public function setPaymentTerm($paymentTerm): Invoice
    {
        $this->paymentTerm = $paymentTerm;

        return $this;
    }

    /**
     * @param Customer $customer
     * @return Invoice
     */
    public function setCustomer(Customer $customer) : Invoice
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
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

    /**
     * Send the invoice
     */
    public function send()
    {
        if (count($this->invoiceLines) == 0) {
            throw new \DomainException('Unable to send an invoice without invoice lines');
        }

        $this->sentAt = new \DateTimeImmutable();
    }

    /**
     * @param \DateTimeInterface|null $date
     * @return bool
     */
    public function isOverdue(\DateTimeInterface $date = null): bool
    {
        if ($date === null) {
            $date = new \DateTimeImmutable();
        }

        if ($this->getSentAt()->diff($date)->days > $this->getPaymentTerm()->getDays()) {
            return true;
        }

        return false;
    }
}
