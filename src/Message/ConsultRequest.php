<?php 

namespace Omnipay\Elavon\Message;

class ConsultRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('TransactionID');
        $data = $this->createCommons('DoPaymentInquiry');
        $data = $this->getTransactionIdXml($data);
        $data = $this->getMerchantDetails($data);
        return $data;
    }
}