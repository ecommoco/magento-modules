define(
    [
        'jquery',
		'mage/url',
    ],
    function ($, urlBuilder) {
        'use strict';

        return function (email, firstname, lastname, amount, currency, pos, title, key, address,fee,aws_status) {
			var serviceUrl = urlBuilder.build('paylike/standard/order', {}); 
			var paylike = Paylike(key);		
            $.ajax({
				url: serviceUrl,
				type: 'post',
				context: this,
				data: {isAjax: 1},
				dataType: 'json',
				success: function (response) {					
					$('#paylike_payent').show();
					$('#order_place').hide();	
					if ($.type(response) === 'object' && !$.isEmptyObject(response)) {
						var orderid = response.orderid;
						
						
						if(response.amount != false && response.currency != null) {
							amount = response.amount;
							currency = response.currency;							
						}
						amount = Number.parseFloat(amount) + Number.parseFloat(fee);
						amount *= 100;						
												
						if(response.tid != undefined && response.tid != null){
							alert({
								content: $.mage.__('Your order is placed. Please check your mail')
							});
							window.location = urlBuilder.build('checkout/onepage/success',{});
						}
						if(pos === '1' || pos == '1' || pos == 1 || pos === 1) {
							var redirect = urlBuilder.build('paylike/standard/redirect');									
							//Redirect 							
							window.location='https://pos.paylike.io/?key='+key+'&currency='+currency+'&amount='+amount+'&reference='+orderid+'&redirect='+redirect;
							
						} else {
							//On checkout step
							if(aws_status === '1' || aws_status == '1' || aws_status == 1 || aws_status === 1) {
								paylike.popup({
								title: title,
								currency: currency,
								amount: amount,
								custom: {
									orderNo: orderid,
									email: email,
									address:address,
									firstname:firstname,
									lastname:lastname											
								}
							}, function (err, res) {								
								if (err){
									return console.warn(err);
								} else {
									$('#paylike_payent').hide();
									var tid = '';
										if(res.transaction !== undefined){
											tid = res.transaction.id;
										}
										console.log(res);
										
										serviceUrl = urlBuilder.build('paylike/standard/response', {});
										$.ajax({
											url: serviceUrl,
											type: 'post',
											context: this,
											data: {isAjax: 1,tid: tid, amount, currency, orderid},
											dataType: 'json',
											success: function (response) {													
												if ($.type(response) === 'object' && !$.isEmptyObject(response)) {														
														window.location = urlBuilder.build('checkout/onepage/success',{});													
												}
											}
										});
								}
							});
							}else{
								paylike.popup({
									title: title,
									currency: currency,
									amount: amount,
									custom: {
										orderNo: orderid									
									}
								}, function (err, res) {
									if (err){
										return console.warn(err);
									} else {
										var tid = '';
											if(res.transaction !== undefined){
												tid = res.transaction.id;
											}
											console.log(res);
											
											serviceUrl = urlBuilder.build('paylike/standard/response', {});
											$.ajax({
												url: serviceUrl,
												type: 'post',
												context: this,
												data: {isAjax: 1,tid: tid, amount, currency, orderid},
												dataType: 'json',
												success: function (response) {													
													if ($.type(response) === 'object' && !$.isEmptyObject(response)) {														
															window.location = urlBuilder.build('checkout/onepage/success',{});													
													}
												}
											});
									}
								});
							}
							
						}
						
					} else {								
						alert({
							content: $.mage.__('Sorry, something went wrong. Please try again.')
						});
						
					}
				},
				error: function (response) {							
					alert({
						content: $.mage.__('Sorry, something went wrong. Please try again later.')
					});
				}
			});
        };
    }
);		
				