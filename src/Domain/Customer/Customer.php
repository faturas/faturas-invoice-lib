<?php

namespace Faturas\Invoice\Domain\Customer;

interface Customer
{

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getName();

}
