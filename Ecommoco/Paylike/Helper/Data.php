<?php
namespace  Ecommoco\Paylike\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $scopConfig = null;
	protected $_priceCurrency;

	const PAYLIKE_PUBLIC_KEY = 'payment/paylike/publickey';
	const PAYLIKE_PRIVATE_KEY = 'payment/paylike/app_key';
	const PAYLIKE_PAYMENT_MODE = 'payment/paylike/payment_mode';
	const PAYLIKE_PAYMENT_STORETITLE = 'payment/paylike/storetile';
	const PAYLIKE_PAYMENT_POS = 'payment/paylike/pos';
	const PAYLIKE_PAYMENT_PAYMENTORDER = 'payment/paylike/paymentorder';
	const PAYLIKE_PAYMENT_FEE = 'payment/paylike/fee';
	const PAYLIKE_PAYMENT_AWS_STATUS = 'payment/paylike/aws_status';
	
	public  function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scop_config,\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency)
	{
			$this->scopConfig = $scop_config;
  		    $this->_priceCurrency = $priceCurrency;
	}
	
	public function getPublicKey()
	{
		return $this->scopConfig->getValue(self::PAYLIKE_PUBLIC_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	public function getPrivateKey()
	{
			return $this->scopConfig->getValue(self::PAYLIKE_PRIVATE_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getPaymentMode(){
		return $this->scopConfig->getValue(self::PAYLIKE_PAYMENT_MODE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getCurrentCurrencyCode()
	{
	  return $this->_priceCurrency->getCurrency()->getCurrencyCode();
	}
	public function getStoreTitle()
	{
		return $this->scopConfig->getValue(self::PAYLIKE_PAYMENT_STORETITLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getPOS()
	{
		return $this->scopConfig->getValue(self::PAYLIKE_PAYMENT_POS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getPaymentOrder()
	{
		return $this->scopConfig->getValue(self::PAYLIKE_PAYMENT_PAYMENTORDER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getFee()
	{
		return $this->scopConfig->getValue(self::PAYLIKE_PAYMENT_FEE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	public function getAwsStatus()
	{
		return $this->scopConfig->getValue(self::PAYLIKE_PAYMENT_AWS_STATUS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	 public function getStoreName() {
        return $this->scopConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}