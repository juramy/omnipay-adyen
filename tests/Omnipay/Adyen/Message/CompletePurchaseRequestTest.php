<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

class CompletePurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(array(
            'secret' => 'Kah942*$7sdp0)',
            'authResult' => 'AUTHORISED',
            'pspReference' => '1211992213193029',
            'merchantReference' => 'Internet Order 12345',
            'skinCode' => '4aD37dJA',
            'merchantReturnData' => '',
        ));
    }

    public function testGetData()
    {

        $data = $this->request->getData();

        $this->assertSame(array (
          'authResult' => 'AUTHORISED',
          'pspReference' => '1211992213193029',
          'merchantReference' => 'Internet Order 12345',
          'skinCode' => '4aD37dJA',
          'merchantSig' => 'ytt3QxWoEhAskUzUne0P5VA9lPw=',
        ), $data);

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
