/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
		'ko',
		'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Ecommoco_Paylike/js/action/set-payment-method',  
		'Magento_Checkout/js/action/place-order',		
		'Magento_Checkout/js/model/quote',
		'Magento_Customer/js/model/customer',
		'mage/url',
		'Magento_Ui/js/model/messages',
		'Magento_Checkout/js/model/payment/additional-validators',		
    ],
    function (ko, $, Component, setPaymentMethod, placeOrderAction, quote, customer, url, Messages, additionalValidators) {
        'use strict';

        return Component.extend({
			defaults:{
				'template':'Ecommoco_Paylike/payment/paylike',	
				 paylikeTxnId: ''				
			},		
			paylikeTxnId:'',
			redirectAfterPlaceOrder: false,			
			afterPlaceOrder: function () {
				window.location.replace(url.build('paylike/standard/response?tid='+ this.paylikeTxnId));
			},
			isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null),
			placeOrder: function (data, event) {
				console.log("placing order....");
				var self = this,
						placeOrder;

				if (event) {
					event.preventDefault();
				}

				if (this.validate() && additionalValidators.validate()) {
					this.isPlaceOrderActionAllowed(false);
					placeOrder = placeOrderAction(this.getData(), this.redirectAfterPlaceOrder, this.messageContainer);

					$.when(placeOrder).fail(function () {
						self.isPlaceOrderActionAllowed(true);
					}).done(this.afterPlaceOrder.bind(this));
					return true;
				}
				return false;
			},
			paylike: function(){ //to pay before order placed				
				//setPaymentMethod();
				var fee = window.checkoutConfig.fee;
				var amount = quote.totals()['base_grand_total'];
				amount = Number.parseFloat(amount) + Number.parseFloat(fee);
				paylikeConfig.amount = amount *100;				
				var addresses;
				var address_;
				var email;
				if (!customer.isLoggedIn()) {						
					address_= quote.billingAddress();
					email = quote.guestEmail;
				} else {
					email = customer.customerData.email;
					var addresses = customer.getBillingAddressList();			
					address_ = addresses[0]; 
				}
				var firstname = address_.firstname;
				var lastname = address_.lastname;	
				
				/*if(paylikeConfig.fields!==undefined){					
					paylikeConfig.fields.name = firstname + ' ' + lastname;															
				}*/
				if(paylikeConfig.custom !== undefined){
					if( paylikeConfig.custom.address !== undefined){
						var address = '';
						address += address_.street[0] +', '+ address_.city + ', ';
						if(address_.region != ''){
							address += address_.region+ ', ';
						}
						address += address_.countryId +', '+ address_.postcode;
						paylikeConfig.custom.address = address;
					}
					paylikeConfig.custom.email = email;
					paylikeConfig.custom.name = firstname + ' ' + lastname;		
				}
				var self = this;
				paylike.popup(paylikeConfig, function (err, res) {
					if (err) {
						console.log(err);
						return false;
					}
					console.log(res);
					if (res.transaction.id !== undefined && res.transaction.id !== "") {
						self.paylikeTxnId = res.transaction.id;                            
						self.placeOrder();
					} else {
						return false;
					}
				});
			},	
			getData: function () {
				return {
					"method": this.item.method,
					'additional_data': {
						'paylike_txn_id': self.paylikeTxnId
					}
				};

			},	
			validate: function () {
                    return true;
            },			
		});
    }
);
