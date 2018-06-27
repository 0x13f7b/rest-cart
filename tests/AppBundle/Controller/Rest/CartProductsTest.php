<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartProductsTest extends WebTestCase
{
    /**
     * Test scenario:
     * 1. Create new cart
     * 2. Create new product "PR1"
     * 3. Create new product "PR2"
     * 4. Add product "PR1" to cart (2 units)
     * 5. Add product "PR2" to cart (1 unit)
     * 6. Get all products in cart
     * 7. Get cart subtotal
     * 8. Get cart VAT amount
     * 9. Get cart total
     * 10. Delete cart
     * 11. Delete product "PR1"
     * 12. Delete product "PR2"
     *
     * After each step validate json
     */
    public function testAddRemoveProductFromCart()
    {
        $client = static::createClient();

        $client->request('POST', '/cart/add');

        $response = $client->getResponse();
        $content  = $response->getContent();
        $cartId   = json_decode($content)->cartId;

        // Create new product and check that response contains valid json content
        $productJson = '{"name": "PR1", "available": 8, "tax_rate": 0.5, "price": {"amount": 56, "decimals": 20}}';
        $client->request('POST', '/product/add', ['rest-test' => true], [], [], $productJson);
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);

        $productAId = json_decode($content)->id;

        // Create another product
        $productJson = '{"name": "PR2", "available": 5, "vat_rate": 0.2, "price": {"amount": 10, "decimals": 20}}';
        $client->request('POST', '/product/add', ['rest-test' => true], [], [], $productJson);
        $content = $client->getResponse()->getContent();
        $this->assertJson($content);
        $productBId = json_decode($content)->id;

        // Add product to cart and check that response contains correct json for the new product
        $client->request('GET', '/cart/' . $cartId . '/add/' . $productAId);
        $validJSON = '{"cart": {"id": ' . $cartId . ', "products": [{"product": {"id": ' . $productAId . ', "name": "PR1", "available": 8, "tax_rate": 0.5, "price": { "amount": 56, "decimals": 20 }},"count": 1}]}}';
        $this->assertJsonStringEqualsJsonString($validJSON, $client->getResponse()->getContent());

        // Add the same product to cart one more time and check if response still contains correct json and product count increases
        $client->request('GET', '/cart/' . $cartId . '/add/' . $productAId);
        $validJSON = '{"cart": {"id": ' . $cartId . ', "products": [{"product": {"id": ' . $productAId . ', "name": "PR1", "available": 8, "tax_rate": 0.5, "price": { "amount": 56, "decimals": 20 }},"count": 2}]}}';
        $this->assertJsonStringEqualsJsonString($validJSON, $client->getResponse()->getContent());

        // Add different product to cart and check if cart contains both products
        $client->request('GET', '/cart/' . $cartId . '/add/' . $productBId);
        $validJSON = '{"cart": {"id": ' . $cartId . ', "products": [
            {"product": {"id": ' . $productAId . ', "name": "PR1", "available": 8, "tax_rate": 0.5, "price": { "amount": 56, "decimals": 20 }},"count": 2}, 
            {"product": {"id": ' . $productBId . ', "name": "PR2", "available": 5, "tax_rate": 0, "price": { "amount": 10, "decimals": 20 }},"count": 1}]}}';

        $response = $client->getResponse()->getContent();

        trim($validJSON, "\n\t\r\s");
        trim($response, "\n\t\r\s");

        $this->assertJsonStringEqualsJsonString($validJSON, $response);


        // Get cart subtotal and check if it is correct
        $client->request('GET', '/cart/' . $cartId . '/subtotal');
        $validJSON = '{"amount": 122, "decimals": 60}';
        $this->assertJsonStringEqualsJsonString($validJSON, $client->getResponse()->getContent());

        // Get cart VAT amount and check if it is correct
        $client->request('GET', '/cart/' . $cartId . '/tax-amount');
        $validJSON = '{"amount": 56, "decimals": 20}';
        $this->assertJsonStringEqualsJsonString($validJSON, $client->getResponse()->getContent());

        // Get cart total and check if it is correct
        $client->request('GET', '/cart/' . $cartId . '/total');
        $validJSON = '{"amount": 178, "decimals": 80}';
        $this->assertJsonStringEqualsJsonString($validJSON, $client->getResponse()->getContent());

        // Delete cart and check if response status code is 204
        $client->request('DELETE', '/cart/' . $cartId . '/remove');
        $this->assertEquals(204, $client->getResponse()->getStatusCode());

        // Delete both products and check if response status code is 204
        $client->request('DELETE', '/product/' . $productAId . '/remove');
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $client->request('DELETE', '/product/' . $productBId . '/remove');
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }
}
