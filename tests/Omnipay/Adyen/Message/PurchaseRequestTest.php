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
        $merchantData = [
            'customer_account_info' => [
                [
                    'unique_account_identifier' => 'test@email.com',
                    'account_registration_date' => '2017-03-10T14:51:42+01:00',
                    'account_last_modified' => '2017-03-10T14:51:42+01:00'
                ]
            ],
            'payment_history_simple' => [
                [
                    'unique_account_identifier' => 'test@email.com',
                    'paid_before' => true
                ]
            ]
        ];
        $this->originalData['merchantData'] = $merchantData;
        $this->originalData['shopperType'] = '2';
        $this->originalData['shopperEmail'] = 'pepe@balr.com';

        $this->request->initialize($this->originalData);

        $data = $this->request->getData();

        $this->assertSame('testacc', $data['merchantAccount']);
        $this->assertSame(1000, $data['paymentAmount']);
        $this->assertSame('EUR', $data['currencyCode']);
        $this->assertSame('TEST-10000', $data['merchantReference']);
        $this->assertSame('YZgTZ5geG5+9aAms8jeIjpenBHOthEbnXf8VkZ4qn50=', $data['merchantSig']);
        $this->assertSame($merchantData, json_decode(base64_decode($data['openinvoicedata.merchantData']), true));
        $this->assertSame('2', $data['shopperType']);
        $this->assertSame('pepe@balr.com', $data['shopperEmail']);
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

    public function testGetMerchantData()
    {
        $merchantData = [
            'customer_account_info' => [
                [
                    'unique_account_identifier' => 'test@email.com',
                    'account_registration_date' => '2017-03-10T14:51:42+01:00',
                    'account_last_modified' => '2017-03-10T14:51:42+01:00'
                ]
            ],
            'payment_history_simple' => [
                [
                    'unique_account_identifier' => 'test@email.com',
                    'paid_before' => true
                ]
            ]
        ];

        $this->request->setMerchantData($merchantData);
        $this->assertSame($this->request->getMerchantData(), base64_encode(json_encode($merchantData)));
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
