<?php 

namespace Omnipay\Elavon\Message;

class PurchaseRequest extends AuthorizeRequest
{
    public function getData()
    {
        $this->transactionType = 'ccsale';

        return parent::getData();
    }
}