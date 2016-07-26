<?php 

namespace Omnipay\Elavon\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class Response extends AbstractResponse
{
    public function isSuccessful()
    {
        return ((String)$this->data->Ack == 'Success');
    }

    public function getMessage()
    {
        if ($this->isSuccessful()) {
            return (String) $this->data->PaymentResponseDetails->Card->AuthorizationDetails->ResponseMessage;
            
        } 
        return (String) $this->data->ErrorDetails->ResponseMessage;
    }

    public function getCode()
    {
        if ($this->isSuccessful()) {
            return (String) $this->data->PaymentResponseDetails->Card->AuthorizationDetails->ResponseCode;
        }
        return (String) $this->data->ErrorDetails->ResponseCode;
    }

    public function getToken() {
        if (isset($this->data->PaymentResponseDetails->Card->AuthorizationDetails->Token)) {
            return (String) $this->data->PaymentResponseDetails->Card->AuthorizationDetails->Token;
        } else {
            return '';
        }
    }

    public function getStatus() {
        return (String) $this->data->StatusCode;
    }

    public function getResponseStatus() {
        if ($this->isSuccessful()) {
            return (String) $this->data->PaymentResponseDetails->Card->AuthorizationDetails->IssuerResponseCode;
        } else {
            return (String) $this->data->StatusCode;
        }
    }

    public function getTransactionId() {
        if ($this->isSuccessful()) {
            return (String) $this->data->TransactionID;
        } else {
            return (String) $this->data->ErrorDetails->TransactionID;
        }
    }
    
}