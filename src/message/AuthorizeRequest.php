<?php 

namespace Omnipay\Elavon\Message;

class AuthorizeRequest extends AbstractRequest
{

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
        
        return $data;
    }

    private function getBrandElavon($brand) 
    {
        switch ($brand) {
            case 'mastercard':
                return 'MA';
                break;
            
            default:
                return ucfirst($brand);
                break;
        }
    }

    public function getEciCard($brand) {
        switch ($brand) {
            case 'mastercard':
                return 0;
                break;
            
            default:
                return 7;
                break;
        }
    }

    private function getPaymentRequestDetailsCard($data) 
    {
        $paymentRequestDetailsCard = $data->addChild('PaymentRequestDetails')->addChild('Card');
        $paymentRequestDetailsCard->addChild('CardProduct', $this->getBrandElavon($this->getCard()->getBrand()) . '.Credit');

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
        } else {
            $paymentRequestDetailsCard->addChild('CVV2Indicator', 0);
        }
        
        $authorizationAmount = $paymentRequestDetailsCard->addChild('AuthorizationAmount', $this->getAmountInteger());
        $authorizationAmount->addAttribute('currencyCode', $this->getCurrency());

        $eci_send = 1;
        if ($this->getRecurring()) {
            $paymentRequestDetailsCard->addChild('Recurring', 1);
            $eci_send = 0;
        } 

        $paymentRequestDetailsCard->addChild('POSEntryCapability', '01');
        $paymentRequestDetailsCard->addChild('CardEntryMode', '01');
        
        if ($eci_send) {
            $paymentRequestDetailsCard->addChild('ECI', $this->getEciCard($this->getCard()->getBrand()));
        }

        if ($this->getParameter('tokenization')) {
            $tokenSettingDetails = $paymentRequestDetailsCard->addChild('TokenSettingDetails');
            $tokenSettingDetails->addChild('Format', 'Strong');
        }

        return $data;
    }

    public function getData()
    {
        $this->validate('amount', 'card');
        $this->getCard()->validate();

        $data = $this->createCommons('DoPayment');

        $data = $this->getTransactionIdXml($data);
        $data = $this->getPaymentActionXml($data);
        $data = $this->getIpAddressXml($data);
        $data = $this->getMerchantDetails($data);
        $data = $this->getPurchaseDetails($data);
        $data = $this->getPaymentRequestDetailsCard($data);

        return $data;
    }
}