


$(function() {
	if(!$('#chatbot_container').html()) {
		var buttons = '<ul style="display:flex;margin:40px auto;width:800px;"><li style="width:200px;"><button style="width:200px;border:solid 1px #000;height:40px;" onclick="getTestOrderList()">주문/배송 조회</button></li>';
		buttons+= '<li><button  style="width:200px;border:solid 1px #000;height:40px;" onclick="getTestBasketList()">주문하기</button></li>';
		buttons+= '<li><button  style="width:200px;border:solid 1px #000;height:40px;" onclick="getTestGoodsList()">상품조회</button></li></ul>';
		buttons+= '<div id="chatbot-virtual-container" style="display:none;"></div><div style="clear:both;"></div><ul id="chatbot-list-container" style="display:flex;width:100%;padding:40px 30px;position:relative;flex-wrap:wrap;"></ul>';

		$('body').append('<div id="chatbot_container" style="position:fixed;bottom:100px;left:100px;z-index:10000000;background:#ffff;width:600px;border:solid 3px #000;padding:40px;">'+buttons+'</div><div id="chatbot_screen_container"></div><style>#chatbot_container button { width:40%;background:#fff;border:solid 1px #000;margin:10px auto;}</style>');
		chatBot.initalize();	
	}
});
var getTestGoodsList = function() {
	
	switch(chatBot.SHOPTYPE) {
		case 'cafe24':
			var params = {keyword:'셔츠',name:''};
		break;
		case 'godo':
			var params = {keyword:'',name:''};
		break;
	}

	chatBot.getGoodsList(params,function(goodsList) {
		console.log(goodsList);
		$('#chatbot-list-container').html('');
		for(var k in goodsList) {
			var item = goodsList[k];
			var li = '<li><img src="'+item.imgsrc+'" style="width:130px;">';
			li+='<div>'+item.goodsName+'</div><div>'+item.price+'</div>';
			li+='<div style="text-align:center;"><button onclick="chatBot.goodsView(\''+item.goodsHref+'\')">자세히</button></li>';
			$('#chatbot-list-container').append(li);
		}
	});
}
var getTestOrderList = function() {
	var params = {pcs:'010-111-1111',name:'테스트'};
	chatBot.getOrderList(params,function(orderResult) {
		$('#chatbot-list-container').html('');
		if(orderResult.login) {
			console.log(orderResult.orderList);
			for(var k in orderResult.orderList) {
				var item = orderResult.orderList[k];
				var li = '<li style="padding:10px;"><img src="'+item.imgsrc+'" style="width:130px;">';
				li+='<div>'+item.goodsName+'</div><div>'+item.price+'</div>';
				li+='<div style="text-align:center;"><button onclick="chatBot.orderView(\''+item.orderHref+'\')">자세히</button>';
				if(item.statusCarieerHref) {
					li+=' | <button onclick="chatBot.deliveryTracking(\''+item.statusCarieerHref+'\')">배송조회</button>';
				}
				li+='</div>';
				//if(chatBot.SHOPTYPE == 'godo') {
					if(item.orderUpdateItems.length > 0) {
						li+=' <select onchange="chatBot.updateOrderStatus('+k+',this)">';
						li+='<option value="">주문상태변경</option>';
						for(var j in item.orderUpdateItems) {
							var updateItem = item.orderUpdateItems[j];
							li+='<option value="'+updateItem.code+'">'+updateItem.name+'</option>';
						}
						li+='</select>';
								
					} else li+='<div>취소된상품</div>';
				//}
				li+='</li>';
				$('#chatbot-list-container').append(li);
			}
			var html='<div id="chatbot-screen-layour" style="display:none;position:fixed;z-index:1000000000000;padding:40px 0 0 50px;top:100px;left:200px;width:200px;border:solid 2px #000;background:#fff;height:200px;"><div><select id="chatbot-clameCode">';
			html+='<option value="">반품/환불/교환사유</option>';
			for(var j in chatBot.clameList) {
				var item = chatBot.clameList[j];
				html+='<option value="'+item.code+'">'+item.name+'</option>';
			}
			html+='</select></div>';
			html+='<div><select id="chatbot-bank">';
			html+='<option value="">환불은행</option>';
			for(var j in chatBot.bankList) {
				var item = chatBot.bankList[j];
				html+='<option value="'+item.code+'">'+item.name+'</option>';
			}
			html+='</select></div>';
			html+='<div><input type="text" id="chatbot-accountNo" placeholder="환불계좌번호"><br><input type="text" id="chatbot-accountOwner" placeholder="예금주"></div><div><button onclick="chatBot.submitRefund()">확인</button></div></div>';
			$('#chatbot_screen_container').html(html);
		} else {
			$('#chatbot-list-container').html('<li><h3>먼저 로그인하세요</h3></li>');
		}
	});
}
var getTestBasketList = function() {

	var params = {keyword:'',name:''};
	chatBot.getBasketList(params,function(basketList) {
		$('#chatbot-list-container').html('');
		for(var k in basketList) {
			var item = basketList[k];
			var li = '<li><img src="'+item.imgsrc+'" style="width:130px;">';
			li+='<div>'+item.goodsName+'</div><div>'+item.price+'</div>';
			li+='<div style="text-align:center;"><button onclick="chatBot.goodsView(\''+item.goodsHref+'\')">자세히</button> | <button onclick="chatBot.goodsOrder('+k+')">구매하기</button></li>';
			$('#chatbot-list-container').append(li);
		}
	});
}
var chatBot = {
	SERVER_URI:'', //'https://shopapi.bottalks.co.kr',
	SHOPTYPE:'cafe24',
	orderList:[],
	basketList:[],
    goodsList:[],
	bankList:[],
	clameList:[],
	refundValue:'',
	refundItem:{},		
	initalize: function() {
		
	},
	getOrderList: function(params,callback) {
		switch(this.SHOPTYPE) {
			case 'cafe24':
				this.getCafe24OrderList(params,callback);
			break;
			case 'godo':
				this.getGodoOrderList(params,callback)
			break;
		}
	},
	getBasketList: function(params,callback) {
		switch(this.SHOPTYPE) {
			case 'cafe24':
				this.getCafe24BasketList(params,callback);
			break;
			case 'godo':
				this.getGodoBasketList(params,callback)
			break;
		}
	},
	getGoodsList: function(params,callback) {
		switch(this.SHOPTYPE) {
			case 'cafe24':
				this.getCafe24GoodsList(params,callback);
			break;
			case 'godo':
				this.getGodoGoodsList(params,callback);
			break;
		}
	},
	goodsView: function(href) {
		switch(this.SHOPTYPE) {
			case 'cafe24':
			case 'godo':
				location.href=href;
			break;
		
		}
	},
	orderView: function(href) {
		switch(this.SHOPTYPE) {
			case 'cafe24':
				location.href='/myshop/order/'+href;
			break;
			case 'godo':
				location.href=href;
			break;
		}
	},
	goodsOrder: function(idx) {
		switch(this.SHOPTYPE) {
			case 'cafe24':
				Basket.orderBasketItem(idx);
			break;
			case 'godo':
				$('#frmCart  input:checkbox[name="cartSno[]"]').prop('checked',false);
				$('#frmCart  input:checkbox[name="cartSno[]"]').eq(idx).prop('checked',true);
				gd_cart_process('orderSelect');	
			break;
		}
	},
	deliveryTracking: function(href) {
		switch(this.SHOPTYPE) {
			case 'cafe24':
			case 'godo':
				window.open(href);
			break;
		}
	},
	updateOrderStatus: function(idx,el) {
		var value = $(el).val();
		if(value) {
			var item = this.orderList[idx];
			switch(this.SHOPTYPE) {
				case 'cafe24':
					if(value == 'orderCancel') {
					} else {
						location.href='myshop/order/'+value;	
					}

				break;
				case 'godo':
					if(value=='cancle') {
						this.cancleGodoOrderStatus(item,function(result) {
							alert(result.message);
						});
					} else {
						this.refundValue = value;
						this.refundItem = item;
						$('#chatbot-screen-layour').show();
						
					}
				break;
			}
		}		
    },
	submitRefund: function() {
		if(!$("#chatbot-clameCode").val()) {
			alert('반품/환불/교환사유를 입력하세요');
			return;
		}
		if(this.refundValue!='exchange') {
			if(!$("#chatbot-bank").val()) {
				alert('환불은행을 선택하세요');
				return;
			}
			if(!$("#chatbot-accountOwner").val() || !$("#chatbot-accountNo").val()) {
				alert('계좌번호 예금주를 입력하세요');
				return;
			}
		}

		this.updateGodoOrderStatus(this.refundItem,this.refundValue);
	},
	getCafe24OrderList: function(params,callback) {
		this.orderList = [];
		
		var obj = this;
		$('#chatbot-virtual-container').load('/myshop/order/list.html',function() {
			var inputId = $('#chatbot-virtual-container #member_id').attr('name');
			var inputPw = $('#chatbot-virtual-container #member_passwd').attr('name');
			if(inputId && inputPw) {
				var result = {login:false,orderList:obj.orderList};
			} else {
				var $container = $('#chatbot-virtual-container .xans-myshop-orderhistorylistitem table tbody tr');
				$container.each(function() {
				
					var $orderEl = $(this).find('td').eq(0); // 주문일자/ 주문번호
					var orderNo = $orderEl.find('a').eq(0).text();
					var $orderStatusList = $orderEl.find('a').not('.displaynone'); // 상품구매금액
					//var orderStatusList = {};
					var orderUpdateItems = [];
					
					$orderStatusList.each(function(idxk) {
					  if($(this).attr('href').search("detail.html")) {
						  if($(this).attr('href')!='#none') {
						 //orderStatusList[$(this).attr('href')] = $(this).find('img').attr('alt');
							 var orderItem = {code:$(this).attr('href'),name:$(this).find('img').attr('alt')};
						  } else {
							 //orderStatusList['orderCancel'] = '주문취소';
							 var orderItem = {code:'orderCancel',name:'주문취소'};
						  }
						  orderUpdateItems.push(orderItem);
					  }
					});
				
					var orderHref = $orderEl.find('a').eq(0).attr('href');
					var orderDate = $orderEl.text().trim().replace(orderNo,'');
					orderNo = orderNo.replace('[','').replace(']','');
				
					var goodsPic = $(this).find('td').eq(1).find('img').attr('src'); // 상품사진
					var goodsName = $(this).find('td').eq(2).find('a').text(); // 상품이름
					var orderAmount = $(this).find('td').eq(3).text(); // 상품이름
					var orderPrice = $(this).find('td').eq(4).text(); // 상품구매금액
			
					var $orderStatus = $(this).find('td').eq(5); // 주문처리상태
					var orderStatusName = $orderStatus.find('.txtEm').text(); // 현재상태;
					var $orderStatusCarieerName = $orderStatus.find('a').eq(0); // 배송인경우 배송사
					var orderStatusCarieerNo = $orderStatus.find('a').eq(1).text().replace('[','').replace(']','');; // 배송인경우 배송번호
				
					var item = {orderNo:orderNo,orderDate:orderDate,imgsrc:goodsPic,goodsName:goodsName,amount:orderAmount,price:orderPrice,statusName:orderStatusName,statusCarieerName:$orderStatusCarieerName.text(),statusCarieerHref:$orderStatusCarieerName.attr('href'),statusCarieerNo:orderStatusCarieerNo,orderHref:orderHref,orderUpdateItems:orderUpdateItems};
					obj.orderList.push(item);
				});
				var result = {login:true,orderList:obj.orderList};
			}
			callback(result);
			

		});
	},
	getGodoOrderList: function(params,callback) {
		this.orderList = [];
		this.clameList = [];
		this.bankList = [];


		var name = params.name;
		var pcs = params.pcs;
		var obj = this;
		$.ajax({url:this.SERVER_URI+'/shopAPI/godo.php',
				data:{name:name,pcs:pcs,mode:'orderList'},
				dataType:'json',
				type:'post',
				success:function(resp) {
					console.log(resp);
					if(resp.result=='succ') {
						for(var key in resp.clameList) {
							var item = {code:key,name:resp.clameList[key]};
							obj.clameList.push(item);
						}
						for(var key in resp.bankList) {
							var item = {code:key,name:resp.bankList[key]};
							obj.bankList.push(item);
						}

						for(var key in resp.orderList) {
							
							var item = resp.orderList[key];
							//console.log(item.ordstatus);
							switch(item.ordstatus) {
								case 'e1':
								case 'b1':
								case 'r1':
								case 's1':
									continue;
								break;
							}
							if(item.goodsData[0].ordstatus=='c4' && item.ordstatus=='o1') { // 구매취소 한경우 
								continue;
							}
							var orderNo = item.ordno;
							var goodsPic =item.goodsData[0].img; // 상품사진
							var goodsName = item.gname; // 상품이름
							var orderHref = '/mypage/order_view.php?orderNo='+item.ordno;
							var statusCarieerHref = '';	
							if(item.ordstatusView=='D') {
								statusCarieerHref = '/share/delivery_trace.php?invoiceCompanySno='+item.transcp+'&invoiceNo='+item.transno;
							} 
							
							//console.log(goodsName,item.ordstatus);
							
							var orderUpdateItems = [];
							switch(item.ordstatus) {
								case 'o1': // 주문대기
									var orderItem = {code:'cancle',name:'주문취소'};
									orderUpdateItems.push(orderItem);
								break;
								default:
									var orderItem = {code:'exchange',name:'교환'};
									orderUpdateItems.push(orderItem);
									var orderItem = {code:'return',name:'반품'};
									orderUpdateItems.push(orderItem);
									var orderItem = {code:'refund',name:'환불'};
									orderUpdateItems.push(orderItem);

								break;
							}

							
							
							var snos = [];
							for(var gidx in item.goodsData) {
								snos.push(item.goodsData[gidx].sno);
							}

							var price = item.price; // 상품구매금액
							var dataItem = {orderNo:orderNo,imgsrc:goodsPic,goodsName:goodsName,price:price,orderHref:orderHref,statusCarieerHref:statusCarieerHref,orderStatus:item.ordstatus,snos:snos,orderUpdateItems:orderUpdateItems};
							obj.orderList.push(dataItem);
						}
						var orderResult = {login:true,orderList:obj.orderList};
						callback(orderResult);
					}
				},
				error:function(request,status,error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			   }
	   });
	},
	getCafe24BasketList: function(params,callback) {
		this.basketList = [];
		var obj = this;
		$('#chatbot-virtual-container').load('/order/basket.html',function() {
			var $container = $('#chatbot-virtual-container table.xans-order tbody tr');
			$container.each(function(idx) {
				
				var goodsPic = $(this).find('td').eq(1).find('img').attr('src'); // 상품사진
				var goodsName = $(this).find('td').eq(2).find('a').text(); // 상품이름
				var goodsHref = $(this).find('td').eq(2).find('a').attr('href'); // 상품url
				var orderPrice = $(this).find('td').eq(3).children().not('.displaynone').text(); // 상품구매금액
				var orderAmount = $(this).find('td').eq(4).find('input').val(); // 구매수량
				
				var item = {imgsrc:goodsPic,goodsName:goodsName,amount:orderAmount,price:orderPrice,basketIndex:idx,goodsHref:goodsHref};
				obj.basketList.push(item);
			});
			callback(obj.basketList);
		});

	},
	getGodoBasketList: function(params,callback) {
		this.basketList = [];
		var obj = this;
		$('#chatbot-virtual-container').load('/order/cart.php',function() {

			var $container = $('#chatbot-virtual-container .cart_cont_list table tbody tr');
			$container.each(function(idx) {
				var goodsPic = $(this).find('td').eq(1).find('.pick_add_img img').attr('src'); // 상품사진
				var goodsName = $(this).find('td').eq(1).find('.pick_add_img img').attr('alt'); // 상품이름
				var goodsHref = $(this).find('td').eq(1).find('.pick_add_img a').attr('href'); // 상품url
				var orderPrice = $(this).find('td').eq(4).find('.order_sum_txt').text(); // 상품구매금액
				var orderAmount = $(this).find('td').eq(2).find('.order_goods_num strong').text(); // 구매수량
				
				var item = {imgsrc:goodsPic,goodsName:goodsName,amount:orderAmount,price:orderPrice,basketIndex:idx,goodsHref:goodsHref};
				obj.basketList.push(item);
			});
			callback(obj.basketList);
			
		});

	},
	getCafe24GoodsList: function(params,callback) {
		this.goodsList = [];
		var keyword = (params.keyword)?params.keyword:'';
		var brand = (params.brand)?params.brand:'';
		var obj = this;
		$('#chatbot-virtual-container').load('/product/search.html?keyword='+keyword+'&prd_brand='+brand,function() {
			
			var $container = $('#chatbot-virtual-container ul.prdList > li');
			$container.each(function(idx) {
				var goodsPic = $(this).find('.prdImg a img').attr('src'); // 상품사진
				var goodsHref = $(this).find('.prdImg a').attr('href');
				var goodsName = $(this).find('.description .name').text(); // 상품이름
				var $priceEl =  $(this).find('.description > ul li').eq(0);	
				var price = $priceEl.find('span').text().replace('판매가',''); // 상품구매금액
				var item = {imgsrc:goodsPic,goodsName:goodsName,price:price,goodsHref:goodsHref};
				obj.goodsList.push(item);
			});
			callback(obj.goodsList);
		});
	},
	getGodoGoodsList: function(params,callback) {
		this.goodsList = [];
		var gname = params.name;
		var keyword = params.keyword;
		var obj = this;
		$.ajax({url:this.SERVER_URI+'/shopAPI/godo.php',
				data:{gname:gname,keyword:keyword,mode:'goodsList'},
				dataType:'json',
				type:'post',
				success:function(resp) {
					console.log(resp);
					if(resp.result=='succ') {
						for(var key in resp.data) {
							var item = resp.data[key];
							var goodsHref = '/goods/goods_view.php?goodsNo='+item.no;
							var goodsPic =item.img; // 상품사진
							var goodsName = item.gname; // 상품이름
							var price = item.consumer_price; // 상품구매금액
							var dataItem = {imgsrc:goodsPic,goodsName:goodsName,price:price,goodsHref:goodsHref,goodsNo:item.no};
							obj.goodsList.push(dataItem);
						   //[no] => 1000000003
						}
						callback(obj.goodsList);
					}
				},
				error:function(request,status,error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			   }
	   });
	},
	updateGodoOrderStatus: function(item,value) {
		var params = {};
		params.mode = 'updateOrder';
		params.orderNo = item.orderNo;
		params.orderStatus = item.orderStatus;
		params.snos = item.snos;
		params.action = value;

		params.handleReason	= $("#chatbot-clameCode").val();
	    params.refundBankName = $("#chatbot-bank").val();
		params.refundAccountNumber = $("#chatbot-accountNo").val();
		params.refundDepositor =$("#chatbot-accountOwner").val();
	

		//console.log(params);
		$.ajax({url:this.SERVER_URI+'/shopAPI/godo.php',
				data:params,
				dataType:'json',
				type:'post',
				success:function(resp) {
					alert(resp.message);
					
				},
				error:function(request,status,error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			   }
	   });
	},
	cancleGodoOrderStatus: function(item,callback) {
		var obj = this;
	    var params = {
             mode: 'cancelRegist',
             orderNo: item.orderNo,
             orderGoodsNo: item.snos.join('|'),
             orderStatus: item.orderStatus,
        };
        $.post('/mypage/order_ps.php', params, function (data) {
			callback(data);
        });
    }
}
//console.log(
// /myshop/order/list.html