<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Helper;
use Omnipay\Common\Message\NotificationInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Adyen Notification Request
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

    public function __construct(array $parameters = array())
    {
        $this->initialize($parameters);
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
        $this->parameters = new ParameterBag;

        $tempParams = array();

        foreach ($parameters as $key => $value) {

            if (strpos($value, ',') !== false) {
                $value = explode(',', $value);
            }

            $parameterKey = explode('_', $key);
            if (count($parameterKey) > 1) {
                if (isset($tempParams[$parameterKey[0]])) {
                    $tempParams[$parameterKey[0]] = array($parameterKey[1] => $value);
                } else {
                    $tempParams[$parameterKey[0]][$parameterKey[1]] = $value;
                }
            } else {
                $tempParams[$parameterKey[0]] = $value;
            }
        }

        Helper::initialize($this, $tempParams);

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

    private function isTrue($val, $returnNull = false)
    {
        $boolVal = (is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool)$val);

        return ($boolVal === null && !$returnNull ? false : $boolVal);
    }

    public function getEventDate()
    {
        return $this->parameters->get('eventDate');
    }

    public function setEventDate($value)
    {
        $this->parameters->set('eventDate', $value);
    }

    public function getReason()
    {
        return $this->parameters->get('reason');
    }

    public function setReason($value)
    {
        $this->parameters->set('reason', $value);
    }

    public function getAdditionalData()
    {
        return $this->parameters->get('additionalData');
    }

    public function setAdditionalData($value)
    {
        $this->parameters->set('additionalData', $value);
    }

    public function getOriginalReference()
    {
        return $this->parameters->get('originalReference');
    }

    public function setOriginalReference($value)
    {
        $this->parameters->set('originalReference', $value);
    }

    public function getMerchantReference()
    {
        return $this->parameters->get('merchantReference');
    }

    public function setMerchantReference($value)
    {
        $this->parameters->set('merchantReference', $value);
    }

    public function getCurrency()
    {
        return $this->parameters->get('currency');
    }

    public function setCurrency($value)
    {
        $this->parameters->set('currency', $value);
    }

    public function getPspReference()
    {
        return $this->parameters->get('pspReference');
    }

    public function setPspReference($value)
    {
        $this->parameters->set('pspReference', $value);
    }

    public function getMerchantAccountCode()
    {
        return $this->parameters->get('merchantAccountCode');
    }

    public function setMerchantAccountCode($value)
    {
        $this->parameters->set('merchantAccountCode', $value);
    }

    public function getEventCode()
    {
        return $this->parameters->get('eventCode');
    }

    public function setEventCode($value)
    {
        $this->parameters->set('eventCode', $value);
    }

    public function getValue()
    {
        return $this->parameters->get('value');
    }

    public function setValue($value)
    {
        $this->parameters->set('value', $value);
    }

    public function getOperations()
    {
        return $this->parameters->get('operations');
    }

    public function setOperations($value)
    {
        $this->parameters->set('operations', $value);
    }

    public function getSuccess()
    {
        return $this->parameters->get('success');
    }

    public function setSuccess($value)
    {
        $this->parameters->set('success', $this->isTrue($value));
    }

    public function getPaymentMethod()
    {
        return $this->parameters->get('paymentMethod');
    }

    public function setPaymentMethod($value)
    {
        $this->parameters->set('paymentMethod', $value);
    }

    public function getLive()
    {
        return $this->parameters->get('live');
    }

    public function setLive($value)
    {
        $this->parameters->set('live', $this->isTrue($value));
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
        if ($this->getParameter('eventCode') == 'AUTHORISATION' && $this->getParameter('success')) {
            return self::STATUS_COMPLETED;
        } elseif ($this->getParameter('eventCode') == 'PENDING') {
            return self::STATUS_PENDING;
        }

        return self::STATUS_FAILED;
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

    /**
     * The response code that can be given to the payment gateway
     *
     * @return string
     */
    public function getResponseCode()
    {
        return 200;
    }

    public function isValid()
    {
        return  is_bool($this->getLive()) &&
                !empty($this->getParameter('currency')) &&
                !empty($this->getParameter('value')) &&
                !empty($this->getTransactionReference()) &&
                !empty($this->getEventCode()) &&
                !empty($this->getEventDate()) &&
                !empty($this->getMerchantAccountCode()) &&
                !empty($this->getMerchantReference()) &&
                !empty($this->getPaymentMethod()) &&
                is_bool($this->getSuccess());
    }
}