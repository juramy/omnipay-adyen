<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

class CompletePurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(array(
            'secret' => '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF',
            'authResult' => 'AUTHORISED',
            'pspReference' => '1211992213193029',
            'merchantReference' => 'Internet Order 12345',
            'skinCode' => '4aD37dJA',
            'merchantReturnData' => null,
            'merchantSig' => 'YRTyF4SIdrW2mKIbNukCTkZ21dHCzcQYOevrBII+yUI='
        ));
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame(array(
            'authResult' => 'AUTHORISED',
            'pspReference' => '1211992213193029',
            'merchantReference' => 'Internet Order 12345',
            'skinCode' => '4aD37dJA',
            'merchantSig' => 'YRTyF4SIdrW2mKIbNukCTkZ21dHCzcQYOevrBII+yUI=',
            'additionalData.acquirerReference' => null
        ), $data);
    }

    public function testGenerateResponseSignature()
    {
        $signatureMethod = new \ReflectionMethod($this->request, 'generateResponseSignature');
        $signatureMethod->setAccessible(true);

        $this->assertSame(
            'YRTyF4SIdrW2mKIbNukCTkZ21dHCzcQYOevrBII+yUI=',
            $signatureMethod->invoke($this->request, $this->request->getData())
        );
    }

    public function testSend()
    {
        // Test a valid request.
        $response = $this->request->send();
        $this->assertTrue($response->isSuccessful());
    }

    public function testTamperedSend()
    {
        // Test a tampered with request.
        $tamperedRequest = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $tamperedRequest->initialize(array(
            'secret' => '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF',
            'authResult' => 'AUTHORISED',
            'pspReference' => '1211992213193029',
            'merchantReference' => 'Internet Order 12345',
            'skinCode' => '4aD37dJA',
            'merchantReturnData' => '',
            'merchantSig' => 'tamered-with-merchant-sig'
        ));

        $response = $tamperedRequest->send();
        $this->assertFalse($response->isSuccessful());
    }
}
