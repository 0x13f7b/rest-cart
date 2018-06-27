<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Money;
use AppBundle\Entity\Product;
use PHPUnit_Framework_TestCase;

class ProductTest extends PHPUnit_Framework_TestCase
{
    public function testProduct()
    {
        // Check if new product contains correct values
        $product = new Product();
        $product->setName('testName1');

        $price = new Money();
        $price->setAmount(14);
        $price->setDecimals(56);

        $product->setPrice($price);
        $product->setAvailable(5);
        $product->setTaxRate(0.5);

        $this->assertEquals('testName1', $product->getName());
        $this->assertEquals(5, $product->getAvailable());
        $this->assertEquals(0.5, $product->getTaxRate());
        $this->assertEquals(14, $product->getPrice()->getAmount());
        $this->assertEquals(56, $product->getPrice()->getDecimals());
        $this->assertEquals(2184, $product->getTotalInDecimals());
        $this->assertEquals(728, $product->getTaxAmountInDecimals());
    }
}
