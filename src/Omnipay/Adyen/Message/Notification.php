<?php namespace Omnipay\Adyen\Message;

use Omnipay\Common\Helper;
use Omnipay\Common\Message\NotificationInterface;

/**
 * Adyen Purchase Request
 */
class Notification implements NotificationInterface
{
    private static $acceptedResponse = '[accepted]';

    /**
     * The notification parameters
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Initialize the object with parameters.
     *
     * If any unknown parameters passed, they will be ignored.
     *
     * @param array $parameters An associative array of parameters
     *
     * @return $this
     * @throws RuntimeException
     */
    public function initialize(array $parameters = array())
    {
        if (null !== $this->response) {
            throw new RuntimeException('Request cannot be modified after it has been sent!');
        }

        $this->parameters = new ParameterBag;

        Helper::initialize($this, $parameters);

        return $this;
    }

    /**
     * Get all parameters as an associative array.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters->all();
    }

    /**
     * Get a single parameter.
     *
     * @param string $key The parameter key
     * @return mixed
     */
    private function getParameter($key)
    {
        return $this->parameters->get($key);
    }

    /**
     * Set a single parameter
     *
     * @param string $key The parameter key
     * @param mixed $value The value to set
     * @return PurchaseRequest Provides a fluent interface
     */
    private function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->parameters->all();
    }

    /**
     * Gateway Reference
     *
     * @return string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        return $this->getParameter('pspReference');
    }

    /**
     * Was the transaction successful?
     *
     * @return string Transaction status, one of {@see STATUS_COMPLETED}, {@see #STATUS_PENDING},
     * or {@see #STATUS_FAILED}.
     */
    public function getTransactionStatus()
    {
        if($this->getParameter('eventCode') == 'AUTHORISATION' && $this->getParameter('success')) {
            return NotificationInterface::STATUS_COMPLETED;
        } elseif($this->getParameter('eventCode') == 'PENDING') {
            return NotificationInterface::STATUS_PENDING;
        }
        return NotificationInterface::STATUS_FAILED;
    }

    /**
     * Response Message
     *
     * @return string A response message from the payment gateway
     */
    public function getMessage()
    {
        return $this->getParameter('reason');
    }

    /**
     * The response that can be given to the payment gateway
     *
     * @return string
     */
    public function getResponse()
    {
        return self::$acceptedResponse;
    }
}