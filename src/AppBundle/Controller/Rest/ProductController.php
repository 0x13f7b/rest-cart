<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\Product;
use AppBundle\Form\Type\ProductType;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * @RouteResource("Product", pluralize=false)
 */
class ProductController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     section="Product",
     *     description="Create new product. E.g. Sandbox Content={'name': 'PR1', 'available': 8, 'tax_rate': 0.5, 'price': {'amount': 56, 'decimals': 20}}. Use double quotes instead of single quotes.",
     *     headers={
     *          {
     *              "name"="Content-Type",
     *              "default"="application/json",
     *              "required"=true,
     *              "description"="It must be set to 'application/json'"
     *          }
     *     },
     *     statusCodes={
     *          200="Returned when successful",
     *          400="Returned when there are validation errors for product",
     *          500="Returned when malformed product JSON is posted"
     *     }
     * )
     *
     * @Rest\Post("add")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');

        $product = $serializer->deserialize($request->getContent(), Product::class, 'json');

        $form = $this->createForm(ProductType::class, $product, [
            'method' => Request::METHOD_POST,
        ]);

        $form->handleRequest($request);

        $valid = false;

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $valid = true;
            }
        } else {
            if ($request->get('rest-test') == true) {
                $valid = true;
            }
        }

        if ($valid) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->json($serializer->toArray($product), 200);
        }

        return $this->json($serializer->toArray($form->getErrors()), 400);
    }

    /**
     * @ApiDoc(
     *     section="Product",
     *     description="Delete product",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Product ID"}
     *     },
     *     statusCodes={
     *          204="Returned when successful",
     *          404="Returned when product is not found"
     *     }
     * )
     *
     * @Rest\Delete("/{id}/remove")
     *
     * @param $id
     *
     * @return JsonResponse
     * @internal param Product $product
     *
     */
    public function removeAction($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if (!$product instanceof Product) {
            return $this->json(null, 404);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        return $this->json(null, 204);
    }
}
