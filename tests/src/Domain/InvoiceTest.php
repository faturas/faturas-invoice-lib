<?php

namespace Butler\Test\Invoice\Domain;

use Butler\Invoice\Domain\Invoice;

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
        $invoice = new Invoice(1);

        $this->assertNotNull($invoice->getCreatedAt());
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

}
