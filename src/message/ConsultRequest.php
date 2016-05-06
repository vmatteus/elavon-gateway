<?php 

namespace Omnipay\Elavon\Message;

class ConsultRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount');
        $data = $this->createCommons('DoPaymentInquiry', 1, 0, 0);
        $data = $this->getMerchantDetails($data);
        return $data;
    }
}