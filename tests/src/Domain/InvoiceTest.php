<?php

namespace Faturas\Test\Invoice\Domain;

use Faturas\Invoice\Domain\Customer\Customer;
use Faturas\Invoice\Domain\Invoice;
use Faturas\Invoice\Domain\Invoice\PaymentTerm;
use ReflectionProperty;

class InvoiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function throwExceptionOnZeroInvoiceNumber()
    {
        new Invoice(0);
    }

    /**
     * @test
     */
    function addInvoiceLineToInvoice()
    {
        $invoice = new Invoice(12345);
        $invoiceLine = new Invoice\Line(1,1);

        $invoice->addInvoiceLine($invoiceLine);

        $this->assertContains($invoiceLine, $invoice->getInvoiceLines());
    }

    /**
     * @test
     * @dataProvider getInvoiceLineStack
     */
    public function calculateTotalInvoiceAmountWithoutVAT($invoiceLines, $VAT, $total)
    {
        $invoice = new Invoice(1);
        foreach ($invoiceLines as $invoiceLine) {
            $invoice->addInvoiceLine($invoiceLine);
        }

        $this->assertEquals($invoice->getTotalAmount(), $total['total']);
    }

    /**
     * @test
     * @dataProvider getInvoiceLineStack
     */
    public function calculcateTotalInvoiceAmountWithVAT($invoiceLines, $VAT, $total)
    {
        $invoice = new Invoice(1);
        foreach ($invoiceLines as $invoiceLine) {
            $invoice->addInvoiceLine($invoiceLine);
        }
        $invoice->setVATPercentage($VAT);
        $this->assertEquals($invoice->getTotalAmountWithVAT(), $total['totalVAT']);

    }

    /**
     * @test
     * @dataProvider getInvoiceLineStack
     */
    public function calculateVATAmount($invoiceLines, $VAT, $total)
    {
        $invoice = new Invoice(1);
        foreach ($invoiceLines as $invoiceLine) {
            $invoice->addInvoiceLine($invoiceLine);
        }
        $invoice->setVATPercentage($VAT);
        $this->assertEquals($invoice->getVATAmount(), $total['VAT']);
    }

    /**
     * @test
     */
    public function invoiceCreatedAt()
    {
        $dateTime = new \DateTime();
        $invoice = new Invoice(1);

        $this->assertEquals(
            $invoice->getCreatedAt()->getTimestamp(),
            $dateTime->getTimestamp()
        );
    }

    /**
     * @test
     * @expectedException \DomainException
     */
    public function sendInvoiceWithoutLines()
    {
        $invoice = new Invoice(1);
        $invoice->send();
    }

    /**
     * @test
     * @dataProvider getInvoiceLineStack
     */
    public function checkIfInvoiceIsSent($invoiceLines)
    {
        $invoice = new Invoice(1);

        foreach ($invoiceLines as $invoiceLine) {
            $invoice->addInvoiceLine($invoiceLine);
        }

        $invoice->send();

        $this->assertTrue($invoice->isSent());
    }

    /**
     * @test
     * @dataProvider getInvoiceLineStack
     */
    public function checkInvoiceSentAtIsSet($invoiceLines)
    {
        $dateTime = new \DateTime();
        $invoice = new Invoice(1);

        foreach ($invoiceLines as $invoiceLine) {
            $invoice->addInvoiceLine($invoiceLine);
        }

        $invoice->send();

        $this->assertEquals(
            $invoice->getSentAt()->getTimestamp(),
            $dateTime->getTimestamp()
        );
    }

    /**
     * @test
     */
    public function checkIfInvoiceIsOverdueWithoutDate()
    {
        $invoice = new Invoice(1);
        $invoice->addInvoiceLine(new Invoice\Line(2, 4));
        $invoice->send();

        $invoice->setPaymentTerm(new PaymentTerm(21));
        $this->assertEquals(false, $invoice->isOverdue());
    }

    /**
     * @test
     * @dataProvider getPaymentTermProvider
     */
    public function checkIfInvoiceIsOverdue(PaymentTerm $paymentTerm, \DateTime $invoiceDate, \DateTime $currentDateMock, bool $overdue)
    {
        $invoice = new Invoice(1);

        // Little whiteboxing, shouldn't hurt right?
        $property = new ReflectionProperty(Invoice::class, 'sentAt');
        $property->setAccessible(true);
        $property->setValue($invoice, $invoiceDate);

        $invoice->setPaymentTerm($paymentTerm);
        $this->assertEquals($overdue, $invoice->isOverdue($currentDateMock));
    }

    /**
     * @test
     */
    public function testCustomerSetter()
    {
        $customer = \Mockery::mock(Customer::class)
            ->shouldReceive('getEmail')->once()->andReturn('billing@media-butler.nl')
            ->getMock()
            ;

        $invoice = new Invoice(1);
        $invoice->setCustomer($customer);
    }

    public function testCustomerGetter()
    {
        $customer = \Mockery::mock(Customer::class)
            ->shouldReceive('getEmail')->once()->andReturn('billing@media-butler.nl')
            ->getMock()
        ;

        $invoice = new Invoice(1);
        $invoice->setCustomer($customer);
        $this->assertEquals('billing@media-butler.nl', $invoice->getCustomer()->getEmail());
    }

    public function getInvoiceLineStack()
    {
        return [
            [
                [
                    new Invoice\Line(2, 15),
                    new Invoice\Line(5, 52),
                    new Invoice\Line(6, 2.50),
                    new Invoice\Line(3.50, 6),
                ],
                21,
                [
                    'total' => 326,
                    'totalVAT' => 394.46,
                    'VAT' => 68.46
                ]
            ],
            [
                [
                    new Invoice\Line(6, 15),
                    new Invoice\Line(5, 52),
                    new Invoice\Line(6, -2.50),
                    new Invoice\Line(0.5, 600),
                ],
                32,
                [
                    'total' => 635,
                    'totalVAT' => 838.2,
                    'VAT' => 203.2
                ]
            ],
            [
                [
                    new Invoice\Line(6, 15),
                    new Invoice\Line(15, 52),
                    new Invoice\Line(36, 2.50),
                    new Invoice\Line(3, 600),
                ],
                44,
                [
                    'total' => 2760,
                    'totalVAT' => 3974.4,
                    'VAT' => 1214.4
                ]
            ]
        ];
    }

    public function getPaymentTermProvider()
    {
        return [
            [ new PaymentTerm(30), \DateTime::createFromFormat('Y-m-d', '2012-02-12'), \DateTime::createFromFormat('Y-m-d', '2012-06-12'), true ],
            [ new PaymentTerm(14), \DateTime::createFromFormat('Y-m-d', '2014-06-12'), \DateTime::createFromFormat('Y-m-d', '2014-06-22'), false ],
            [ new PaymentTerm(30), \DateTime::createFromFormat('Y-m-d', '2011-02-22'), \DateTime::createFromFormat('Y-m-d', '2011-03-24'), false ],
            [ new PaymentTerm(7),  \DateTime::createFromFormat('Y-m-d', '2015-11-22'), \DateTime::createFromFormat('Y-m-d', '2016-01-22'), true ],
            [ new PaymentTerm(21), \DateTime::createFromFormat('Y-m-d', '2017-06-12'), \DateTime::createFromFormat('Y-m-d', '2015-06-12'), true ]
        ];
    }
}
