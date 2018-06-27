<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Money;
use PHPUnit_Framework_TestCase;

class MoneyTest extends PHPUnit_Framework_TestCase
{
    public function testMoney()
    {
        // Check if new Money instance contains 0 euro, 0 cents
        $money = new Money();
        $this->assertEquals(0, $money->getAmount());
        $this->assertEquals(0, $money->getDecimals());
        $this->assertEquals(0, $money->getTotalAmount());

        $money->setAmount(17);
        $money->setDecimals(54);

        // Check if money instance contains correct values for euros and cents
        $this->assertEquals(17, $money->getAmount());
        $this->assertEquals(54, $money->getDecimals());
        $this->assertEquals(1754, $money->getTotalAmount());
    }
}
