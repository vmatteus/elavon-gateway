<?php 

namespace Omnipay\Elavon\Message;

class AuthorizeRequest extends AbstractRequest
{

    const ELAVON_VERSION  = '1.1.0';
    const ELAVON_XMLNS    = 'http://wsgate.elavon.com.br';
    const ELAVON_LANGUAGE = 'PT-BR';

    protected function createDoPaymentHeader() 
    {
        $data = new \SimpleXMLElement('<DoPayment />');
        $data->addAttribute('version', self::ELAVON_VERSION );
        $data->addAttribute('xmlns', self::ELAVON_XMLNS);
        return $data;
    }

    private function getIpAddress() 
    {
        $ipaddress = '0.0.0.0';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        return $ipaddress;
    }

    private function getMerchantDetails($data) 
    {
        $baseData = $this->getBaseData();
        $merchantDetails = $data->addChild('MerchantDetails');
        $merchantDetails->addChild('TerminalID', $baseData['TerminalID']);
        $merchantDetails->addChild('RegKey', $baseData['RegKey']);
        return $data;
    }

    private function getPurchaseDetails($data) 
    {
        $purchaseDetails = $data->addChild('PurchaseDetails');
        
        //Merchant additional data field for this purchase. This data will be present in the reconciliation file.
        //Note: If you do not have a value for this purpose, we suggest you to make use of your TransactionID.
        $purchaseDetails->addChild('AdditionalID', $this->getAdditionalId());

        $totalAmount = $purchaseDetails->addChild('TotalAmount', $this->getAmountInteger());
        $totalAmount->addAttribute('currencyCode', $this->getCurrency());

        $sptm = new \DateTimeZone("America/Sao_Paulo");
        $date = new \DateTime("NOW", $sptm );
        $date->setTimezone( $sptm );

        $purchaseDetails->addChild('DateTime', $date->format('Y-m-d\TH:i:sP'));
        $purchaseDetails->addChild('OrderDescription', $this->getDescription());

        return $data;
    }

    private function getPaymentRequestDetailsCard($data) 
    {
        $paymentRequestDetailsCard = $data->addChild('PaymentRequestDetails')->addChild('Card');
        $paymentRequestDetailsCard->addChild('CardProduct', ucfirst($this->getCard()->getBrand(). '.Credit'));

        // Card Data for the Payment.
        // 1) Manually entered card data
        // CardNumber=CardExpiration (MMYY)
        // e.g.: 4444111122223333=0715
        // 2) Track 1 or 2 data as read with the start
        // and end sentinels removed. e.g.:B4444111122223333^ELAVON TEST CARD^1507101543213961456
        // 3) Token value e.g.:1ED66AA3903549DB9B01CAA76455B9C00715
        $cardData = $this->getCard()->getNumber();
        $expMonth = $this->getCard()->getExpiryMonth();
        $expYear  = substr($this->getCard()->getExpiryYear(), -2, 2);
        $cardData = $cardData . '=' . $expMonth . $expYear;
        $paymentRequestDetailsCard->addChild('CardData', $cardData);
        
        if ($this->getCard()->getCvv()) {
            $paymentRequestDetailsCard->addChild('CVV2Indicator', 1);
            $paymentRequestDetailsCard->addChild('CVV2', $this->getCard()->getCvv());
        }
        
        $authorizationAmount = $paymentRequestDetailsCard->addChild('AuthorizationAmount', $this->getAmountInteger());
        $authorizationAmount->addAttribute('currencyCode', $this->getCurrency());

        $paymentRequestDetailsCard->addChild('POSEntryCapability', '01');
        $paymentRequestDetailsCard->addChild('CardEntryMode', '01');
        $paymentRequestDetailsCard->addChild('ECI', 7);

        return $data;
    }

    public function getData()
    {
        $this->validate('amount', 'card');
        $this->getCard()->validate();

        $data = $this->createDoPaymentHeader();
        
        $data->addChild('Language', self::ELAVON_LANGUAGE);
        
        // Existent TransactionID from which the above TransactionID will be grouped. 
        // Note: Required for PaymentAction=Create mode. Optional for the other modes. If set, will be informative only.
        $data->addChild('TransactionID', $this->getTransactionId());
        
        $data->addChild('PaymentAction', 'Auth');
        $data->addChild('IPAddress', $this->getIpAddress());

        $data = $this->getMerchantDetails($data);        
        $data = $this->getPurchaseDetails($data);
        $data = $this->getPaymentRequestDetailsCard($data);

        return $data;
    }

    public function sendData($data)
    {

        $document = new \DOMDocument('1.0', 'utf-8');

        $node = $document->importNode(dom_import_simplexml($data), true);
        $document->appendChild($node);

        $xml = $document->saveXML();

        $httpResponse = $this->httpClient->post($this->getEndpoint(), null, $xml)
            ->setHeader('Content-Type', 'text/xml; charset=utf-8')
            ->send();

        return $this->response = new Response($this, $httpResponse->xml());
    }
}