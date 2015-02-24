<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

class CompletePurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->getHttpRequest()->initialize(array(
            'authResult' => 'AUTHORISED',
            'pspReference' => '1211992213193029',
            'merchantReference' => 'Internet Order 12345',
            'skinCode' => '4aD37dJA',
            'merchantReturnData' => '',
        ));

        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'secret' => 'Kah942*$7sdp0)'
        ]);
    }

    public function testGetData()
    {

        $data = $this->request->getData();
        $this->assertSame($this->getHttpRequest()->request->all(), $data);

    }

    public function testGenerateResponseSignature()
    {
        $this->assertSame(
            'ytt3QxWoEhAskUzUne0P5VA9lPw=',
            $this->request->generateResponseSignature($this->request->getData())
        );
    }

    public function testSend()
    {
        $this->getHttpRequest()->request->set('authResult', 'AUTHORISED');
        $authResult = $this->request->getData();
        $this->assertSame('AUTHORISED', $authResult['authResult']);

    }
}
