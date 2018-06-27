<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * CartProducts
 *
 * @ORM\Table(name="cart_products")
 * @ORM\Entity()
 * @ORM\ChangeTrackingPolicy(value="DEFERRED_EXPLICIT")
 * @JMS\ExclusionPolicy("all")
 */
class CartProducts
{
    /**
     * @var Cart
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Cart", inversedBy="cartProducts")
     * @ORM\JoinColumn(name="cart_id")
     */
    private $cart;

    /**
     * @var Product
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product", inversedBy="carts")
     * @ORM\JoinColumn(name="product_id")
     */
    private $product;

    /**
     * Indicates how many times this product has been added to the cart
     *
     * @var int
     * @ORM\Column(name="count", type="integer")
     */
    private $count = 1;

    /**
     * @return Cart
     */
    public function getCart(): Cart
    {
        return $this->cart;
    }

    /**
     * @param Cart $cart
     *
     * @return $this
     */
    public function setCart($cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param $product
     *
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Increase product count in cart
     * @return $this
     */
    public function increaseCount()
    {
        ++$this->count;

        return $this;
    }

    /**
     * Decrease product count in cart
     * @return $this
     */
    public function decreaseCount()
    {
        if ($this->count == 0) {

        } else {
            --$this->count;
        }

        return $this;
    }
}
