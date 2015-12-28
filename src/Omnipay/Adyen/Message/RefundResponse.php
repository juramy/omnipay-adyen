<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractResponse;

class RefundResponse extends AbstractResponse
{
    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return (isset($this->data['response']) && $this->data['response'] === '[refund-received]');
    }

    // TODO: maybe the standard function should be used
    /**
     * @return mixed
     */
    public function getMessage()
    {
        if (isset($this->data['pspReference'], $this->data['response'])) {
            return $this->data['pspReference'] . ' / ' . $this->data['response'];
        }
    }
}
