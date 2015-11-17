<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Adyen Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://live.adyen.com/hpp/pay.shtml';
    protected $testEndpoint = 'https://test.adyen.com/hpp/pay.shtml';        // Hosted Payment Pages (Single):

    //~ protected $testEndpoint = 'https://test.adyen.com/hpp/select.shtml'; // Hosted Payment Pages (multiple)

    public function getMerchantAccount()
    {
        return $this->getParameter('merchantAccount');
    }

    /**
     * @param String $value The merchant account you want to process this payment with.
     */
    public function setMerchantAccount($value)
    {
        return $this->setParameter('merchantAccount', $value);
    }

    public function getSkinCode()
    {
        return $this->getParameter('skinCode');
    }

    /**
     * @param String $value The code of the skin to be used.
     * You can have more than one skin associated with your account if you require a different branding.
     */
    public function setSkinCode($value)
    {
        return $this->setParameter('skinCode', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    /**
     * @param String $value A Secret known by You and Adyen, no one else.
     */
    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function getShipBeforeDate()
    {
        return $this->getParameter('shipBeforeDate');
    }

    /**
     * @param String $value The date by which the goods or services specified in the order must be shipped or rendered.
     */
    public function setShipBeforeDate($value)
    {
        return $this->setParameter('shipBeforeDate', $value);
    }

    public function getSessionValidity()
    {
        return $this->getParameter('sessionValidity');
    }

    /**
     * @param String $value The final time by which a payment needs to have been made. This is especially useful for
     * tickets/reservations, where you want to “lock” the item for sale for only a short time and payments made
     * after this time would lead to extra costs and administrative hassle. Format is YYYY-MM-DDThh:mm:ssTZD.
     * TZD is the Time Zone Designator which can either be the letter 'Z' or +hh:mm or -hh:mm.
     */
    public function setSessionValidity($value)
    {
        return $this->setParameter('sessionValidity', $value);
    }

    public function getMerchantReference()
    {
        return $this->getParameter('merchantReference');
    }

    /**
     * @param String $value The (merchant) reference for this payment.
     * This reference will be used in all communication to the merchant about the status of the payment.
     * Although it is a good idea to make sure it is unique, this is not a requirement.
     */
    public function setMerchantReference($value)
    {
        return $this->setParameter('merchantReference', $value);
    }

    public function getShopperLocale()
    {
        return $this->getParameter('shopperLocale');
    }

    /**
     * Set the langauge of the Payment Form on the Adyen site.
     *
     * The default locale is en_GB.
     * A combination of language code and country code to specify the language used in the session.
     * e.g. en_GB for (British) English).
     * Use just the language code when the country distinction is not required (i.e. fr, not fr_FR).
     *
     * @param String $value Optional locale of the payer e.g. "en_GB" or "fr".
     */
    public function setShopperLocale($value)
    {
        return $this->setParameter('shopperLocale', $value);
    }

    public function getAllowedMethods()
    {
        return $this->getParameter('allowedMethods');
    }

    /**
     * @param String $value A comma-separated list of allowed payment methods.
     * This acts as a filter on the payment methods which would normally be available in the skin.
     * Only the ones in this list will be shown (if available); all others will be ignored. No spaces are allowed.
     * Note that this parameter is optional.
     * If the parameter is not used the value for this field in the merchantSignature computation is an empty String.
     */
    public function setAllowedMethods($value)
    {
        return $this->setParameter('allowedMethods', $value);
    }

    public function getBlockedMethods()
    {
        return $this->getParameter('blockedMethods');
    }

    /**
     * @param String $value A comma-separated list of payment methods which should not be made available.
     * This acts as a filter on the payment methods which would normally be available in
     * the skin. The methods listed will be removed from the list of available payment methods. No spaces are
     * allowed. Note that this parameter is optional. If the parameter is not used the value for this field in the
     * merchantSignature computation is an empty String.
     */
    public function setBlockedMethods($value)
    {
        return $this->setParameter('blockedMethods', $value);
    }

    public function getShopperReference()
    {
        return $this->getParameter('shopperReference');
    }

    /**
     * Optional. An ID that uniquely identifies the shopper (e.g. a customer id in a shopping cart system).
     * Recommended as it is used in a velocity fraud check.
     */
    public function setShopperReference($value)
    {
        return $this->setParameter('shopperReference', $value);
    }

    public function getShopperEmail()
    {
        return $this->getParameter('shopperEmail');
    }

    /**
     * Optional. The email address of the shopper. Recommended as it is used in a velocity fraud check.
     */
    public function setShopperEmail($value)
    {
        return $this->setParameter('shopperEmail', $value);
    }

    public function getCountryCode()
    {
        return $this->getParameter('countryCode');
    }

    /**
     * Optional country code of shopper.
     * By default the payment methods offered to the shopper are filtered based on the country which the IP
     * address of the shopper is mapped to. This prevents a UK shopper from being presented with a German
     * payment method like ELV. This IP-to-country mapping is not 100% accurate so if you have already
     * established the country of the shopper you may set it explicitly using the countryCode parameter.
     * This parameter is optional and is not used as part of the signing data.
     */
    public function setCountryCode($value)
    {
        return $this->setParameter('countryCode', $value);
    }

    public function getBrandCode()
    {
        return $this->getParameter('brandCode');
    }

    /**
     * Optional payment method used to process the payment.
     */
    public function setBrandCode($value)
    {
        return $this->setParameter('brandCode', $value);
    }

    public function getIssuerId()
    {
        return $this->getParameter('issuerId');
    }

    /**
     * Optional issuer ID used to process the payment.
     */
    public function setIssuerId($value)
    {
        return $this->setParameter('issuerId', $value);
    }

    public function getShopperStatement()
    {
        return $this->getParameter('shopperStatement');
    }

    /**
     * Optional field in your payment request if you want to include a variable shopper statement.
     *
     * You can include placeholders for the references. For example:
     * ${reference} for the merchant reference
     * ${pspReference} for the psp reference.
     *
     * Note:
     * Not all acquirers support dynamic shopper statements.
     * Maximum allowed character length: 135 characters
     * Allowed characters: a-zA-Z0-9.,-?|
     * If you set the shopperStatement field, it is included in the HMAC calculation.
     * Not supported for all payments methods, for further information contact support.
     */
    public function setShopperStatement($value)
    {
        return $this->setParameter('shopperStatement', substr($value, 0, 135));
    }

    public function getOrderData()
    {
        return $this->getParameter('orderData');
    }

    public function getOfferEmail()
    {
        return $this->getParameter('offerEmail');
    }

    /**
     * Optional. The email address of the shopper.
     *
     * If offerEmail is set to prompt, an extra Pay by Email payment method is added to the available payment method list.
     *
     * If the shopper selects this option, they receive an email with a link that they can use to complete the payment.
     *
     * The sessionValidity time value determines the link validity.
     */
    public function setOfferEmail($value)
    {
        return $this->setParameter('offerEmail', $value);
    }

    /**
     * Optional  order details to display to the shopper on the payment review page.
     *
     * An HTML fragment containing the order details to display to the shopper on the payment review page,
     * just before the shopper proceeds to the final order confirmation.
     *
     * Data is compressed and encoded in the session to prevent data corruption,
     * for example in case the locale is set to non-Latin character sets.
     *
     * Compression: GZIP & Encoding: Base64
     */
    public function setOrderData($value)
    {
        return $this->setParameter('orderData', base64_encode(gzencode($value)));
    }

    public function getMerchantReturnData()
    {
        return $this->getParameter('merchantReturnData');
    }

    /**
     * Optional field value that will be appended as-is to the return URL.
     *
     * This field value is appended as-is to the return URL when the shopper completes, or abandons,
     * the payment process and is redirected to your web shop.
     *
     * Typically, this field is used to hold and transmit a session ID. Maximum allowed character length: 128 characters.
     *
     * Note:
     * When you include the merchantReturnData parameter in your request, Adyen cannot guarantee that a payment method works as expected.
     * Some redirect methods such as iDEAL apply size limitations to payment requests.
     * If by including merchantReturnData in a request causes it to exceed the allowed maximum size, the payment can fail.
     */
    public function setMerchantReturnData($value)
    {
        return $this->setParameter('merchantReturnData', substr($value, 0, 128));
    }

    public function getFraudOffset()
    {
        return $this->getParameter('fraudOffset');
    }

    /**
     * Optional fraud offset used to process the payment.
     *
     * An integer value that adds up to the normal fraud score.
     * The value can be either a positive or negative integer.
     */
    public function setFraudOffset($value)
    {
        return $this->setParameter('fraudOffset', $value);
    }

    private function getDataWithoutSignature()
    {
        $this->validate('secret', 'amount', 'currency', 'shipBeforeDate', 'merchantReference', 'skinCode', 'merchantAccount', 'sessionValidity');
        $data = array();

        // Compulsory fields (in the same order as listed on https://docs.adyen.com/display/TD/HPP+payment+fields)
        $data['merchantReference'] = $this->getMerchantReference();
        $data['paymentAmount'] = $this->getAmountInteger();
        $data['currencyCode'] = $this->getCurrency();
        $data['shipBeforeDate'] = $this->getShipBeforeDate();
        $data['skinCode'] = $this->getSkinCode();
        $data['merchantAccount'] = $this->getMerchantAccount();
        $data['sessionValidity'] = $this->getSessionValidity();
        // merchantSig will be set later

        // Optional fields (again in the same order as listed on https://docs.adyen.com/display/TD/HPP+payment+fields)
        $data['shopperLocale'] = $this->getShopperLocale();
        $data['orderData'] = $this->getOrderData();
        $data['merchantReturnData'] = $this->getMerchantReturnData();
        $data['countryCode'] = $this->getCountryCode();
        $data['shopperEmail'] = $this->getShopperEmail();
        $data['shopperReference'] = $this->getShopperReference();
        $data['allowedMethods'] = $this->getAllowedMethods();
        $data['blockedMethods'] = $this->getBlockedMethods();
        $data['offset'] = $this->getFraudOffset();
        $data['brandCode'] = $this->getBrandCode();
        $data['issuerId'] = $this->getIssuerId();
        $data['shopperStatement'] = $this->getShopperStatement();
        $data['offerEmail'] = $this->getOfferEmail();
        $data['resURL'] = $this->getReturnUrl();

        return $data;
    }

    public function getData()
    {
        $data = $this->getDataWithoutSignature();
        $data['merchantSig'] = $this->generateSignature(); // this field is also required

        return $data;
    }

    /**
     * The Adyen signature is computed using the HMAC algorithm with the SHA-256 hashing function using the shared
     * secret configured in the skin.
     * The input is the concatenated values of a number of the payment session fields.
     * It is in Base64 encoded format.
     */
    private function generateSignature()
    {
        $params = $this->getDataWithoutSignature();

        // The character escape function
        $escapeVal = function ($val) {
            return str_replace(':', '\\:', str_replace('\\', '\\\\', $val));
        };

        // Sort the array by key using SORT_STRING order
        ksort($params, SORT_STRING);

        // Generate the signing data string
        $signData = implode(':', array_map($escapeVal, array_merge(array_keys($params), array_values($params))));

        // base64-encode the binary result of the HMAC computation
        $merchantSig = base64_encode(hash_hmac('sha256', $signData, pack('H*', $this->getSecret()), true));

        return $merchantSig;
    }

    /**
     * Send the request with specified data.
     * Adyen is a Off-site gateway - No need to send request, instead we need to redirect customer to Adyen site
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }

    /**
     * Optional custom endpoint that will be used to process the payment.
     */
    public function setCustomEndpoint($value)
    {
        return $this->setParameter('customEndpoint', $value);
    }

    private function getCustomEndpoint()
    {
        return $this->getParameter('customEndpoint');
    }

    public function getEndpoint()
    {
        if (!empty($this->getCustomEndpoint())) {
            return $this->getCustomEndpoint();
        }

        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}