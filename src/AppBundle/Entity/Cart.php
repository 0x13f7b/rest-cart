<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Base\CartInterface;
use AppBundle\Entity\Base\MoneyInterface;
use AppBundle\Entity\Base\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Cart
 *
 * @ORM\Table(name="cart")
 * @ORM\Entity()
 * @ORM\ChangeTrackingPolicy(value="DEFERRED_EXPLICIT")
 * @JMS\ExclusionPolicy("all")
 */
class Cart implements CartInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CartProducts", mappedBy="cart", cascade={"all"})
     * @JMS\Expose()
     */
    private $cartProducts;

    /**
     * Cart constructor.
     */
    public function __construct()
    {
        $this->cartProducts = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Add product to cart
     * @param ProductInterface $product
     *
     * @return CartInterface
     */
    public function addProduct(ProductInterface $product): CartInterface
    {
        $cartProduct = new CartProducts();
        $cartProduct->setProduct($product);
        $cartProduct->setCart($this);

        $this->cartProducts->add($cartProduct);

        return $this;
    }

    /**
     * Remove product from cart
     * @param ProductInterface $product
     *
     * @return CartInterface
     */
    public function removeProduct(ProductInterface $product): CartInterface
    {
        $cartProduct = new CartProducts();
        $cartProduct->setProduct($product);
        $cartProduct->setCart($this);

        $this->cartProducts->removeElement($cartProduct);

        return $this;
    }

    /**
     * Get all cartProducts
     * @return Collection
     */
    public function getProducts(): Collection
    {
        return $this->cartProducts;
    }


    /**
     * Get all Product instances in cart
     * @return array
     */
    public function getAllProducts()
    {
        $products = [];

        foreach ($this->cartProducts as $cartProduct) {
            $products[] = $cartProduct->getProduct();
        }

        return $products;
    }

    /**
     * Calculate and return cart total
     * @return MoneyInterface
     */
    public function getTotal(): MoneyInterface
    {
        $totalDecimals = 0;

        foreach ($this->getProducts() as $cartProduct) {
            $totalDecimals += $cartProduct->getProduct()->getTotalInDecimals() * $cartProduct->getCount();
        }

        $total = new Money();
        $total->setDecimals($totalDecimals);

        return $total;

    }

    /**
     * Sum all product TAX amounts and return sum
     * @return MoneyInterface
     */
    public function getTaxAmount(): MoneyInterface
    {
        $taxAmount = 0;

        foreach ($this->getProducts() as $cartProduct) {
            $taxAmount += $cartProduct->getProduct()->getTaxAmountInDecimals() * $cartProduct->getCount();
        }

        $money = new Money();
        $money->setDecimals($taxAmount);

        return $money;
    }

    /**
     * Sum all product prices without TAX and sum
     * @return MoneyInterface
     */
    public function getSubtotal(): MoneyInterface
    {
        $totalDecimals = 0;

        foreach ($this->getProducts() as $cartProduct) {
            /** @var MoneyInterface $productPrice */
            $productPrice = $cartProduct->getProduct()->getPrice();
            $totalDecimals += $productPrice->getTotalAmount() * $cartProduct->getCount();
        }

        $total = new Money();
        $total->setDecimals($totalDecimals);

        return $total;
    }
}
