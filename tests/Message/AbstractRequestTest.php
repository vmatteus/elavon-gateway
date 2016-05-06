<?php

namespace Omnipay\Elavon\Message;

use Mockery;
use Omnipay\Tests\TestCase;

class AbstractRequestTest extends TestCase
{
    private $data;

    public function setUp()
    {
        $this->request = Mockery::mock('\Omnipay\Elavon\Message\AbstractRequest')->makePartial();
        $this->request->initialize();
        $this->request->setTerminalId('0019410000000000000001');
        $this->request->setRegKey('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF');
        $this->request->setDynamicDBA('TestMerchant');
        $this->data = $this->request->createCommons('DoPaymentTest', 0,0,0);
    }

    public function testTerminalId()
    {   
        $this->assertSame('0019410000000000000001', $this->request->getTerminalID());
    }

    public function testRegKey()
    {
        $this->assertSame('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF', $this->request->getRegKey());
    }

    public function testAdditionalId()
    {
        $this->request->setAdditionalId(12345);
        $this->assertSame(12345, $this->request->getAdditionalId());
    }

    public function testTransactionId()
    {
        $this->request->setTransactionId(12345);
        $this->assertSame(12345, $this->request->getTransactionId());
    }

    public function testTokenization()
    {
        $this->request->setTokenization(1);
        $this->assertSame(1, $this->request->getTokenization());

        $this->request->setTokenization(0);
        $this->assertSame(0, $this->request->getTokenization());
    }

    public function testDynamicDBA()
    {
        $this->assertSame('TestMerchant', $this->request->getDynamicDBA());
    }

    public function testCreateCommons() 
    {
        $xmlReturn = $this->data->saveXml();
        $dir = dirname(__FILE__);
        $xml = file_get_contents( $dir . '/../' . 'Mock/mockTestCreateCommons.xml');
        $this->assertXmlStringEqualsXmlString($xml, $xmlReturn);
    }

}