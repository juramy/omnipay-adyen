<?php

namespace Omnipay\Adyen;

use Omnipay\Adyen\Message\Notification;
use Omnipay\Common\AbstractGateway;
use Omnipay\Adyen\Message\CompletePurchaseResponse;

/**
 * Adyen Gateway
 *
 * Adyen is a Off-site gateway - No need to send request, instead we need to redirect customer to Adyen site.
 *
 * @link https://support.adyen.com/index.php?/Knowledgebase/List
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Adyen';
    }

    public function getDefaultParameters()
    {
        return array(
            'testMode'          => true,
            'merchantAccount'   => 'see-what-is-configured-in-the-adyen-skin',
            'secret'            => 'see-what-is-configured-in-the-adyen-skin',
            'skinCode'          => 'see-what-is-configured-in-the-adyen-skin',
            'hmacKey'           => 'see-what-is-configured-in-the-adyen-notification'
        );
    }

    public function getSessionValidity()
    {
        return $this->getParameter('sessionValidity');
    }

    public function setSessionValidity($value)
    {
        return $this->setParameter('sessionValidity', $value);
    }

    public function getMerchantReference()
    {
        return $this->getParameter('merchantReference');
    }

    public function setMerchantReference($value)
    {
        return $this->setParameter('merchantReference', $value);
    }

    public function getMerchantAccount()
    {
        return $this->getParameter('merchantAccount');
    }

    public function setMerchantAccount($value)
    {
        return $this->setParameter('merchantAccount', $value);
    }

    public function getSkinCode()
    {
        return $this->getParameter('skinCode');
    }

    public function setSkinCode($value)
    {
        return $this->setParameter('skinCode', $value);
    }

    public function getShipBeforeDate()
    {
        return $this->getParameter('shipBeforeDate');
    }

    public function setShipBeforeDate($value)
    {
        return $this->setParameter('shipBeforeDate', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function getHmacKey()
    {
        return $this->getParameter('hmacKey');
    }

    public function setHmacKey($value)
    {
        return $this->setParameter('hmacKey', $value);
    }

    public function getShopperLocale()
    {
        return $this->getParameter('shopperLocale');
    }

    public function setShopperLocale($value)
    {
        return $this->setParameter('shopperLocale', $value);
    }

    public function getAllowedMethods()
    {
        return $this->getParameter('allowedMethods');
    }

    public function setAllowedMethods($value)
    {
        return $this->setParameter('allowedMethods', $value);
    }

    public function getBlockedMethods()
    {
        return $this->getParameter('blockedMethods');
    }

    public function setBlockedMethods($value)
    {
        return $this->setParameter('blockedMethods', $value);
    }

    public function getShopperReference()
    {
        return $this->getParameter('shopperReference');
    }

    public function setShopperReference($value)
    {
        return $this->setParameter('shopperReference', $value);
    }

    public function getReturnUrl()
    {
        return $this->getParameter('returnUrl');
    }

    public function setReturnUrl($value)
    {
        return $this->setParameter('returnUrl', $value);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Adyen\Message\PurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Adyen\Message\CompletePurchaseRequest', $parameters);
    }

    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Adyen\Message\CaptureRequest', $parameters);
    }

    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Adyen\Message\RefundRequest', $parameters);
    }

    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Adyen\Message\VoidRequest', $parameters);
    }

    public function acceptNotification()
    {
        return new Notification(array_replace(
            $this->getParameters(),
            $this->httpRequest->request->all(),
            $this->httpRequest->query->all()
        ));
    }
}
