<?php 

namespace Omnipay\Elavon\Message;

class TokenRequest extends AbstractRequest
{

    public function getData()
    {
        $this->validate('card');
        $this->getCard()->validate();

        $data = $this->createCommons('DoToken');
        $data = $this->getTransactionIdXml($data);
        $data = $this->getMerchantDetails($data);

        if ($this->getManualBrand()) {
            $brand = $this->getBrandElavon($this->getManualBrand());
        } else {
            if (empty($this->getCard()->getBrand())) {
                throw new \Exception("Não foi possível definir a bandeira do cartão");
            }
            $brand = $this->getBrandElavon($this->getCard()->getBrand());
        }

        $tokenRequestDetails = $data->addChild('TokenRequestDetails')->addChild('Card');

        $tokenRequestDetails->addChild('CardProduct', $brand . '.Credit');
        
        $cardData = $this->getCard()->getNumber();
        $expMonth = str_pad($this->getCard()->getExpiryMonth(), 2, '0', STR_PAD_LEFT);
        $expYear  = substr($this->getCard()->getExpiryYear(), -2, 2);
        $cardData = $cardData . '=' . $expMonth . $expYear;
        $tokenRequestDetails->addChild('CardData', $cardData);

        $tokenSettingDetails = $data->addChild('TokenSettingDetails');
        $tokenSettingDetails->addChild('Format', 'Strong');
        
        return $data;
    }
}