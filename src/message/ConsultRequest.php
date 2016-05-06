<?php 

namespace Omnipay\Elavon\Message;

class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount');

        $data = $this->createCommons('DoPaymentInquiry');

        $data->addChild('TransactionID', $this->getTransactionId());

        $data = $this->getMerchantDetails($data);

        return $data;
    }
}