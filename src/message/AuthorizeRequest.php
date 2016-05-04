<?php 

namespace Omnipay\Elavon\Message;

class AuthorizeRequest extends AbstractRequest
{
    
    public function getData()
    {
        $this->validate('amount', 'card');
        $this->getCard()->validate();

        $data = new \SimpleXMLElement('<DoPayment />');
        $data->addAttribute('version', '1.1.0');
        $data->addAttribute('xmlns', 'http://wsgate.elavon.com.br');
        
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

        $httpResponse = $this->httpClient->post($this->getEndpoint(), null, $xml)
            ->setHeader('Content-Type', 'text/xml; charset=utf-8')
            ->send();

        dd($httpResponse->xml());

        return $this->createResponse($httpResponse->xml());
    }
}