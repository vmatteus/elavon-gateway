<?php 

namespace Omnipay\Elavon\Message;

class CancelRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('TransactionID');
        $data = $this->createCommons('DoPaymentCancel');
        $data = $this->getTransactionIdXml($data);
        $data = $this->getMerchantDetails($data);
        return $data;
    }
}