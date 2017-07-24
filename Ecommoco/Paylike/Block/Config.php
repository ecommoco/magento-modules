<?php

namespace Ecommoco\Paylike\Block;

class Config extends \Magento\Framework\View\Element\Template {

    protected $_cart;
    protected $helper;
	protected $customerSession;
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, 
	\Ecommoco\Paylike\Helper\Data $helper,
	\Magento\Checkout\Model\Cart $cart,
	 \Magento\Customer\Model\Session $customerSession
    ) {
        $this->helper = $helper;
        $this->_cart = $cart;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    protected function _getQuote() {
        return $this->_cart->getQuote();
    }

    public function getStoreName() {
        return $this->helper->getStoreName();
        
    }
	public function getStoreTitle()
	{
		return $this->helper->getStoreTitle();
	}
    public function getPublicApiKey() {

        return $this->helper->getPublicKey();
        
    }
	public function getPOS()
	{
		return $this->helper->getPOS();
	}
	public function getFee()
	{
		return $this->helper->getFee();
	}
	public function getAwsStatus()
	{
		return $this->helper->getAwsStatus();
	}
    public function getConfigJSON() {

        $quote = $this->_getQuote();
		
        $config = array(
            "title" => $this->getStoreTitle(),
            'description' => '',
            'currency' => $quote->getBaseCurrencyCode(),
            'amount' => $quote->getBaseGrandTotal() * 100,
        );
		$email = '';
		$name = '';
		$address = '';
		if($this->customerSession->getCustomer()->getId()){
			$email = $this->customerSession->getCustomer()->getEmail();			
		} else {
		
		}		
		$fields = array(				
		
		);        
		if($this->getAwsStatus()){
			 $config['custom']['address'] = $address;
		}
		
       
        $config['fields'] = $fields;
		
        $description = '';
        $products = array();
        foreach ($quote->getAllItems() as $item) {
            $product = array(
                'Name' => $item->getName(),
                'SKU' => $item->getSku(),
                "quantity" => $item->getQty(),
            );

            $description .= " " . $item->getQty() . "X " . $item->getName() . ' &';
            $products[] = $product;
        }
        $config['custom']['email'] = $email;
        $config['custom']['name'] = $name;
        $config['custom']['products'] = $products;
		
        $config['description'] = trim($description, "&");				
        return json_encode($config);
    }

}
