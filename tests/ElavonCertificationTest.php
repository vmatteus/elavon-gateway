<?php

namespace Omnipay\Elavon;

use Omnipay\Tests\TestCase;

class ElavonCertificationTest extends TestCase
{

    private $transaction_id;
    private $gateway;

    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTerminalId('0019410000000000000001');
        $this->gateway->setRegKey('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF');
        $this->gateway->setTestMode(1);
        $this->transaction_id = 5010; // Tem que mudar o id, ou modificar e mock os requests
    }

    public function testDoPaymentSslToken() {

        // Coloquei isso pro teste não rodar
        return true;

        $request = $this->gateway->token(
        [
            'TransactionID' => $this->transaction_id,
            'card' => [
                'number'      => '4024007175388916',
                'expiryMonth' => '10',
                'expiryYear'  => '2018'
            ]
        ]);

        $responseToken = $request->send();

        print_r($responseToken->getData());

        $token = (String) $responseToken->getData()->Token[0];

        $request = $this->gateway->tokenValidation(
        [
            'TransactionID' => $this->transaction_id+1,
            'manualBrand'   => 'visa',
            'tokenString'   => 'DCCEF25609D73C428AF5AF3E69403B8A8916'
        ]);

        $responseValidation = $request->send();

        print_r($responseValidation->getData());

        $request = $this->gateway->authorize(
                [
                    'amount'        => '10.00',
                    'currency'      => 'BRL',
                    'AdditionalID'  => 1,
                    'TransactionID' => $this->transaction_id+2,
                    'TokenIndicator'=> 1,
                    'manualBrand'   => 'visa',
                    'tokenString'   => 'DCCEF25609D73C428AF5AF3E69403B8A8916'
                ]);

        $response = $request->send();
        print_r($response->getData());
    }    

}