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

    /**
     * @var string
     */
    private $summary;

    /**
     * @var string
     */
    private $description;

    public function __construct(float $amount, float $price)
    {
        $this->amount = $amount;
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description): Line
    {
        if ($description == '') {
            throw new \InvalidArgumentException('An invoice line needs a description');
        }
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     * @return $this
     */
    public function setSummary($summary): Line
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return float
     */
    public function getLineTotal(): float
    {
        return $this->amount * $this->price;
    }
}
