<?php

namespace Omnipay\Adyen\Message;

/**
 * Adyen Complete Purchase Request
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    public function getData()
    {
        $data = array();

        $data['authResult'] = $this->getAuthResult();
        $data['pspReference'] = $this->getPspReference();
        $data['merchantReference'] = $this->getMerchantReference();
        $data['skinCode'] = $this->getSkinCode();
        $data['merchantSig'] = $this->generateResponseSignature();

        return $data;
    }

    public function getAuthResult()
    {
        return $this->getParameter('authResult');
    }

    public function setAuthResult($value)
    {
        return $this->setParameter('authResult', $value);
    }

    public function getPspReference()
    {
        return $this->getParameter('pspReference');
    }

    public function setPspReference($value)
    {
        return $this->setParameter('pspReference', $value);
    }

    public function getMerchantReference()
    {
        return $this->getParameter('merchantReference');
    }

    public function setMerchantReference($value)
    {
        return $this->setParameter('merchantReference', $value);
    }

    public function getSkinCode()
    {
        return $this->getParameter('skinCode');
    }

    public function setSkinCode($value)
    {
        return $this->setParameter('skinCode', $value);
    }

    public function getMerchantReturnData()
    {
        return $this->getParameter('merchantReturnData');
    }

    public function setMerchantReturnData($value)
    {
        return $this->setParameter('merchantReturnData', $value);
    }

    public function generateResponseSignature()
    {
        return base64_encode(
            hash_hmac(
                'sha1',
                $this->getAuthResult().
                $this->getPspReference().
                $this->getMerchantReference().
                $this->getSkinCode().
                $this->getMerchantReturnData(),
                $this->getSecret(),
                true
            )
        );
    }

    public function send()
    {
        $data = $this->getData();
        $data['success'] = $this->isSuccessful();
        $data['allParams'] = $this->getData();
        $data['responseSignature'] = $this->generateResponseSignature();

        return new CompletePurchaseResponse($this, $data);
    }

    public function isSuccessful()
    {
        return (bool) (strpos($this->getAuthResult(), 'AUTHROIS') + 1);
    }
}
