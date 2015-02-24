<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

class CompletePurchaseResponseTest extends TestCase
{
    public function testCompletePurchaseSuccess()
    {
        $response = new CompletePurchaseResponse(
            $this->getMockRequest(),
            array(
                'success' => true,
                'allParams' => array(
                    'merchantSig' => 'Ti+ACycv7TmV3VY6hfQ6tIIdhGM='
                ),
                'responseSignature' => 'Ti+ACycv7TmV3VY6hfQ6tIIdhGM='
            )
        );

        $this->assertTrue($response->isSuccessful());
    }

    public function testCompletePurchaseFailure()
    {
        $response = new CompletePurchaseResponse(
            $this->getMockRequest(),
            array(
                'success' => false,
                'allParams' => array(
                    'merchantSig' => 'Ti+ACycv7TmV3VY6hfQ6tIIdhGM='
                ),
                'responseSignature' => 'Ti+ACycv7TmV3VY6hfQ6tIIdhGM='
            )
        );

        $this->assertFalse($response->isSuccessful());
    }

    public function testNonMatchingSignature()
    {
        $response = new CompletePurchaseResponse(
            $this->getMockRequest(),
            array(
                'success' => true,
                'allParams' => array(
                    'merchantSig' => 'Ti+ACycv7TmV3VY6hfQ6tIIdhGM='
                ),
                'responseSignature' => 'Wkf3d2PVmSPu5Zn5N5H240AQfJ0='
            )
        );

        $this->assertFalse($response->isSuccessful());
    }

    public function testIsSuccessful()
    {
        $response = new CompletePurchaseResponse(
            $this->getMockRequest(),
            array(
                'success' => true,
                'allParams' => array(
                    'merchantSig' => 'Ti+ACycv7TmV3VY6hfQ6tIIdhGM='
                ),
                'responseSignature' => 'Ti+ACycv7TmV3VY6hfQ6tIIdhGM='
            )
        );

        $this->assertTrue($response->isSuccessful());
    }

    public function testGetResponse()
    {

        $mock = $this->getMockRequest();
        $response = new CompletePurchaseResponse($mock, array());
        $this->assertSame(serialize($response), serialize($response->getResponse()));

    }
}
