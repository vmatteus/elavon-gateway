<?php 

namespace Omnipay\Elavon\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    const ELAVON_VERSION  = '1.1.0';
    const ELAVON_XMLNS    = 'http://wsgate.elavon.com.br';
    const ELAVON_LANGUAGE = 'PT-BR';

    protected $testEndpoint = 'https://qaswsgate.elavon.com.br/wsgate/requesthandler';
    protected $liveEndpoint = 'https://wsgate.elavon.com.br/wsgate/requesthandler';

    public function getEndpoint()
    {
        return ($this->getTestMode()) ? $this->testEndpoint : $this->liveEndpoint;
    }

    public function getTerminalID()
    {
        return $this->getParameter('TerminalID');
    }

    public function setTerminalID($value)
    {
        $this->setParameter('TerminalID', $value);
    }

    public function getRegKey()
    {
        return $this->getParameter('RegKey');
    }

    public function setRegKey($value)
    {
        $this->setParameter('RegKey', $value);
    }

    public function getAdditionalId()
    {
        return $this->getParameter('AdditionalID');
    }

    public function setAdditionalId($value)
    {
        $this->setParameter('AdditionalID', $value);
    }

    public function getTransactionId()
    {
        return $this->getParameter('TransactionID');
    }

    public function setTransactionId($value)
    {
        $this->setParameter('TransactionID', $value);
    }

    public function setTokenization($boolean) {
        return $this->setParameter('tokenization', $boolean);
    }

    public function getTokenization() {
        return $this->getParameter('tokenization');
    }

    public function setDynamicDBA($dynamicDBA){
        $this->setParameter('DynamicDBA', $dynamicDBA);   
    }

    public function getDynamicDBA(){
        return $this->getParameter('DynamicDBA');   
    }

    protected function getIpAddress() 
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

    protected function getMerchantDetails($data) 
    {
        $merchantDetails = $data->addChild('MerchantDetails');
        $merchantDetails->addChild('TerminalID', $this->getTerminalID());
        $merchantDetails->addChild('RegKey', $this->getRegKey());
        if ($this->getDynamicDBA()) {
            $merchantDetails->addChild('DynamicDBA', $this->getDynamicDBA());    
        }
        return $data;
    }

    public function createCommons($transactionName, $setTransactionId = 1, $setPaymentAction = 1, $setIpAddress = 1) 
    {
        $data = new \SimpleXMLElement('<'. $transactionName .' />');
        $data->addAttribute('version', self::ELAVON_VERSION );
        $data->addAttribute('xmlns', self::ELAVON_XMLNS);
        $data->addChild('Language', self::ELAVON_LANGUAGE);
        
        if ($setTransactionId) {
            // Existent TransactionID from which the above TransactionID will be grouped. 
            // Note: Required for PaymentAction=Create mode. Optional for the other modes. If set, will be informative only.
            $data->addChild('TransactionID', $this->getTransactionId());
        }

        if ($setTransactionId) {
            $data->addChild('PaymentAction', 'Auth');
        }

        if ($setIpAddress) {
            $data->addChild('IPAddress', $this->getIpAddress());
        }

        $data = $this->getMerchantDetails($data);

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