<?php 

namespace Omnipay\Elavon\Message;

class ElavonPurchaseRequest extends ElavonAuthorizeRequest
{
    public function getData()
    {
        $this->transactionType = 'ccsale';

        return parent::getData();
    }
}