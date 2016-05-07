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

    public function setRecurring($recurring) {
        $this->setParameter('Recurring', $recurring);
    }

    public function getRecurring() {
        return $this->getParameter('Recurring');
    }

    public function setTokenIndicator($tokenIndicator) {
        $this->setParameter('TokenIndicator', $tokenIndicator);
    }

    public function getTokenIndicator() {
        return $this->getParameter('TokenIndicator');
    }

    public function setManualBrand($brand) {
        $this->setParameter('manualBrand', $brand);
    }

    public function getManualBrand() {
        return $this->getParameter('manualBrand');
    }

    protected function getIpAddress() 
    {
        $ipaddress = '0.0.0.0';
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

    protected function getTransactionIdXml($data) {
        // Existent TransactionID from which the above TransactionID will be grouped. 
        // Note: Required for PaymentAction=Create mode. Optional for the other modes. If set, will be informative only.
        $data->addChild('TransactionID', $this->getTransactionId());
        return $data;
    }

    protected function getPaymentActionXml($data) {
        $data->addChild('PaymentAction', 'Auth');
        return $data;
    }

    protected function getIpAddressXml($data) {
        $data->addChild('IPAddress', $this->getIpAddress());
        return $data;
    }

    public function createCommons($transactionName) 
    {
        $data = new \SimpleXMLElement('<'. $transactionName .' />');
        $data->addAttribute('version', self::ELAVON_VERSION );
        $data->addAttribute('xmlns', self::ELAVON_XMLNS);
        $data->addChild('Language', self::ELAVON_LANGUAGE);

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