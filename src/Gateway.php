<?php

namespace Omnipay\Elavon;

use Omnipay\Common\AbstractGateway;

/**
 * Elavon's Gateway
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Elavon';
    }

    public function getDefaultParameters()
    {
        return array(
            'TerminalID' => $this->getTerminalId(),
            'RegKey'   => $this->getRegKey()
        );
    }

    public function getTerminalId()
    {
        return $this->getParameter('TerminalID');
    }

    public function setTerminalId($terminalId)
    {
        return $this->getParameter('TerminalID', $terminalId);
    }

    public function getRegKey()
    {
        return $this->getParameter('RegKey');
    }

    public function setRegKey($regKey)
    {
        return $this->getParameter('RegKey', $regKey);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Elavon\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('App\Models\Elavon\Message\ElavonAuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Elavon\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('App\Models\Elavon\Message\ElavonPurchaseRequest', $parameters);
    }
}