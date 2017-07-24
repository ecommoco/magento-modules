<?php
namespace Ecommoco\Paylike\Controller\Standard;
use Ecommoco\Paylike\Gatway;
use Magento\Sales\Api\Data\TransactionInterface;

class Redirect extends \Ecommoco\Paylike\Controller\Index {	
	
	public function execute(){
		
		//$transactionId = $data->response->transactionId;
		$transactionId = $this->getRequest()->getParam('transactionId');
		$response['tid'] = $transactionId;
		$orderid = $this->_checkoutSession->getLastRealOrderId();
		$order = $this->_objectManager->create('\Magento\Sales\Model\Order')->loadByIncrementId($orderid);
		if (! $order->getGrandTotal() ){			
			return $this->resultJsonFactory->create()->setData($response);
		}
		$gt = str_replace('.','',$order->getGrandTotal());
		$amount= substr($gt,0,strlen($gt)-2);	
		$currency = $order->getOrderCurrencyCode();

	    $data     = array(
			'amount'   => $amount,
			'currency' => $currency
		);
		
		$helper = $this->_objectManager->create('\Ecommoco\Paylike\Helper\Data');
		 if ($order->getPayment()->getMethod() == 'paylike') {
			 if ( $helper->getPaymentMode() == 'authorize_capture') {
				$order->setTransactionId($transactionId)->save();			
				$invoice = $order->prepareInvoice();
				$invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
				$invoice->register();
				$transaction = $this->transactionFactory->create();
				$transaction->addObject($invoice)
						->addObject($invoice->getOrder())
						->save();

				if ($invoice && !$order->getEmailSent()) {
					$this->_orderSender->send($order);
					$order->addStatusHistoryComment(
							__('You notified customer about invoice #%1.', $invoice->getIncrementId())
					)->setIsCustomerNotified(
							true
					)->save();
				}
			 }
			
        }
		
		$this->getResponse()->setRedirect('/checkout/onepage/success');		
	}
}