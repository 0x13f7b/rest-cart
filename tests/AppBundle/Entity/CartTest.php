<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Money;
use AppBundle\Entity\Product;
use PHPUnit_Framework_TestCase;

class CartTest extends PHPUnit_Framework_TestCase
{
    public function testCartProducts()
    {
        // Check if new cart is empty (doesn't contain any product)
        $cart = new Cart();
        $this->assertEquals(0, $cart->getProducts()->count());

        // Add first product to cart
        $priceA = new Money();
        $priceA->setAmount(10);
        $priceA->setDecimals(60);

        $productA = new Product();
        $productA->setPrice($priceA);
        $productA->setTaxRate(0.5);

        $cart->addProduct($productA);

        // Check if cart subtotal is correct
        $this->assertEquals(10, $cart->getSubtotal()->getAmount());
        $this->assertEquals(60, $cart->getSubtotal()->getDecimals());

        // Check if cart VAT amount is correct
        $this->assertEquals(5, $cart->getTaxAmount()->getAmount());
        $this->assertEquals(30, $cart->getTaxAmount()->getDecimals());

        // Check if cart total is correct
        $this->assertEquals(15, $cart->getTotal()->getAmount());
        $this->assertEquals(90, $cart->getTotal()->getDecimals());

        // Add another product to cart
        $priceB = new Money();
        $priceB->setAmount(1);
        $priceB->setDecimals(50);

        $productB = new Product();
        $productB->setPrice($priceB);
        $productB->setTaxRate(0.2);

        $cart->addProduct($productB);

        // Check if cart subtotal is still correct
        $this->assertEquals(12, $cart->getSubtotal()->getAmount());
        $this->assertEquals(10, $cart->getSubtotal()->getDecimals());

        // Check if cart VAT amount is still correct
        $this->assertEquals(5, $cart->getTaxAmount()->getAmount());
        $this->assertEquals(60, $cart->getTaxAmount()->getDecimals());

        // Check if cart total is still correct
        $this->assertEquals(17, $cart->getTotal()->getAmount());
        $this->assertEquals(70, $cart->getTotal()->getDecimals());
    }
}
