<?php
namespace Ecommoco\Paylike\Controller\Standard;
class Order extends \Ecommoco\Paylike\Controller\Index {

    public function execute() {		
		$params = [];
		$params['orderid']=$this->_checkoutSession->getLastRealOrderId();		
		$params['amount']='';
		$params['currency']='';
		if ( null != $params['orderid'] ) {
			$order = $this->_objectManager->create('\Magento\Sales\Model\Order')->loadByIncrementId($params['orderid']);
			$gt = str_replace('.','',$order->getGrandTotal());			
			$params['amount']= $order->getGrandTotal();
			$params['currency'] = $order->getOrderCurrencyCode();			
		}				
		return $this->resultJsonFactory->create()->setData($params);
	}
}