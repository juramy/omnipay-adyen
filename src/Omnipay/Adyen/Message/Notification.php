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
            if (is_array($value) || is_object($value)) {
                $tempParams[$key] = (array) $value;

                foreach ((array) $value as $subKey => $subValue) {
                    $tempParams[$subKey] = $subValue;
                }

                continue;
            }

            $tempParams[$key] = $value;
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

    private function getAdditionalDataParameter($parameter)
    {
        if (is_array($this->getAdditionalData()) && isset($this->getAdditionalData()[$parameter])) {
            return $this->getAdditionalData()[$parameter];
        }

        return '';
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

    public function getHmacKey()
    {
        return $this->getParameter('hmacKey');
    }

    public function setHmacKey($value)
    {
        return $this->setParameter('hmacKey', $value);
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

    private function getDataWithoutSignature()
    {
        $data = array();

        $data['pspReference'] = $this->getPspReference();
        $data['originalReference'] = $this->getOriginalReference();
        $data['merchantAccountCode'] = $this->getMerchantAccountCode();
        $data['merchantReference'] = $this->getMerchantReference();
        $data['value'] = $this->getValue();
        $data['currencyCode'] = $this->getCurrency();
        $data['eventCode'] = $this->getEventCode();
        $data['success'] = var_export($this->getSuccess(), true);

        return $data;
    }

    private static function calculateSha256Signature($hmacKey, $params)
    {
        // The character escape function
        $escapeVal = function ($val) {
            return str_replace(':', '\\:', str_replace('\\', '\\\\', $val));
        };

        // Generate the signing data string
        $signData = implode(":", array_map($escapeVal, array_values($params)));

        // base64-encode the binary result of the HMAC computation
        $merchantSig = base64_encode(hash_hmac('sha256', $signData, pack("H*", $hmacKey), true));

        return $merchantSig;
    }

    private function validateSignature()
    {
        $calculatedSign = $this->calculateSha256Signature($this->getHmacKey(), $this->getDataWithoutSignature());

        return strcmp($calculatedSign, $this->getAdditionalDataParameter('hmacSignature')) == 0;
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
                is_bool($this->getSuccess() &&
                $this->validateSignature());
    }
}