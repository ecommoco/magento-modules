<?php
namespace Ecommoco\Paylike\Controller\Standard;
use Ecommoco\Paylike\Gatway;
class Response extends \Ecommoco\Paylike\Controller\Index {	
	
	public function execute(){
		$transactionId = $this->getRequest()->getParam('tid');				
		$orderid = $this->_checkoutSession->getLastRealOrderId();
		$order = $this->_objectManager->create('\Magento\Sales\Model\Order')->loadByIncrementId($orderid);
		if (! $order->getGrandTotal() ){
			return $this->resultJsonFactory->create()->setData($orderid);
		}
		
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
		$response['response'] = true;
		$response['tid'] = $transactionId;
		$this->getResponse()->setRedirect('/checkout/onepage/success');		
	}
}