<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    /**
     * @var array
     */
    private $originalData;

    public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->originalData = [
            'merchantAccount' => 'testacc',
            'merchantReference' => 'TEST-10000',
            'secret' => '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF',
            'skinCode' => '05cp1ZtM',
            'amount' => 10.00,
            'testMode' => true,
            'shipBeforeDate' => '2013-11-11',
            'sessionValidity' => '2013-11-05T11:27:59',
            'currency' => 'EUR'
        ];

        $this->request->initialize($this->originalData);
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('testacc', $data['merchantAccount']);
        $this->assertSame(1000, $data['paymentAmount']);
        $this->assertSame('EUR', $data['currencyCode']);
        $this->assertSame('TEST-10000', $data['merchantReference']);
        $this->assertSame('upSRkqwZjw3Q3USND3Xmjmh4e1+XBTwLFFWX4hkfhSc=', $data['merchantSig']);
    }

    public function testGetDataWithAdditionalData()
    {
        $additionalData = [
            "openinvoicedata.merchantData" => "eyJjdXN0b21lcl9hY2NvdW50X2luZm8iOlt7InVuaXF1ZV9hY2NvdW50X2lkZW50aWZpZXIiOiJ0ZXN0QGVtYWlsLmNvbSIsImFjY291bnRfcmVnaXN0cmF0aW9uX2RhdGUiOiIyMDE3LTAzLTEwVDE0OjUxOjQyKzAxOjAwIiwiYWNjb3VudF9sYXN0X21vZGlmaWVkIjoiMjAxNy0wMy0xMFQxNDo1MTo0MiswMTowMCJ9XSwicGF5bWVudF9oaXN0b3J5X3NpbXBsZSI6W3sidW5pcXVlX2FjY291bnRfaWRlbnRpZmllciI6InRlc3RAZW1haWwuY29tIiwicGFpZF9iZWZvcmUiOnRydWV9XX0=",
            "openinvoicedata.numberOfLines" => 2,
            "openinvoicedata.line1.currencyCode" => "EUR",
            "openinvoicedata.line1.description" => "Brand Shirt Black",
            "openinvoicedata.line1.itemAmount" => 7500,
            "openinvoicedata.line1.itemVatAmount" => 2100,
            "openinvoicedata.line1.itemVatPercentage" => 2800,
            "openinvoicedata.line1.numberOfItems" => 1,
            "openinvoicedata.line1.vatCategory" => "high",
            "openinvoicedata.line2.currencyCode" => "EUR",
            "openinvoicedata.line2.description" => "V-Neck Shirt Grey",
            "openinvoicedata.line2.itemAmount" => 8000,
            "openinvoicedata.line2.itemVatAmount" => 2400,
            "openinvoicedata.line2.itemVatPercentage" => 3000,
            "openinvoicedata.line2.numberOfItems" => 2,
            "openinvoicedata.line2.vatCategory" => "high",
        ];

        $this->originalData['additionalData'] = $additionalData;
        $this->originalData['shopperType'] = '2';
        $this->originalData['shopperEmail'] = 'pepe@balr.com';

        $this->request->initialize($this->originalData);

        $data = $this->request->getData();

        $this->assertSame('testacc', $data['merchantAccount']);
        $this->assertSame(1000, $data['paymentAmount']);
        $this->assertSame('EUR', $data['currencyCode']);
        $this->assertSame('TEST-10000', $data['merchantReference']);
        $this->assertSame('YVYBKvGlM7LyAyhLZyEuz81W2BI9ovnKeWKRCFwR5vc=', $data['merchantSig']);
        $this->assertSame('2', $data['shopperType']);
        $this->assertSame('pepe@balr.com', $data['shopperEmail']);
        //only testing some of the open invoice data
        $this->assertSame(2, $data['openinvoicedata.numberOfLines']);
        $this->assertSame(7500, $data['openinvoicedata.line1.itemAmount']);
        $this->assertSame(3000, $data['openinvoicedata.line2.itemVatPercentage']);
    }

    public function testGenerateSignature()
    {
        $signatureMethod = new \ReflectionMethod($this->request, 'generateSignature');
        $signatureMethod->setAccessible(true);

        $this->assertSame(
            'R3UmuIu1XARl6mhZrduuhBT6BV8+jcF/s0kyHfHi9r0=',
            $signatureMethod->invoke($this->request, $this->request->getData())
        );
    }

    public function testGetSetSessionValidity()
    {
        $this->request->setSessionValidity('2013-11-05T11:27:59');
        $this->assertSame($this->request->getSessionValidity(), '2013-11-05T11:27:59');
    }

    public function testGetSetMerchantReference()
    {
        $this->request->setMerchantReference('TESTREF');
        $this->assertSame($this->request->getMerchantReference(), 'TESTREF');
    }

    public function testGetSetMerchantAccount()
    {
        $this->request->setMerchantAccount('TESTACC');
        $this->assertSame($this->request->getMerchantAccount(), 'TESTACC');
    }

    public function testGetAdditionalData()
    {
        $additionalData = [
            "openinvoicedata.merchantData" => "eyJjdXN0b21lcl9hY2NvdW50X2luZm8iOlt7InVuaXF1ZV9hY2NvdW50X2lkZW50aWZpZXIiOiJ0ZXN0QGVtYWlsLmNvbSIsImFjY291bnRfcmVnaXN0cmF0aW9uX2RhdGUiOiIyMDE3LTAzLTEwVDE0OjUxOjQyKzAxOjAwIiwiYWNjb3VudF9sYXN0X21vZGlmaWVkIjoiMjAxNy0wMy0xMFQxNDo1MTo0MiswMTowMCJ9XSwicGF5bWVudF9oaXN0b3J5X3NpbXBsZSI6W3sidW5pcXVlX2FjY291bnRfaWRlbnRpZmllciI6InRlc3RAZW1haWwuY29tIiwicGFpZF9iZWZvcmUiOnRydWV9XX0=",
            "openinvoicedata.numberOfLines" => 2,
            "openinvoicedata.line1.currencyCode" => "EUR",
            "openinvoicedata.line1.description" => "Brand Shirt Black",
            "openinvoicedata.line1.itemAmount" => 7500,
            "openinvoicedata.line1.itemVatAmount" => 2100,
            "openinvoicedata.line1.itemVatPercentage" => 2800,
            "openinvoicedata.line1.numberOfItems" => 1,
            "openinvoicedata.line1.vatCategory" => "high",
            "openinvoicedata.line2.currencyCode" => "EUR",
            "openinvoicedata.line2.description" => "V-Neck Shirt Grey",
            "openinvoicedata.line2.itemAmount" => 8000,
            "openinvoicedata.line2.itemVatAmount" => 2400,
            "openinvoicedata.line2.itemVatPercentage" => 3000,
            "openinvoicedata.line2.numberOfItems" => 2,
            "openinvoicedata.line2.vatCategory" => "high",
        ];

        $this->request->setAdditionalData($additionalData);
        $this->assertSame($this->request->getAdditionalData(), $additionalData);
    }

    public function testGetSetSkinCode()
    {
        $this->request->setSkinCode('da45gy6');
        $this->assertSame($this->request->getSkinCode(), 'da45gy6');
    }

    public function testGetSetShipBeforeDate()
    {
        $this->request->setShipBeforeDate('2012-12-21');
        $this->assertSame($this->request->getShipBeforeDate(), '2012-12-21');
    }

    public function testGetSetSecret()
    {
        $this->request->setSecret('^hfyJs7f_K8');
        $this->assertSame($this->request->getSecret(), '^hfyJs7f_K8');
    }

    public function testGetSetShopperLocale()
    {
        $this->request->setShopperLocale('en_GB');
        $this->assertSame($this->request->getShopperLocale(), 'en_GB');
    }

    public function testGetSetAllowedMethods()
    {
        $this->request->setAllowedMethods('visa');
        $this->assertSame($this->request->getAllowedMethods(), 'visa');
    }

    public function testGetEndpoint()
    {
        $this->assertSame(
            $this->request->getEndpoint(),
            'https://test.adyen.com/hpp/pay.shtml'
        );
    }

    public function testShopperType()
    {
        $this->request->setShopperType('2');
        $this->assertSame($this->request->getShopperType(), '2');
    }

    public function testShopperEmail()
    {
        $this->request->setShopperEmail('pepe@balr.com');
        $this->assertSame($this->request->getShopperEmail(), 'pepe@balr.com');
    }
}
