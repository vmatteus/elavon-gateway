<?php 

namespace Omnipay\Elavon\Message;

class TokenValidationRequest extends AbstractRequest
{

    public function getData()
    {

        $data = $this->createCommons('DoToken');
        $data = $this->getTransactionIdXml($data);
        $data = $this->getMerchantDetails($data);

        $tokenRequestDetails = $data->addChild('TokenRequestDetails')->addChild('Token');
        
        $cardData = $this->getTokenString();
        $tokenRequestDetails->addChild('Token', $cardData);

        $sptm = new \DateTimeZone("America/Sao_Paulo");
        $date = new \DateTime("Tomorrow", $sptm );
        $date->setTimezone( $sptm );

        $tokenSettingDetails = $data->addChild('TokenSettingDetails');
        $tokenSettingDetails->addChild('Expiration', $date->format('Y-m-d\TH:i:sP'));
        
        return $data;
    }
}