<?php
namespace Ecommoco\Paylike\Controller\Standard;
use Ecommoco\Paylike\Gatway;
class ProResponse extends \Ecommoco\Paylike\Controller\Index {	
	
	public function execute(){
		$transactionId = $this->getRequest()->getParam('tid');
		
		$amount = $this->getRequest()->getParam('amount');
		$currency = $this->getRequest()->getParam('currency');
		
		$this->quote = $this->getQuote();
		$this->quote->getPayment()->setMethod('paylike');
		$orderid = $this->cartManagement->placeOrder($this->quote->getId());
		$order = $this->_objectManager->create('\Magento\Sales\Model\Order')->load($orderid);
		$gt = str_replace('.','',$order->getGrandTotal());
		$amount= substr($gt,0,strlen($gt)-2);	
		$currency = $order->getOrderCurrencyCode();

	    $data     = array(
			'amount'   => $amount,
			'currency' => $currency
		);
		$helper = $this->_objectManager->create('Ecommoco\Paylike\Helper\Data');

		$privateAppKey = $helper->getPrivateKey();		
		
		\Ecommoco\Paylike\Gatway\Client::setKey($privateAppKey);
		
		//capture payment		
		if ( $helper->getPaymentMode() == ' capture') {
			$response['response'] = \Ecommoco\Paylike\Gatway\Transaction::capture( $transactionId, $data );
		} else {
			$response['response'] = \Ecommoco\Paylike\Gatway\Transaction::void( $transactionId, $data );
		}
		if ( is_object ( $this->_logger ) ) {
			$this->_logger->debug(json_encode($response));
		}
		
		//save transaction
		$paymentModel = $this->_objectManager->create('Ecommoco\Paylike\Model\Paylike');
		$payment = $order->getPayment();
		if ( $response['response'] !== false) {			
			$mode = 'capture';	
		}else{
			$mode = 'void';	
		}	
		$response['payment']=$paymentModel->postProcessing($order, $payment,['txnid'=>$transactionId],$mode);		
		
	
		return $this->resultJsonFactory->create()->setData($response);
	}
}