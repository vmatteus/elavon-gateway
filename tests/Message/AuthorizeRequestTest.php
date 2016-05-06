<?php

namespace Omnipay\Elavon\Message;

use Mockery;
use Omnipay\Tests\TestCase;

class AuthorizeRequestTest extends TestCase
{
    private $data;

    public function setUp()
    {
        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => '12.00',
                'currency' => 'BRL',
                'card' => $this->getValidCard(),
            )
        );
        $this->request->setTerminalId('0019410000000000000001');
        $this->request->setRegKey('FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF');
        $this->request->setDynamicDBA('TestMerchant');
        $this->data = $this->request->createCommons('DoPaymentTest');
    }

    public function testGetData() {
        $data = $this->request->getData();
        $this->assertSame(1200, (Int) $data->PurchaseDetails->TotalAmount[0]);
        $this->assertSame(1200, (Int) $data->PaymentRequestDetails->Card->AuthorizationAmount[0]);
        $this->assertSame('BRL', (String) $data->PurchaseDetails->TotalAmount[0]->attributes()[0]);
        $this->assertSame('BRL', (String) $data->PaymentRequestDetails->Card->AuthorizationAmount[0]->attributes()[0]);
    }

}