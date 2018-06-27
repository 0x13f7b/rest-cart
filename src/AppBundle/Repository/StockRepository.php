<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Base\ProductInterface;
use AppBundle\Entity\Base\StockInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class StockRepository extends EntityRepository implements StockInterface
{
    /**
     * Insert product into DB
     * @param ProductInterface $product
     *
     * @return StockInterface
     */
    public function addProduct(ProductInterface $product): StockInterface
    {
        $em = $this->getEntityManager();
        $em->persist($product);
        $em->flush();

        return $this;
    }

    /**
     * Remove product from DB
     * @param ProductInterface $product
     *
     * @return StockInterface
     */
    public function removeProduct(ProductInterface $product): StockInterface
    {
        $em = $this->getEntityManager();
        $em->remove($product);
        $em->flush();

        return $this;
    }

    /**
     * Get all products
     * @return ArrayCollection
     */
    public function getProducts(): ArrayCollection
    {
        return new ArrayCollection($this->findAll());
    }
}
