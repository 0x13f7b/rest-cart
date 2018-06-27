<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\Cart;
use AppBundle\Entity\CartProducts;
use AppBundle\Entity\Product;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * @RouteResource("Cart", pluralize=false)
 */
class CartController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     section="Cart",
     *     description="Create new cart",
     *     statusCodes={
     *          200="Returned when successful"
     *     }
     * )
     *
     * @Rest\Post("add")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addAction()
    {
        $cart = new Cart();

        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();

        return $this->json(['cartId' => $cart->getId()], 200);
    }

    /**
     * @ApiDoc(
     *     section="Cart",
     *     description="Delete cart",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Cart ID"}
     *     },
     *     statusCodes={
     *          204="Returned when successful",
     *          404="Returned when cart or product is not found"
     *     }
     * )
     *
     * @Rest\Delete("/{id}/remove")
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function removeAction($id)
    {
        $cart = $this->getDoctrine()->getRepository(Cart::class)->find($id);

        if (!$cart instanceof Cart) {
            return new JsonResponse(null, 404);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($cart);
        $em->flush();

        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *     section="Cart",
     *     description="Add product to cart",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Cart ID"},
     *          {"name"="productId", "dataType"="integer", "requirement"="\d+", "description"="Product ID"},
     *     },
     *     statusCodes={
     *          200="Returned when successful",
     *          404="Returned when cart or product is not found"
     *     }
     * )
     *
     * @Rest\Get("/{id}/add/{productId}")
     *
     * @param         $id
     * @param         $productId
     *
     * @return JsonResponse
     */
    public function addProductAction($id, $productId)
    {
        $doctrine = $this->getDoctrine();
        $cart     = $doctrine->getRepository(Cart::class)->find($id);
        $product  = $doctrine->getRepository(Product::class)->find($productId);

        if (!$cart instanceof Cart || !$product instanceof Product) {
            return new JsonResponse(null, 404);
        }

        $cartProduct = $doctrine->getRepository(CartProducts::class)->findOneBy(['cart' => $id, 'product' => $productId]);

        $em = $this->getDoctrine()->getManager();

        // If product is already in cart then increase count
        if (!empty($cartProduct)) {
            $cartProduct->increaseCount();
            $em->persist($cartProduct);
        } else {
            // Otherwise put the product in cart
            $cartProduct = new CartProducts();
            $cartProduct->setCart($cart);
            $cartProduct->setProduct($product);

            $cart->addProduct($product);
            $em->persist($cart);
        }

        $em->flush();

        $serializer = $this->get('jms_serializer');

        // All cart products
        $allCartProducts = $cart->getProducts();

        // Cart product array for return purposes
        $cartProducts = [];

        // Create array with all cart products and their counts
        foreach ($allCartProducts as $cartProduct) {
            $cartProducts[] = [
                'product' => $cartProduct->getProduct(),
                'count'   => $cartProduct->getCount(),
            ];
        }

        return $this->json([
            'cart' => [
                'id'       => $cart->getId(),
                'products' => $serializer->toArray($cartProducts),
            ],
        ], 200);
    }

    /**
     * @ApiDoc(
     *     section="Cart",
     *     description="Remove product from cart",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Cart ID"},
     *          {"name"="productId", "dataType"="integer", "requirement"="\d+", "description"="Product ID"},
     *     },
     *     statusCodes={
     *          204="Returned when successful",
     *          404="Returned when cart or product is not found"
     *     }
     * )
     *
     * @Rest\Delete("/{id}/remove/{productId}")
     *
     * @param $id
     * @param $productId
     *
     * @return JsonResponse
     */
    public function removeProductAction($id, $productId)
    {
        $doctrine = $this->getDoctrine();
        $cart     = $doctrine->getRepository(Cart::class)->find($id);
        $product  = $doctrine->getRepository(Product::class)->find($productId);

        if (!$cart instanceof Cart || !$product instanceof Product) {
            return new JsonResponse(null, 404);
        }

        $cartProduct = $doctrine->getRepository(CartProducts::class)->findOneBy(['cart' => $id, 'product' => $productId]);

        $em = $this->getDoctrine()->getManager();

        if (!empty($cartProduct)) {
            // Decrease count if product has been added to cart more than once
            if ($cartProduct->getCount() > 1) {
                $cartProduct->decreaseCount();
                $em->persist($cartProduct);
            } else {
                // If user removes last unit of this property from cart then remove this entry from DB
                $em->remove($cartProduct);
            }
        }

        $cart->removeProduct($product);

        $em->persist($cart);
        $em->flush();

        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *     section="Cart",
     *     description="Get all cart products",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Cart ID"}
     *     },
     *     statusCodes={
     *          200="Returned when successful",
     *          404="Returned when cart is not found"
     *     }
     * )
     *
     * @Rest\Get("/{id}/products")
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function getProductsAction($id)
    {
        $doctrine = $this->getDoctrine();
        $cart     = $doctrine->getRepository(Cart::class)->find($id);

        if (!$cart instanceof Cart) {
            return new JsonResponse(null, 404);
        }

        // All cart products
        $allCartProducts = $cart->getProducts();

        // Cart product array for return purposes
        $cartProducts = [];

        // Create array with all cart products and their counts
        foreach ($allCartProducts as $cartProduct) {
            $cartProducts[] = [
                'product' => $cartProduct->getProduct(),
                'count'   => $cartProduct->getCount(),
            ];
        }

        $serializer = $this->get('jms_serializer');

        return $this->json($serializer->toArray($cartProducts), 200);
    }

    /**
     * @ApiDoc(
     *     section="Cart",
     *     description="Get cart total",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Cart ID"}
     *     },
     *     statusCodes={
     *          200="Returned when successful",
     *          404="Returned when cart is not found"
     *     }
     * )
     *
     * @Rest\Get("/{id}/total")
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function getTotalAction($id)
    {
        $doctrine = $this->getDoctrine();
        $cart     = $doctrine->getRepository(Cart::class)->find($id);

        if (!$cart instanceof Cart) {
            return new JsonResponse(null, 404);
        }

        $total = $cart->getTotal();

        return $this->json([
            'amount' => $total->getAmount(),
            'decimals' => $total->getDecimals(),
        ], 200);
    }

    /**
     * @ApiDoc(
     *     section="Cart",
     *     description="Get cart subtotal",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Cart ID"}
     *     },
     *     statusCodes={
     *          200="Returned when successful",
     *          404="Returned when cart is not found"
     *     }
     * )
     *
     * @Rest\Get("/{id}/subtotal")
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function getSubtotalAction($id)
    {
        $doctrine = $this->getDoctrine();
        $cart     = $doctrine->getRepository(Cart::class)->find($id);

        if (!$cart instanceof Cart) {
            return new JsonResponse(null, 404);
        }

        $total = $cart->getSubtotal();

        return $this->json([
            'amount' => $total->getAmount(),
            'decimals' => $total->getDecimals(),
        ], 200);
    }

    /**
     * @ApiDoc(
     *     section="Cart",
     *     description="Get cart TAX amount",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Cart ID"}
     *     },
     *     statusCodes={
     *          200="Returned when successful",
     *          404="Returned when cart is not found"
     *     }
     * )
     *
     * @Rest\Get("/{id}/tax-amount")
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function getTaxAmountAction($id)
    {
        $doctrine = $this->getDoctrine();
        /** @var Cart $cart */
        $cart = $doctrine->getRepository(Cart::class)->find($id);

        if (!$cart instanceof Cart) {
            return new JsonResponse(null, 404);
        }

        $vatAmount = $cart->getTaxAmount();

        return $this->json([
            'amount' => $vatAmount->getAmount(),
            'decimals' => $vatAmount->getDecimals(),
        ], 200);
    }
}
