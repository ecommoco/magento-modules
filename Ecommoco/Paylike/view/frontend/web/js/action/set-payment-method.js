define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/customer-data',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Ui/js/modal/alert',
		'Magento_Checkout/js/model/totals',
		'mage/url',
		'Ecommoco_Paylike/js/model/popup-call',	
    ],
    function ($, quote, customerData, customer, fullScreenLoader, alert, totals, urlBuilder, popupCall) {
        'use strict';

        return function (messageContainer) {
			var key	= window.checkoutConfig.paylike;
			var title = window.checkoutConfig.storetitle;
			var pos = window.checkoutConfig.pos;					
			var fee = window.checkoutConfig.fee;					
			var aws_status = window.checkoutConfig.aws_status;					
            var serviceUrl, email;
			var addresses;
			var address_;
            if (!customer.isLoggedIn()) {
				address_= quote.billingAddress();
                email = quote.guestEmail;
            } else {
                email = customer.customerData.email;				
				var addresses = customer.getBillingAddressList();			
				address_ = addresses[0]; 
            }
			
			console.log(customer.customerData);
			var firstname = address_.firstname;
			var lastname = address_.lastname;
			

			var address = '';
			address += address_.street[0] +', '+ address_.city + ', ';
			if(address_.region != ''){
				address += address_.region+ ', ';
			}
			address += address_.countryId +', '+ address_.postcode;
			
			fullScreenLoader.stopLoader();			
            serviceUrl = urlBuilder.build('paylike/standard/order', {});  			
			var total= totals.totals();
			var amount = total.grand_total;
			var currency = total.base_currency_code;									
			return popupCall(email, firstname, lastname, amount, currency, pos, title, key, address, fee, aws_status);		
					
        };
    }
);


