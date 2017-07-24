<?php
namespace Ecommoco\Paylike\Model;
use Magento\Checkout\Model\ConfigProviderInterface;
class PaylikeConfigProvider implements ConfigProviderInterface
{		
	public function getConfig()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$helper = $objectManager->create('Ecommoco\Paylike\Helper\Data');
		
		$config['paylike'] = $helper->getPublicKey();
		$config['storetitle'] = $helper->getStoreTitle();
		$config['pos'] = $helper->getPOS();
		$config['paymentorder'] = $helper->getPaymentOrder();
		$config['fee'] = $helper->getFee();
		$config['aws_status'] = $helper->getAwsStatus();
		
		return $config;
	}
}
