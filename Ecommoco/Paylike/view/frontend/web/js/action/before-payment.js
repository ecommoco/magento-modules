define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/customer-data',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Ui/js/modal/alert',
		'Magento_Checkout/js/model/totals',		
		'Ecommoco_Paylike/js/model/popup',	
    ],
    function ($, quote, customerData, customer, fullScreenLoader, alert, totals, popupCall) {
        'use strict';

        return function (messageContainer) {

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
			var key	= window.checkoutConfig.paylike;
			var title = window.checkoutConfig.storetitle;
			var pos = window.checkoutConfig.pos;
			var total= totals.totals();
			var amount = total.grand_total;
			var currency = total.base_currency_code;
			
			return popupCall(email, firstname, lastname, amount, currency, pos, title, key, address);
			
				
        };
    }
);


