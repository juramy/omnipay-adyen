<?php

namespace Omnipay\Adyen\Message;

/**
 * Adyen Complete Purchase Request
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    public function getData()
    {
        return $this->httpRequest->request->all();
    }

    public function getAuthResult()
    {
        return $this->httpRequest->query->get('authResult');
    }

    public function getPspReference()
    {
        return $this->httpRequest->query->get('pspReference');
    }

    public function getMerchantReference()
    {
        return $this->httpRequest->query->get('merchantReference');
    }

    public function getSkinCode()
    {
        return $this->httpRequest->query->get('skinCode');
    }

    public function getMerchantReturnData()
    {
        return $this->httpRequest->query->get('merchantReturnData');
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
        $data['success'] = ('AUTHORISED' == $this->httpRequest->query->get('authResult')) ? true : false;
        $data['allParams'] = $this->httpRequest->query->all();
        $data['responseSignature'] = $this->generateResponseSignature();

        return new CompletePurchaseResponse($this, $data);
    }
}
