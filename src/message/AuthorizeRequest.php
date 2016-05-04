<?php 

namespace Omnipay\Elavon\Message;

class ElavonAuthorizeRequest extends ElavonAbstractRequest
{
    
    public function getData()
    {
        $this->validate('amount', 'card');
        $this->getCard()->validate();

        $data = new \SimpleXMLElement('<DoPayment />');
        $data->addAttribute('version', '1.1.0');
        $data->addAttribute('xmlns', $this->getEndpoint());
        
        $data->addChild('Language', 'PT-BR');
        $data->addChild('TransactionID', uniqid()); // TROCAR

        return $data;
    }

    public function sendData($data)
    {

        $document = new \DOMDocument('1.0', 'utf-8');

        $autorizacao = $document->createElementNS('Teste', 'requisicao-autorizacao-tid');

        $node = $document->importNode(dom_import_simplexml($data), true);
        $document->appendChild($node);

        $xml = $document->saveXML();

        dd($xml);

        $httpResponse = $this->httpClient->post($this->getEndpoint() . '/process.do', null, http_build_query($data))
            ->setHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->send();

        dd($httpResponse->getBody());

        return $this->createResponse($httpResponse->getBody());
    }
}