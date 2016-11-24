<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractRequest;
use Guzzle\Common\Event;

/**
 * Adyen Void Request
 */
class VoidRequest extends AbstractRequest
{
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function getMerchantAccount()
    {
        return $this->getParameter('merchantAccount');
    }

    public function setMerchantAccount($value)
    {
        return $this->setParameter('merchantAccount', $value);
    }

    public function getPspId()
    {
        return $this->getParameter('pspId');
    }

    public function setPspId($value)
    {
        return $this->setParameter('pspId', $value);
    }

    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $this->validate('merchantAccount', 'pspId', 'description');

        $data = [];

        $data['merchantAccount']   = $this->getMerchantAccount();
        $data['originalReference'] = $this->getPspId();
        $data['reference']         = $this->getDescription();

        return $data;
    }

    protected function sendRequest($data = null)
    {
        $this->httpClient->getEventDispatcher()->addListener('request.error', function (Event $event) {
            /**
             * @var \Guzzle\Http\Message\Response $response
             */
            $response = $event['response'];

            if ($response->isError()) {
                $event->stopPropagation();
            }
        });

        $httpRequest = $this->httpClient->post(
            $this->getEndpoint(),
            [
                'Accept'       => 'application/json',
                'Content-type' => 'application/json',
            ],
            json_encode($data)
        );

        $httpRequest->setAuth($this->getUsername(), $this->getPassword());

        return $httpRequest->send();
    }

    /**
     * @param mixed $data
     *
     * @return VoidResponse
     */
    public function sendData($data)
    {
        $httpResponse = $this->sendRequest($data);

        return $this->response = new VoidResponse($this, $httpResponse->json());
    }

    public function getEndPoint()
    {
        return ('https://pal-' . ($this->getTestMode() ? 'test' : 'live') .
            '.adyen.com/pal/servlet/Payment/v12/cancel');
    }
}
