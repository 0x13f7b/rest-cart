<?php

namespace AppBundle\Entity\Base;

use Doctrine\Common\Collections\ArrayCollection;

interface StockInterface
{
    /**
     * @param ProductInterface $product
     *
     * @return StockInterface
     */
    public function addProduct(ProductInterface $product): self;

    /**
     * @param ProductInterface $product
     *
     * @return StockInterface
     */
    public function removeProduct(ProductInterface $product): self;

    /**
     * @return ArrayCollection
     */
    public function getProducts(): ArrayCollection;
}
