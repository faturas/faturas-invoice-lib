<?php
/**
 * Created by PhpStorm.
 * User: patrick
 * Date: 01/08/16
 * Time: 21:53
 */

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
