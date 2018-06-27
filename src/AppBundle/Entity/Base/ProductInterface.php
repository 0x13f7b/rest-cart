<?php

namespace AppBundle\Entity\Base;

interface ProductInterface
{
    /**
     * @param string $name
     *
     * @return ProductInterface
     */
    public function setName(string $name): self;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param int $available
     *
     * @return ProductInterface
     */
    public function setAvailable(int $available): self;

    /**
     * @return int
     */
    public function getAvailable(): int;

    /**
     * @param MoneyInterface $price
     *
     * @return ProductInterface
     */
    public function setPrice(MoneyInterface $price): self;

    /**
     * @return MoneyInterface
     */
    public function getPrice(): MoneyInterface;

    /**
     * @param float $vat
     *
     * @return ProductInterface
     */
    public function setTaxRate(float $vat): self;

    /**
     * @return float
     */
    public function getTaxRate(): float;
}
