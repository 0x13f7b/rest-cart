<?php

namespace AppBundle\Entity\Base;

use Doctrine\Common\Collections\Collection;

interface CartInterface
{
    /**
     * @param ProductInterface $product
     *
     * @return CartInterface
     */
    public function addProduct(ProductInterface $product): self;

    /**
     * @param ProductInterface $product
     *
     * @return CartInterface
     */
    public function removeProduct(ProductInterface $product): self;

    /**
     * @return Collection
     */
    public function getProducts(): Collection;

    /**
     * @return MoneyInterface
     */
    public function getSubtotal(): MoneyInterface;

    /**
     * @return MoneyInterface
     */
    public function getTaxAmount(): MoneyInterface;

    /**
     * @return MoneyInterface
     */
    public function getTotal(): MoneyInterface;
}
