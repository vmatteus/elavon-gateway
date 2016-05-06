<?php 

namespace Omnipay\Elavon\Message;

class ConsultRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount');
        $data = $this->createCommons('DoPaymentInquiry');
        $data = $this->getMerchantDetails($data);
        $data = $this->getTransactionIdXml($data);
        return $data;
    }
}