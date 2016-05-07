<?php

namespace Omnipay\Elavon;

use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    private $transaction_id;
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTerminalId('0019410000000000000001');
        $this->gateway->setRegKey('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF');
        $this->gateway->setTestMode(1);
        $this->transaction_id = 2017; // Tem que mudar o id, ou modificar e mock os requests
    }

    public function testSendSuccess()
    {
        $this->gateway->setTokenization(1);
        $request = $this->gateway->authorize(
                    [
                        'amount'        => '10.00',
                        'currency'      => 'BRL',
                        'AdditionalID'  => $this->transaction_id,
                        'TransactionID' => $this->transaction_id,
                        'Recurring'     => 1,
                        'card' => [
                            'number'      => '4444111122223333',
                            'expiryMonth' => '10',
                            'expiryYear'  => '2018'
                        ]
                    ]);

        $this->assertInstanceOf('Omnipay\Elavon\Message\AuthorizeRequest', $request);
        $response = $request->send();
        $this->assertNotNull($response->getToken());
        $this->assertTrue($response->isSuccessful());
    }

    public function testSendFailure()
    {
        // Transaction id já existe, da duplicada
        $request = $this->gateway->authorize(
                    [
                        'amount'        => '10.00',
                        'currency'      => 'BRL',
                        'AdditionalID'  => 1,
                        'TransactionID' => 1,
                        'card' => [
                            'number'      => '4444111122223333',
                            'expiryMonth' => '10',
                            'expiryYear'  => '2018'
                        ]
                    ]);

        $this->assertInstanceOf('Omnipay\Elavon\Message\AuthorizeRequest', $request);
        $response = $request->send();
        $this->assertFalse($response->isSuccessful());
    }

    public function testTokenSuccess()
    {
        // Transaction id já existe, da duplicada
        $request = $this->gateway->authorize(
                    [
                        'amount'        => '10.00',
                        'currency'      => 'BRL',
                        'AdditionalID'  => 1,
                        'TransactionID' => 1,
                        'TokenIndicator'=> 1,
                        'card' => [
                            'number'      => 'B8F9A9C7D8C3C342106098924BB0158C3333'
                        ]
                    ]);

        $this->assertInstanceOf('Omnipay\Elavon\Message\AuthorizeRequest', $request);
        $response = $request->send();
        $this->assertFalse($response->isSuccessful());
    }

    public function testPurchaseSuccess()
    {
        $request = $this->gateway->purchase([
            'amount'        => '10.00',
            'currency'      => 'BRL',
            'TransactionID' => $this->transaction_id,
        ]);
        $this->assertInstanceOf('Omnipay\Elavon\Message\PurchaseRequest', $request);
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
    }

    public function testConsultSuccess()
    {
        $request = $this->gateway->consult([
            'TransactionID' => 1,
        ]);
        $this->assertInstanceOf('Omnipay\Elavon\Message\ConsultRequest', $request);
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
    }

    public function testConsultFailure()
    {
        $request = $this->gateway->consult([
            'TransactionID' => $this->transaction_id . '_LOL'
        ]);
        $this->assertInstanceOf('Omnipay\Elavon\Message\ConsultRequest', $request);
        $response = $request->send();
        $this->assertFalse($response->isSuccessful());
    }

    public function testCancelSuccess()
    {
        $request = $this->gateway->cancel([
            'TransactionID' => $this->transaction_id
        ]);
        $this->assertInstanceOf('Omnipay\Elavon\Message\CancelRequest', $request);
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
    }

    public function testCancelFailure()
    {
        $request = $this->gateway->cancel([
            'TransactionID' => $this->transaction_id . '_LOL'
        ]);
        $this->assertInstanceOf('Omnipay\Elavon\Message\CancelRequest', $request);
        $response = $request->send();
        $this->assertFalse($response->isSuccessful());
    }

}
