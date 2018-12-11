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
        $data['merchantSig'] = $this->getMerchantSig();

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

    public function getMerchantSig()
    {
        return $this->getParameter('merchantSig');
    }

    public function setMerchantSig($value)
    {
        return $this->setParameter('merchantSig', $value);
    }

    public function getPaymentMethod()
    {
        return $this->getParameter('paymentMethod');
    }

    public function setPaymentMethod($value)
    {
        return $this->setParameter('paymentMethod', $value);
    }

    private function generateResponseSignature()
    {
        $params = $this->httpRequest->query->all();

        unset($params['merchantSig']);

        if ($this->httpRequest->query->has('additionalData_acquirerReference')) {
            // this field is added to the signature when open invoice data is added to the request
            // @see https://docs.adyen.com/developers/classic-integration/hosted-payment-pages/hosted-payment-pages-api
            $params['additionalData.acquirerReference'] = $this->httpRequest->query->get('additionalData_acquirerReference');

            unset($params['additionalData_acquirerReference']);
        }

        $params = array_filter($params);

        // Sort the array by key using SORT_STRING order
        ksort($params, SORT_STRING);

        // The character escape function
        $escapeVal = function ($val) {
            return str_replace(':', '\\:', str_replace('\\', '\\\\', $val));
        };

        // Generate the signing data string
        $signData = implode(':', array_map($escapeVal, array_merge(array_keys($params), array_values($params))));

        // base64-encode the binary result of the HMAC computation
        return base64_encode(hash_hmac('sha256', $signData, pack("H*", $this->getSecret()), true));
    }

    public function send()
    {
        $data = $this->getData();
        $data['success'] = $this->isSuccessful();
        $data['allParams'] = $this->getData();
        $data['responseSignature'] = $this->generateResponseSignature();

        return new CompletePurchaseResponse($this, $data);
    }

    /**
     * Check if the payment request is authorized. For use outside this library you
     * should however use the isSuccessful() method of the CompletePurchaseResponse
     * which also validates the response.
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->getAuthResult() == 'AUTHORISED' || $this->getAuthResult() == 'AUTHORISATION';
    }

    /**
     * {@inheritDoc}
     * @see \Omnipay\Common\Message\AbstractRequest::initialize()
     *
     * Not completely sure this is the right way to have all the fields
     * returned from Adyen; maybe the consumer of the Gateway should do it,
     * but right now I don't see any better options.
     */
    public function initialize(array $parameters = array())
    {
        return parent::initialize(array_replace(
            $parameters,
            $this->httpRequest->request->all(),
            $this->httpRequest->query->all()
        ));
    }
}
