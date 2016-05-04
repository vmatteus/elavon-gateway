<?php 

namespace App\Models\Elavon\Message;

class ElavonPurchaseRequest extends ElavonAuthorizeRequest
{
    public function getData()
    {
        $this->transactionType = 'ccsale';

        return parent::getData();
    }
}