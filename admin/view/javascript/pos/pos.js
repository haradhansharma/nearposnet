$(document).on('click', '.readqr',function(){
	$('.readqr-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
	$('.readqr-body').load($(this).attr('rel'));
	$('#qrmodal').modal('show');
});
$(document).on('click', '.listproduct',function(){
	$('.posproductlist-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
	$('.posproductlist-body').load($(this).attr('rel'));
	$("#productlist").modal("show");
});
$(document).on('click', '.posproduct',function(){
	$('.posproduct-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
	$('.posproduct-body').load($(this).attr('rel'));
	$("#noncatalogmodal").modal("show");
});
$(document).on('click', '.tmdload',function(){
	$('.orderlist-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
	$('.orderlist-body').load($(this).attr('href'));
	$("#sidebarmodal").modal("show");
});
$(document).on('click', '.emptyholdon',function(){
	$('.holdon-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
	$('.holdon-body').load($(this).attr('rel'));
	$("#holdmodal").modal("show");
});
$(document).on('click', '.paynow',function(){
	$('.paynow-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
	$('.paynow-body').load($(this).attr('rel'));
	$("#paymodal").modal("show");
});
//DateTime
$('.date').datetimepicker({
	pickTime: false
});

$('.time').datetimepicker({
	pickDate: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});

  /// Expend
  $('.clickexpend').click(function(){
  	rel=$(this).attr('rel');
  	if(rel==1)
  	{
  		$('#column-left').addClass('leftopen');
  		$('#header, #footer,.navbar-header,#column-left,#column-left').css({'display':'block'});
  		$(this).attr('rel','2');
  	}
  	else
  	{
  		$(this).attr('rel','1');
  		$('#column-left').removeClass('leftopen');
  		$('#header, #footer,.navbar-header,#column-left,#column-left.active').css({'display':'none'});
  	}
  });


  $('.scrollbox3').enscroll({
  	showOnHover: false,
  	verticalTrackClass: 'track3',
  	verticalHandleClass: 'handle3'
  });
  $('.scrollbox4').enscroll({
  	showOnHover: false,
  	verticalTrackClass: 'track3',
  	verticalHandleClass: 'handle3'
  });
  


  $(document).on('click', '.productinfodata',function(){
  	$('.productinfodata-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw" style="font-size:42x"></i></div>');
  	var product_id = $(this).attr("rel");
  	$('.productinfodata-body').load("index.php?route=pos/productinfo&product_id=" + product_id + "&user_token="+utoken);
		//$('.productinfodata-body').load($(this).attr('rel1'));
		$("#infomodal").modal("show");
	});

  $(document).on('click', '.partialform',function(){
  	$('.partialform-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
  	$('.partialform-body').load($(this).attr('href'));
  	$("#partialmodal").modal("show");
  	$('#sidebarmodal').modal('hide');
  });

// Customer Form
$(document).on('click', '.customerform',function(){
	$('.customerform-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
	$('.customerform-body').load($(this).attr('rel'));
	$("#customermodal").modal("show");
	$('#sidebarmodal').modal('hide');
});

/*Add Customer on Paynow*/
$(document).on('click', '.paynowcustomeradd',function(){
	$('.loadcustomerfrom').load($(this).attr('rel'));
});
/*Add Customer on Paynow*/

$('.tmdorderbtn').on('click', function(){
	$('.tmdorderbtn').show();
	$(this).hide();
});

$('.orderbox').hide();
$('.orderbtn').on('click', function(){
	$('.orderbox').show();
	$('.custbox').hide();
	$('.reportbox').hide();
	$('.printbox').hide();
});
$('.custbox').hide();
$('.custbtn').on('click', function(){
	$('.custbox').show();
	$('.printbox').hide();
	$('.reportbox').hide();
	$('.orderbox').hide();
});
$('.printbox').hide();
$('.printbtn').on('click', function(){
	$('.printbox').show();
	$('.reportbox').hide();
	$('.orderbox').hide();
	$('.custbox').hide();
});
/*05 11 2019 */
$('.reportbox').hide();
$('.reportbtn').on('click', function(){
	$('.reportbox').show();
	$('.printbox').hide();
	$('.orderbox').hide();
	$('.custbox').hide();
});
/*05 11 2019 */
$('.orderbox .fa-shopping-cart').on('click', function(){
	$('.orderbox').hide();
	$('.custbox').hide();
	$('.printbox').hide();
});
$('.custbox .fa-user').on('click', function(){
	$('.orderbox').hide();
	$('.custbox').hide();
	$('.printbox').hide();
});
$('.printbox .fa-print').on('click', function(){
	$('.orderbox').hide();
	$('.custbox').hide();
	$('.printbox').hide();
});
/*3 march 2020 updated code*/
$('.reportbox .fa-th-list').on('click', function(){
	$('.orderbox').hide();
	$('.reportbox').hide();
	$('.custbox').hide();
	$('.printbox').hide();
});


/// clear cart start
$(document).on('click', '.clearcart',function(){
	$.ajax({
		url: 'index.php?route=pos/cart/clearcart&user_token='+utoken,
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('.clearcart').button('loading');
		},
		complete: function() {
			$('.clearcart').button('reset');

		},
		success: function(json) {
			setTimeout(function () {
				$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
				loadtotal();
			}, 100);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
/// clear cart end

$(document).on('click', '.loadmoreproduct',function(){
	rel=$(this).attr('rel');
	path=$(this).attr('path');
	page=$('#page').val();

	$.ajax({
		url: 'index.php?route=pos/pos/ajaxloaddata&user_token='+utoken+'&path='+path+'&category_id='+rel+'&page='+page,
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('.loader').removeClass('hide');
		},
		complete: function() {

		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.loader').addClass('hide');
			if (json['products']) {
				$('.loader').addClass('hide');
				$('.loadmoreproduct').remove();
				html='';
				for (i in json['products']) {
					html +='<div class="col-md-4 col-sm-4 col-xs-12">';
					html +='<div class="box4">';
					stockclass="off";
					if(json['products'][i]['stock']<10)
					{
						stockclass='off1';
					}
					html +='<div class="'+stockclass+'">';
					html +='<span>'+json['products'][i]['stock']+'</span>';
					html +='</div>';
					html +='<div class="image">';
					html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
					html +='</div>';
					html +='<div class="buttons">';
					html +='<a rel1="'+json['products'][i]['href']+'" name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
					html +='<i class="fa fa-info-circle" aria-hidden="true"></i>Info</a>';
					html +='<a rel1="'+json['products'][i]['href']+'" class="addtocartquick" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i>Cart</a>';
					html +='</div>';

					html +='<div class="caption"><h1>'+json['products'][i]['name']+'</h1>';
					if(json['products'][i]['price'])
					{
						html +='<p class="price">';
						if(!json['products'][i]['special'])
						{
							html +=' <span class="price-new">'+json['products'][i]['price']+'</span>';
						} else {
							html +=' <span class="price-new">'+json['products'][i]['special']+'</span> <span class="price-old">'+json['products'][i]['price']+'</span>';
						}
						if(json['products'][i]['tax'])
						{
							html +='<span class="price-tax">'+json['products'][i]['tax']+'</span>';
						}
						html +='</p>';
					}
					html +='<b>'+json['products'][i]['options']+'</b>';
					html +='</div></div></div>';
				}

				if(json['loadmore'])
				{
					$('#page').val(parseInt(page)+1);
					html +="<div class='loadmoreproduct btn btn-primary' rel='"+rel+"' path='"+path+"' >Load more</div>";
					oldpath=path;

				}
				$('.products').append(html);

			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$(document).on('click', '.addtocartquick',function(){
	product_id=$(this).attr('rel');
	var data={}
	data['product_id']=product_id;

	$.ajax({
		url: 'index.php?route=pos/cart/ajaxloadaddtocart&user_token='+utoken,
		type: 'post',
		data:data,
		dataType: 'json',
		beforeSend: function() {

		},
		complete: function() {
			$('#infomodal').modal('hide');
			$('.loader1').removeClass('hide');
		},

		success: function(json) {
			$('.loader1').addClass('hide');
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');

			if (json['error']) {
				if (json['error']['option']) {
					$('.productinfodata-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw" style="font-size:42x"></i></div>');
					setTimeout(function(){ $("#infomodal").modal("show"); }, 3000);
					$('.productinfodata-body').load("index.php?route=pos/productinfo&product_id=" + product_id + "&user_token="+utoken);
				}
				$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
				loadtotal();

			}
			else
			{

				if (json['success']) {

					$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
					loadtotal();
				}
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

//Add To Cart
$(document).on('click', '.addtocart',function(){
	$.ajax({
		url: 'index.php?route=pos/cart/ajaxloadaddtocart&user_token='+utoken,
		type: 'post',
		data: $('#product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-cart').button('loading');
		},
		complete: function() {
			$('#button-cart').button('reset');
			$('.loader1').removeClass('hide');
		},

		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
			$('.loader1').addClass('hide');

			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						var element = $('#input-option' + i.replace('_', '-'));

						if (element.parent().hasClass('input-group')) {
							element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						} else {
							element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						}
					}
				}
				// Highlight any found errors
				$('.text-danger').parent().addClass('has-error');
			}

			if (json['success']) {
				$('#infomodal').modal('hide');
				$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
				loadtotal();
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

//Update
$(document).on('click', '.update',function() {
	key=$(this).attr('rel');
	rel1=$(this).attr('rel1');
	quantity=$('.quantity'+rel1).val();
	price=$('.price'+rel1).val();

	$.ajax({
		url: 'index.php?route=pos/cart/edit&user_token='+utoken,
		type: 'post',
		data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1)+'&price=' +(typeof(price) != 'undefined' ? price :''),
		dataType: 'json',
		beforeSend: function() {
			$('.loader1').removeClass('hide');
		},
		complete: function() {

		},
		success: function(json) {
			$('.loader1').addClass('hide');
			$('.alert, .text-danger').remove();

			$('.breadcrumb').after('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			$('html, body').animate({ scrollTop: 0 }, 'slow');


			// Need to set timeout otherwise it wont update the total
			setTimeout(function () {
				$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
				loadtotal();
			}, 100);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});

});

//Remove
$(document).on('click', '.remove',function() {
	key=$(this).attr('rel');

	$.ajax({
		url: 'index.php?route=pos/cart/remove&user_token='+utoken,
		type: 'post',
		data: 'key=' + key,
		dataType: 'json',
		beforeSend: function() {
			$('.loader1').removeClass('hide');
		},
		complete: function() {

		},

		success: function(json) {
			$('.loader1').addClass('hide');
			$('.alert, .text-danger').remove();

			$('.breadcrumb').after('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			$('html, body').animate({ scrollTop: 0 }, 'slow');


			// Need to set timeout otherwise it wont update the total
			setTimeout(function () {
				$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
				loadtotal();
			}, 100);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
//OrderNow
$(document).on('click', '.ordernow',function(){
	$.ajax({
		url: 'index.php?route=pos/paynow/addorder&user_token='+utoken,
		type: 'post',
		data: $('.order-nowdata input[type=\'text\'], .order-nowdata input[type=\'number\'], .order-nowdata input[type=\'hidden\'], .order-nowdata input[type=\'checkbox\']:checked, .order-nowdata select, .order-nowdata textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#pay-now').button('loading');
		},
		complete: function() {
			$('#pay-now').button('reset');

		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');

			if (json['error']) {
				$('.modal-header').after('<div class="alert alert-danger col-sm-12"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}

			if (json['success']) {
				$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
				loadtotal();
				link=json['link'];
				link=link.replace('amp;', '');
				link=link.replace('amp;', '');
				//$('.paynowfinal').load(link);
				$('.paynowfinal').html('<div class="success alert-success" style="margin:10px;padding:10px;font-size:25px"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + ' <a href="'+json['link']+'" target="_blank"><i class="fa fa-print"></i> </a> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
				$('.loaddashbord').load('index.php?route=pos/dashboardload&loaddirect=true&user_token='+utoken);

			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	}); 
});
// Manual Discount
$(document).on('click', '.manualdiscount',function(){
	$.ajax({
		url: 'index.php?route=pos/cart/manualdiscount&user_token='+utoken,
		type: 'post',
		data: $('#manualdiscount input[type=\'text\'],#manualdiscount select'),
		dataType: 'json',
		beforeSend: function() {
			$('.manualdiscount').button('loading');
		},
		complete: function() {
			$('.manualdiscount').button('reset');

		},
		success: function(json) {
			$('.alert,.text-danger').remove();
			if (json['error']) {

				$('.sub-totals').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			if (json['success']) {
				$('.sub-totals').before('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}

			loadtotal();
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
// Manual Discount layout3
$(document).on('click', '.manualdiscount1',function(){
	$.ajax({
		url: 'index.php?route=pos/cart/manualdiscount&user_token='+utoken,
		type: 'post',
		data: $('#manualdiscount input[type=\'text\'],#manualdiscount select'),
		dataType: 'json',
		beforeSend: function() {
			$('.manualdiscount1').button('loading');
		},
		complete: function() {
			$('.manualdiscount1').button('reset');

		},
		success: function(json) {
			$('.alert,.text-danger').remove();
			if (json['error']) {

				$('#discountModal .extraoptions').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div><br/>');
			}
			if (json['success']) {
				$('#discountModal').hide();
				$('.modal-backdrop').hide();
				$('.sub-totals').before('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}

			loadtotal();
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
// Coupon layout3
$(document).on('click', '.coupondiscount1',function(){
	$.ajax({
		url: 'index.php?route=pos/cart/applydiscount&user_token='+utoken,
		type: 'post',
		data: $('#coupondiscount input[type=\'text\']'),
		dataType: 'json',
		beforeSend: function() {
			$('.coupondiscount1').button('loading');
		},
		complete: function() {
			$('.coupondiscount1').button('reset');

		},

		success: function(json) {
			$('.alert, .text-danger').remove();
			if (json['error']) {
				$('#couponModal #coupondiscount').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div><br/>');
			}
			if (json['success']) {
				$('#couponModal').hide();
				$('.modal-backdrop').hide();
				$('.sub-totals').before('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			loadtotal();
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
// Coupon
$(document).on('click', '.coupondiscount',function(){
	$.ajax({
		url: 'index.php?route=pos/cart/applydiscount&user_token='+utoken,
		type: 'post',
		data: $('#coupondiscount input[type=\'text\']'),
		dataType: 'json',
		beforeSend: function() {
			$('.coupondiscount').button('loading');
		},
		complete: function() {
			$('.coupondiscount').button('reset');

		},

		success: function(json) {
			$('.alert, .text-danger').remove();
			if (json['error']) {
				$('.sub-totals').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			if (json['success']) {
				$('.sub-totals').before('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			loadtotal();
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

// Voucher
$(document).on('click', '.voucherdiscount',function(){
	$.ajax({
		url: 'index.php?route=pos/cart/applyvoucher&user_token='+utoken,
		type: 'post',
		data: $('#voucherdiscount input[type=\'text\']'),
		dataType: 'json',
		beforeSend: function() {
			$('.voucherdiscount').button('loading');
		},
		complete: function() {
			$('.voucherdiscount').button('reset');

		},
		success: function(json) {
			if (json['error']) {
				$('.sub-totals').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			if (json['success']) {
				$('.sub-totals').before('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			loadtotal();
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

// Order Edit
$(document).on('click', '.orderedit',function(){
	rel=$(this).attr('rel');
	$.ajax({
		url: 'index.php?route=pos/orderlist/edit&user_token='+utoken+'&order_id='+rel,
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('.loader1').removeClass('hide');

		},
		complete: function() {
			$('#ordermodal').modal('hide');
			$('#sidebarmodal').modal('hide');
			$('.loader1').addClass('hide');

		},
		success: function(json) {
			if (json['success']) {
		//	$('.paynow').hide();
		$('.editorder').show();
		$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
	}
	loadtotal();
},
error: function(xhr, ajaxOptions, thrownError) {
	alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
}
});
});

// Order edit Action

$(document).on('click', '.editorder',function(){
	rel=$(this).attr('rel');
	$.ajax({
		url: 'index.php?route=pos/orderlist/editsave&user_token='+utoken,
		type: 'post',
		data: $('.order-editdata input[type=\'text\'], .order-editdata input[type=\'hidden\'], .order-editdata input[type=\'checkbox\']:checked, .order-editdata select, .order-editdata textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('.loader1').removeClass('hide');
			$('.editorder').button('loading');

		},
		complete: function() {
			$('.loader1').addClass('hide');
			$('.editorder').button('reset');

		},
		success: function(json) {

			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');

			if (json['error']) {
				$('.modal-header').after('<div class="alert alert-danger col-sm-12"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}


			if (json['success']) {

				$('.paynow').show();
				$('.editorder').hide();
				$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);

				loadtotal();
				/* link=json['link'];
				link=link.replace('amp;', '');
				link=link.replace('amp;', '');
				$('.paynowfinal').load(link);
				$('.paynowfinal').html('<div class="success alert-success" style="margin:10px;padding:10px;font-size:25px"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + ' <a href="'+json['link']+'" target="_blank"><i class="fa fa-print"></i> </a> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'); */

				$('.paynow-body').html('<div class="success alert-success" style="margin:10px;padding:10px;font-size:25px"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + ' <a href="'+json['link']+'" target="_blank"><i class="fa fa-print"></i> </a> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
				$("#paymodal").modal("show");
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});
function loadtotal()
{
	$.ajax({
		url: 'index.php?route=pos/cart/loadtotal&user_token='+utoken,
		type: 'post',
		data: '',
		dataType: 'json',
		beforeSend: function() {

		},
		complete: function() {
		},
		success: function(json) {
			$('.totalload').html()
			html='';
			if(json['totals']) {
				for (z in json['totals']) {
					html +='<tr><td class="text-left"><strong>'+json['totals'][z]['title']+' </strong></td><td class="text-right"> '+ json['totals'][z]['text'] +'</td></tr>';
				}
			}
			$('.totalload').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}
//Search Product start
$(document).on('click', '.serchproduct',function(){

	$.ajax({
		url: 'index.php?route=pos/pos/ajaxloaddata&user_token='+utoken,
		type: 'post',
		data: $('.search input[type=\'text\']'),
		dataType: 'json',
		beforeSend: function() {
			$('.loader').removeClass('hide');
			$('.categories').addClass('hide');
			$('.products').html('');
		},
		complete: function() {
			$('input[name=\'filter_product\']').val('');
		},
		success: function(json) {

			if (json['products']) {
				$('.loader').addClass('hide');
				html='';
				for (i in json['products']) {
					if(json['themechoose']=='layout3'){
						html +='<div class="col-md-3 col-lg-2 col-sm-3 col-xs-6 padd5">';
						html +='<div class="box4">';
						html +='<div class="image">';
						stockclass="off";
						if(json['products'][i]['stock']<10)
						{
							stockclass='off1';
						}
						html +='<div class="'+stockclass+'">';
						html +='<span>'+json['products'][i]['stock']+'</span>';
						html +='</div>';
						html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
						html +='<div class="buttons">';
						html +='<a name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
						html +='<i class="fa fa-info-circle" aria-hidden="true"></i></a>';
						html +='<a class="addtocartquick addtocartquick'+json['products'][i]['product_id']+'" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
						html +='</div></div>';
					}else if(json['themechoose']=='layout2'){
						html +='<div class="col-md-20 col-lg-1 col-sm-3 col-xs-6">';
						html +='<div class="box4">';
						html +='<div class="image">';
						stockclass="off";
						if(json['products'][i]['stock']<10)
						{
							stockclass='off1';
						}
						html +='<div class="'+stockclass+'">';
						html +='<span>'+json['products'][i]['stock']+'</span>';
						html +='</div>';
						html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
						html +='<div class="buttons">';
						html +='<a name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
						html +='<i class="fa fa-info-circle" aria-hidden="true"></i></a>';
						html +='<a class="addtocartquick addtocartquick'+json['products'][i]['product_id']+'" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
						html +='</div></div>';
					}else{
						html +='<div class="col-md-4 col-sm-4 col-xs-12">';
						html +='<div class="box4">';
						stockclass="off";
						if(json['products'][i]['stock']<10)
						{
							stockclass='off1';
						}
						html +='<div class="'+stockclass+'">';
						html +='<span>'+json['products'][i]['stock']+'</span>';
						html +='</div>';
						html +='<div class="image">';
						html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
						html +='</div>';
						html +='<div class="buttons">';
						html +='<a name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
						html +='<i class="fa fa-info-circle" aria-hidden="true"></i>Info</a>';
						html +='<a class="addtocartquick addtocartquick'+json['products'][i]['product_id']+'" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i>Cart</a>';
						html +='</div>';
					}

					html +='<div class="caption"><h1>'+json['products'][i]['name']+'</h1>';
					if(json['products'][i]['price'])
					{

						html +='<p class="price">';
						if(json['products'][i]['special'])
						{
							html +=' <span class="price-new">'+json['products'][i]['special']+'</span> <span class="price-old">'+json['products'][i]['price']+'</span>';

						}else{
							html +=' <span class="price-new">'+json['products'][i]['price']+'</span>';
						}
						if(json['products'][i]['tax']!=false)
						{
							html +='<span class="price-tax">'+json['products'][i]['tax']+'</span>';
						}
						html +='</p>';
					}
					html +='</div></div></div>';
				}



				$('.products').html(html);
				if(json['addquick']) {
					$('.addtocartquick'+json['products'][i]['product_id']).trigger('click');
				}
				if (json['cartload']) {
					$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
					loadtotal();
				}
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
})
//Search Product start
$(document).on('click', '.serchproduct1',function(){

	$.ajax({
		url: 'index.php?route=pos/pos/ajaxloaddata&user_token='+utoken,
		type: 'post',
		data: $('.search input[type=\'text\']'),
		dataType: 'json',
		beforeSend: function() {
			$('.loader').removeClass('hide');
		},
		complete: function() {
			$('input[name=\'filter_product\']').val('');
		},
		success: function(json) {

			if (json['products']) {
				$('.loader').addClass('hide');
				html='';
				for (i in json['products']) {
					if(json['themechoose']=='layout3'){
						html +='<div class="col-md-3 col-lg-2 col-sm-3 col-xs-6 padd5">';
						html +='<div class="box4">';
						html +='<div class="image">';
						stockclass="off";
						if(json['products'][i]['stock']<10)
						{
							stockclass='off1';
						}
						html +='<div class="'+stockclass+'">';
						html +='<span>'+json['products'][i]['stock']+'</span>';
						html +='</div>';
						html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
						html +='<div class="buttons">';
						html +='<a name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
						html +='<i class="fa fa-info-circle" aria-hidden="true"></i></a>';
						html +='<a class="addtocartquick addtocartquick'+json['products'][i]['product_id']+'" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
						html +='</div></div>';
					}else if(json['themechoose']=='layout2'){
						html +='<div class="col-md-20 col-lg-1 col-sm-3 col-xs-6">';
						html +='<div class="box4">';
						html +='<div class="image">';
						stockclass="off";
						if(json['products'][i]['stock']<10)
						{
							stockclass='off1';
						}
						html +='<div class="'+stockclass+'">';
						html +='<span>'+json['products'][i]['stock']+'</span>';
						html +='</div>';
						html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
						html +='<div class="buttons">';
						html +='<a name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
						html +='<i class="fa fa-info-circle" aria-hidden="true"></i></a>';
						html +='<a class="addtocartquick addtocartquick'+json['products'][i]['product_id']+'" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
						html +='</div></div>';
					}else{
						html +='<div class="col-md-4 col-sm-4 col-xs-12">';
						html +='<div class="box4">';
						stockclass="off";
						if(json['products'][i]['stock']<10)
						{
							stockclass='off1';
						}
						html +='<div class="'+stockclass+'">';
						html +='<span>'+json['products'][i]['stock']+'</span>';
						html +='</div>';
						html +='<div class="image">';
						html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
						html +='</div>';
						html +='<div class="buttons">';
						html +='<a name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
						html +='<i class="fa fa-info-circle" aria-hidden="true"></i>Info</a>';
						html +='<a class="addtocartquick addtocartquick'+json['products'][i]['product_id']+'" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i>Cart</a>';
						html +='</div>';
					}

					html +='<div class="caption"><h1>'+json['products'][i]['name']+'</h1>';
					if(json['products'][i]['price'])
					{

						html +='<p class="price">';
						if(json['products'][i]['special'])
						{
							html +=' <span class="price-new">'+json['products'][i]['special']+'</span> <span class="price-old">'+json['products'][i]['price']+'</span>';

						}else{
							html +=' <span class="price-new">'+json['products'][i]['price']+'</span>';
						}
						if(json['products'][i]['tax']!=false)
						{
							html +='<span class="price-tax">'+json['products'][i]['tax']+'</span>';
						}
						html +='</p>';
					}
					html +='</div></div></div>';
				}



				$('.products').html(html);
				if(json['addquick']) {
					$('.addtocartquick'+json['products'][i]['product_id']).trigger('click');
				}
				if (json['cartload']) {
					$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
					loadtotal();
				}
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
})
$('.search input[name=\'filter_product\']').on('keydown', function(e) {
	if (e.keyCode == 13) {
		$('.leftside1 input[name=\'filter_product\']').parent().find('button').trigger('click');
	}
});

$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
loadtotal();

  // Pos Product List
  $(document).on('click', '#addcartlist',function(){
  	rel=$(this).attr('rel');
  	var data={}
  	data['cproduct_id']=rel;
  	$.ajax({
  		url: 'index.php?route=pos/posproductlist/addCart&user_token='+utoken,
  		type: 'post',
  		data:data,
  		dataType: 'json',
  		beforeSend: function() {
  		},
  		complete: function() {
  		},

  		success: function(json) {
      //$('.loader1').addClass('hide');
      $('.alert, .text-danger').remove();
      $('.form-group').removeClass('has-error');

      if (json['success']) {
      	$('#productlist').modal('hide');
      	$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
      	loadtotal();
      }

    },
    error: function(xhr, ajaxOptions, thrownError) {
    	alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
  });
  });

  // Order Print
  $(document).on('click', '.order-id',function(){
  	orderid=$('.orderid').val();
  	if(orderid=='') {
  		$('#form-print').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Order ID Missing<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
  		return false;
  	} else {
  		window.open('index.php?route=pos/printinvoice&user_token='+utoken+'&order_id='+orderid);
  		return false;
  		$("#printModal .order-id" ).click(function() {
  			$('#printModal').remove();
  			$('.modal-backdrop').remove();
  		});
  	}
  });

  //	Add Customer
  $(document).on('click', '.addcustomer',function(){
  	$.ajax({
  		url: 'index.php?route=pos/customerform/addcustomer&user_token='+utoken,
  		type: 'post',
  		data: $('.add-customer input[type=\'text\'], .add-customer input[type=\'hidden\'], .add-customer input[type=\'password\'], .add-customer textarea, .add-customer select'),
  		dataType: 'json',
  		beforeSend: function() {
  			$('.addcustomer').button('loading');
  		},
  		complete: function() {
  			$('.addcustomer').button('reset');

  		},
  		success: function(json) {
  			$('.alert, .alert-danger').remove();
  			$('.form-group').removeClass('has-error');
  			if (json['error']) {
  				for (i = 0; i < json['error'].length; i++) {
  					$('.add-customer').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'][i] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
  				}
  			}

      /*if (json['error']) {
        $('.add-customer').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
      }*/

      if (json['success']) {
      	$('.addcustomer').button('reset');
      	$('.customer-Modal').modal('hide');
      	$('#customermodal').modal('hide');
      	$('#addModal').modal('hide');
        //$('.loadcustomerfrom').hide();
        $('.customer_id').val(json['customer']['customer_id']);
        $('.customer_name').val(json['customer']['name']);
        $('input[name=firstname]').val('');
        $('input[name=lastname]').val('');
        $('input[name=telephone]').val('');
        $('input[name=address_1]').val('');


      }
    },
    error: function(xhr, ajaxOptions, thrownError) {
    	alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
  });
  })

  //	Add Paynow Customer
  $(document).on('click', '.addpaynowcustomer',function(){
  	$.ajax({
  		url: 'index.php?route=pos/paynowcustomer/addpaycustomer&user_token='+utoken,
  		type: 'post',
  		data: $('.add-nowcustomer input[type=\'text\'], .add-nowcustomer input[type=\'hidden\'], .add-nowcustomer input[type=\'password\'], .add-nowcustomer textarea, .add-nowcustomer select',),
  		dataType: 'json',
  		beforeSend: function() {
  			$('.addpaynowcustomer').button('loading');
  		},
  		complete: function() {
  			$('.addpaynowcustomer').button('reset');

  		},
  		success: function(json) {
  			$('.alert, .alert-danger').remove();
  			$('.form-group').removeClass('has-error');
  			if (json['error']) {
  				for (i = 0; i < json['error'].length; i++) {
  					$('.add-nowcustomer').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'][i] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
  				}
  			}



  			if (json['success']) {
  				$('.tmdselectcustom .close').trigger('click');
  				$('.loadcustomerfrom').hide();
  				$('.customer_id').val(json['customer']['customer_id']);
  				$('.customer_name').val(json['customer']['name']);
  			}
  		},
  		error: function(xhr, ajaxOptions, thrownError) {
  			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
  		}
  	});
  })


  //	Add Pos Product
  $(document).on('click', '.addposproduct',function(){
  	$.ajax({
  		url: 'index.php?route=pos/posproduct/addposproduct&user_token='+utoken,
  		type: 'post',
  		data: $('.add-posproduct input[type=\'text\'], .add-posproduct input[type=\'hidden\'], .add-posproduct textarea, .add-posproduct select'),
  		dataType: 'json',
  		beforeSend: function() {
  			$('.addposproduct').button('loading');
  		},
  		complete: function() {
  			$('.addposproduct').button('reset');

  		},
  		success: function(json) {
  			$('.alert, .alert-danger').remove();
  			$('.form-group').removeClass('has-error');

  			if (json['error']) {
  				$('.modal-header').after('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
  			}

  			if (json['success']) {
  				$('#noncatalogmodal').modal('hide');
  				cproduct_id=json['cproduct_id'];
  				var data={}
  				data['cproduct_id']=cproduct_id;
  				data['quantity']=json['quantity'];

  				$.ajax({
  					url: 'index.php?route=pos/cart/ajaxloadaddtocart&user_token='+utoken,
  					type: 'post',
  					data:data,
  					dataType: 'json',
  					beforeSend: function() {
  						$('.loader1').removeClass('hide');
  					},
  					complete: function() {
  						$('.loader1').removeClass('hide');
  					},

  					success: function(json) {
  						$('.loader1').addClass('hide');
  						$('.alert, .text-danger').remove();
  						$('.form-group').removeClass('has-error');

  						if (json['success']) {

  							$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
  							loadtotal();
  						}

  					},
  					error: function(xhr, ajaxOptions, thrownError) {
  						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
  					}
  				});


  			}
  		},
  		error: function(xhr, ajaxOptions, thrownError) {
  			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
  		}
  	});
  });


  $(document).on('click', '.holdoncart',function(){
  	rel=$(this).attr('rel');
  	$.ajax({
  		url: 'index.php?route=pos/holdonreport/holdaddCart&user_token='  +utoken+'&holdon_id='+rel,
  		type: 'post',
  		data :$('.posholdon').serialize(),
  		dataType: 'json',
  		beforeSend: function() {
  		},
  		complete: function() {
  			$('.close').trigger('click');
  		},

  		success: function(json) {
  			$('.alert, .text-danger').remove();
  			$('.form-group').removeClass('has-error');

  			if (json['success']) {

  				setTimeout(function () {
  					$('.loadcartclass').load('index.php?route=pos/cart/loadcart&user_token='+utoken);
  				}, 300);
  				loadtotal();
  			}

  		},
  		error: function(xhr, ajaxOptions, thrownError) {
  			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
  		}
  	});
  });
  var oldpath='';
  $(document).on('click', '.showsubcate',function(){
  	rel=$(this).attr('rel');
  	path=$(this).attr('path');
  	$.ajax({
  		url: 'index.php?route=pos/pos/ajaxloaddata&user_token='+utoken+'&path='+path+'&category_id='+rel,
  		type: 'post',
  		dataType: 'json',
  		beforeSend: function() {
  			$('.loader').removeClass('hide');
  			$('.categories').addClass('hide');
  			$('.products').html('');
  		},
  		complete: function() {

  		},
  		success: function(json) {
  			$('.alert, .text-danger').remove();
  			$('.loader').addClass('hide');
  			$('.categories').removeClass('hide');
  			$('.categories').html();
  			$('.products').html('');
  			if (json['categories']) {
  				html='';
  				for (i in json['categories']) {
  					html +='<li class="col-md-4 col-sm-4 col-xs-12 showsubcate" rel="'+json['categories'][i]['category_id']+'" path="'+json['categories'][i]['path']+'">';
  					html +='<i class="fa fa-folder-open" aria-hidden="true"></i>';
  					html +='<br>'+json['categories'][i]['name']+'</li>';
  				}
  				$('.categories').html(html);
  			}
  			if (json['products']) {
  				$('.loader').addClass('hide');
  				html='';
  				for (i in json['products']) {
  					html +='<div class="col-md-4 col-sm-4 col-xs-12">';
  					html +='<div class="box4">';
  					stockclass="off";
  					if(json['products'][i]['stock']<10)
  					{
  						stockclass='off1';
  					}
  					html +='<div class="'+stockclass+'">';
  					html +='<span>'+json['products'][i]['stock']+'</span>';
  					html +='</div>';
  					html +='<div class="image">';
  					html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
  					html +='</div>';
  					html +='<div class="buttons">';
  					html +='<a rel1="'+json['products'][i]['href']+'" name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
  					html +='<i class="fa fa-info-circle" aria-hidden="true"></i>Info</a>';
  					html +='<a rel1="'+json['products'][i]['href']+'" class="addtocartquick" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i>Cart</a>';
  					html +='</div>';

  					html +='<div class="caption"><h1>'+json['products'][i]['name']+'</h1>';
  					if(json['products'][i]['price'])
  					{
  						html +='<p class="price">';
  						if(!json['products'][i]['special'])
  						{
  							html +=' <span class="price-new">'+json['products'][i]['price']+'</span>';
  						} else {
  							html +=' <span class="price-new">'+json['products'][i]['special']+'</span> <span class="price-old">'+json['products'][i]['price']+'</span>';
  						}
  						if(json['products'][i]['tax'])
  						{
  						/*
  						html +='<span class="price-tax">'+json['products'][i]['tax']+'</span>';
  						*/
  					}
  					html +='</p>';
  				}
  				/*
  				html +='<b>'+json['products'][i]['options']+'</b>';
  				*/
  				html +='</div></div></div>';
  			}

  			if(json['loadmore'])
  			{
  				if(rel==0 || path!=oldpath)
  				{
  					$('#page').val('2');
  				}
  				html +="<div class='loadmoreproduct btn btn-primary' rel='"+rel+"' path='"+path+"' >Load more</div>";
  				oldpath=path;

  			}
  			$('.products').html(html);

  		}
  		$('.breadcrumbs').html();
  		breadcrumbs='';
  		if(json['breadcrumbs'])
  		{
  			for (z in json['breadcrumbs']) {
  				breadcrumbs +='<span class="showsubcate" rel="'+json['breadcrumbs'][z]['category_id']+'" path="'+json['breadcrumbs'][z]['path']+'">'+json['breadcrumbs'][z]['path']+'</span>';
  			}
  		}

  		$('.breadcrumbsload').html(breadcrumbs);

  	},
  	error: function(xhr, ajaxOptions, thrownError) {
  		alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
  	}
  });
  })

	//design2
	$( document ).ready(function() {
		$('.showsubcate1:first-child').addClass('active');
		$('.showsubcate1:first-child').trigger('click');
	});
	var oldpath='';
	$(document).on('click', '.showsubcate1',function(){
		rel=$(this).attr('rel');
		path=$(this).attr('path');
		$('.showsubcate1').removeClass('active');
		$(this).addClass('active');
		$.ajax({
			url: 'index.php?route=pos/pos/ajaxloaddata&user_token='+utoken+'&path='+path+'&category_id='+rel,
			type: 'post',
			dataType: 'json',
			beforeSend: function() {
				$('.loader').removeClass('hide');
				$('.products').html('');
			},
			complete: function() {

			},
			success: function(json) {
				$('.alert, .text-danger').remove();
				$('.loader').addClass('hide');
				$('.products').html('');
				if (json['categories']) {
					html='';
					for (i in json['categories']) {
						if(json['themechoose']=='layout3'){
							html +='<div class="col-md-3 col-lg-2 col-sm-3 col-xs-6 padd5 showsubcate1" rel="'+json['categories'][i]['category_id']+'" path="'+json['categories'][i]['path']+'"><div class="catediv">';
							html +='<i class="fa fa-folder-open" aria-hidden="true"></i>';
							html +=''+json['categories'][i]['name']+'</div></div>';
						}else{
							html +='<div class="col-md-20 col-lg-1 col-sm-3 col-xs-6 showsubcate1" rel="'+json['categories'][i]['category_id']+'" path="'+json['categories'][i]['path']+'"><div class="catediv">';
							html +='<i class="fa fa-folder-open" aria-hidden="true"></i>';
							html +=''+json['categories'][i]['name']+'</div></div>';
						}
					}
					$('.subcategories').html(html);
				}
				if (json['products']) {
					$('.loader').addClass('hide');
					html='';
					for (i in json['products']) {
						if(json['themechoose']=='layout3'){
							html +='<div class="col-md-3 col-lg-2 col-sm-3 col-xs-6 padd5">';
							html +='<div class="box4">';
							html +='<div class="image">';
							stockclass="off";
							if(json['products'][i]['stock']<10)
							{
								stockclass='off1';
							}
							html +='<div class="'+stockclass+'">';
							html +='<span>'+json['products'][i]['stock']+'</span>';
							html +='</div>';
							html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
							html +='<div class="buttons">';
							html +='<a rel1="'+json['products'][i]['href']+'" name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
							html +='<i class="fa fa-info-circle" aria-hidden="true"></i></a>';
							html +='<a rel1="'+json['products'][i]['href']+'" class="addtocartquick" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
							html +='</div></div>';

							html +='<div class="caption">';
							html +='<h1>'+json['products'][i]['name']+'</h1>';
							if(json['products'][i]['price'])
							{
								html +='<p class="price">';
								if(!json['products'][i]['special'])
								{
									html +=json['products'][i]['price']
								} else {
									html +=' <span class="price-new">'+json['products'][i]['special']+'</span> <span class="price-old">'+json['products'][i]['price']+'</span>';
								}
								if(json['products'][i]['tax'])
								{
  						/*
  						html +='<span class="price-tax">'+json['products'][i]['tax']+'</span>';
  						*/
  					}
  					html +='</p>';
  				}
  				/*
  				html +='<b>'+json['products'][i]['options']+'</b>';
  				*/
  				html +='</div></div></div>';
  			}else{
  				html +='<div class="col-md-20 col-lg-1 col-sm-3 col-xs-6">';
  				html +='<div class="box4">';
  				html +='<div class="image">';
  				stockclass="off";
  				if(json['products'][i]['stock']<10)
  				{
  					stockclass='off1';
  				}
  				html +='<div class="'+stockclass+'">';
  				html +='<span>'+json['products'][i]['stock']+'</span>';
  				html +='</div>';
  				html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
  				html +='<div class="buttons">';
  				html +='<a rel1="'+json['products'][i]['href']+'" name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
  				html +='<i class="fa fa-info-circle" aria-hidden="true"></i></a>';
  				html +='<a rel1="'+json['products'][i]['href']+'" class="addtocartquick" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
  				html +='</div></div>';

  				html +='<div class="caption">';
  				html +='<h1>'+json['products'][i]['name']+'</h1>';
  				if(json['products'][i]['price'])
  				{
  					html +='<p class="price">';
  					if(!json['products'][i]['special'])
  					{
  						html +=json['products'][i]['price']
  					} else {
  						html +=' <span class="price-new">'+json['products'][i]['special']+'</span> <span class="price-old">'+json['products'][i]['price']+'</span>';
  					}
  					if(json['products'][i]['tax'])
  					{
							/*
							html +='<span class="price-tax">'+json['products'][i]['tax']+'</span>';
							*/
						}
						html +='</p>';
					}
					/*
					html +='<b>'+json['products'][i]['options']+'</b>';
					*/
					html +='</div></div></div>';
				}
			}
			if(json['loadmore'])
			{
				if(rel==0 || path!=oldpath)
				{
					$('#page').val('2');
				}
				html +="<div class='loadmoreproduct1 btn btn-primary' rel='"+rel+"' path='"+path+"' >Load more</div>";
				oldpath=path;

			}
			$('.products').html(html);

		}
	},
	error: function(xhr, ajaxOptions, thrownError) {
		alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
	}
});
})
$(document).on('click', '.loadmoreproduct1',function(){
	rel=$(this).attr('rel');
	path=$(this).attr('path');
	page=$('#page').val();

	$.ajax({
		url: 'index.php?route=pos/pos/ajaxloaddata&user_token='+utoken+'&path='+path+'&category_id='+rel+'&page='+page,
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('.loader').removeClass('hide');
		},
		complete: function() {

		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.loader').addClass('hide');
			if (json['products']) {
				$('.loader').addClass('hide');
				$('.loadmoreproduct1').remove();
				html='';
				for (i in json['products']) {
					if(json['themechoose']=='layout3'){
						html +='<div class="col-md-3 col-lg-2 col-sm-3 col-xs-6 padd5">';
						html +='<div class="box4">';
						html +='<div class="image">';
						stockclass="off";
						if(json['products'][i]['stock']<10)
						{
							stockclass='off1';
						}
						html +='<div class="'+stockclass+'">';
						html +='<span>'+json['products'][i]['stock']+'</span>';
						html +='</div>';

						html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
						html +='<div class="buttons">';
						html +='<a rel1="'+json['products'][i]['href']+'" href="'+json['products'][i]['href']+'" name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
						html +='<i class="fa fa-info-circle" aria-hidden="true"></i></a>';
						html +='<a rel1="'+json['products'][i]['href']+'" class="addtocartquick" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
						html +='</div></div>';

						html +='<div class="caption">';
						html +='<h1>'+json['products'][i]['name']+'</h1>';
						if(json['products'][i]['price'])
						{
							html +='<p class="price">';
							if(!json['products'][i]['special'])
							{
								html +=' <span class="price-new">'+json['products'][i]['price']+'</span>';
							} else {
								html +=' <span class="price-new">'+json['products'][i]['special']+'</span> <span class="price-old">'+json['products'][i]['price']+'</span>';
							}
							if(json['products'][i]['tax'])
							{
								html +='<span class="price-tax">'+json['products'][i]['tax']+'</span>';
							}
							html +='</p>';
						}
						html +='<b>'+json['products'][i]['options']+'</b>';
						html +='</div></div></div>';
					}else{
						html +='<div class="col-md-20 col-lg-1 col-sm-3 col-xs-6">';
						html +='<div class="box4">';
						html +='<div class="image">';
						stockclass="off";
						if(json['products'][i]['stock']<10)
						{
							stockclass='off1';
						}
						html +='<div class="'+stockclass+'">';
						html +='<span>'+json['products'][i]['stock']+'</span>';
						html +='</div>';

						html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
						html +='<div class="buttons">';
						html +='<a rel1="'+json['products'][i]['href']+'" name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
						html +='<i class="fa fa-info-circle" aria-hidden="true"></i></a>';
						html +='<a rel1="'+json['products'][i]['href']+'" class="addtocartquick" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
						html +='</div></div>';

						html +='<div class="caption">';
						html +='<h1>'+json['products'][i]['name']+'</h1>';
						if(json['products'][i]['price'])
						{
							html +='<p class="price">';
							if(!json['products'][i]['special'])
							{
								html +=' <span class="price-new">'+json['products'][i]['price']+'</span>';
							} else {
								html +=' <span class="price-new">'+json['products'][i]['special']+'</span> <span class="price-old">'+json['products'][i]['price']+'</span>';
							}
							if(json['products'][i]['tax'])
							{
								html +='<span class="price-tax">'+json['products'][i]['tax']+'</span>';
							}
							html +='</p>';
						}
						html +='<b>'+json['products'][i]['options']+'</b>';
						html +='</div></div></div>';
					}
				}
				if(json['loadmore'])
				{
					$('#page').val(parseInt(page)+1);
					html +="<div class='loadmoreproduct1 btn btn-primary' rel='"+rel+"' path='"+path+"' >Load more</div>";
					oldpath=path;

				}
				$('.products').append(html);

			}
		}
	});
});
	//design2
	function toggleFullscreen(elem) {
		elem = elem || document.documentElement;
		if (!document.fullscreenElement && !document.mozFullScreenElement &&
			!document.webkitFullscreenElement && !document.msFullscreenElement) {
			if (elem.requestFullscreen) {
				elem.requestFullscreen();
			} else if (elem.msRequestFullscreen) {
				elem.msRequestFullscreen();
			} else if (elem.mozRequestFullScreen) {
				elem.mozRequestFullScreen();
			} else if (elem.webkitRequestFullscreen) {
				elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
			}
		} else {
			if (document.exitFullscreen) {
				document.exitFullscreen();
			} else if (document.msExitFullscreen) {
				document.msExitFullscreen();
			} else if (document.mozCancelFullScreen) {
				document.mozCancelFullScreen();
			} else if (document.webkitExitFullscreen) {
				document.webkitExitFullscreen();
			}
		}
	}
	document.getElementById('btnFullscreen').addEventListener('click', function() {
		toggleFullscreen();
	});


	$('input[name=\'filter_customer\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: 'index.php?route=pos/paynow/autocomplete&user_token='+utoken+'&customer_name=' +  encodeURIComponent(request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['display'],
							value: item['customer_id']
						}
					}));
				}
			});
		},
		'select': function(item) {
			$('input[name=\'filter_customer\']').val(item['label']);
			$('input[name=\'filter_customer_id\']').val(item['value']);
			$('.addfiltercustomer').trigger('click');
		}
	});

	$(document).on('click', '.addfiltercustomer',function(){
		$.ajax({
			url: 'index.php?route=pos/selectcustomer/edit&user_token='+utoken,
			type: 'post',
			data: 'customer_id='+$('input[name=\'filter_customer_id\']').val(),
			dataType: 'json',
			success: function(json) {
				$('.close').trigger('click');

				if (json['products']) {
					$('.loader').addClass('hide');
					html='';
					for (i in json['products']) {
						html +='<div class="col-md-4 col-sm-4 col-xs-12">';
						html +='<div class="box4">';
						stockclass="off";
						if(json['products'][i]['stock']<10)
						{
							stockclass='off1';
						}
						html +='<div class="'+stockclass+'">';
						html +='<span>'+json['products'][i]['stock']+'</span>';
						html +='</div>';
						html +='<div class="image">';
						html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
						html +='</div>';
						html +='<div class="buttons">';
						html +='<a name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
						html +='<i class="fa fa-info-circle" aria-hidden="true"></i>Info</a>';
						html +='<a class="addtocartquick addtocartquick'+json['products'][i]['product_id']+'" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i>Cart</a>';
						html +='</div>';

						html +='<div class="caption"><h1>'+json['products'][i]['name']+'</h1>';
						if(json['products'][i]['price'])
						{

							html +='<p class="price">';
							if(json['products'][i]['special'])
							{
								html +=' <span class="price-new">'+json['products'][i]['special']+'</span> <span class="price-old">'+json['products'][i]['price']+'</span>';

							}else{
								html +=json['products'][i]['price'];
							}
							if(json['products'][i]['tax']!=false)
							{
								html +='<span class="price-tax">'+json['products'][i]['tax']+'</span>';
							}
							html +='</p>';
						}
						html +='</div></div></div>';
					}
					$('.categories').html(' ');
					$('.products').html(html);
				}
			}
		});
	});



	$(document).on('click', '.returnnow',function(){
		$.ajax({
			url: 'index.php?route=pos/returnorder/update&user_token='+utoken,
			type: 'post',
			data: $('.tmdreturncontent input[type=\'text\'], .tmdreturncontent input[type=\'number\'], .tmdreturncontent input[type=\'hidden\'], .tmdreturncontent[type=\'checkbox\']:checked, .tmdreturncontent select, .tmdreturncontent textarea'),
			dataType: 'json',
			beforeSend: function() {
				$('.returnnow').button('loading');
			},
			complete: function() {
				$('.returnnow').button('reset');

			},
			success: function(json) {
				if (json['success']) {
					$('.returnfinal').html('<div class="success alert-success" style="margin:10px;padding:10px;font-size:25px"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + ' <a href="'+json['link']+'" target="_blank"><i class="fa fa-print"></i> </a> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});

	$(document).ready(function(){
		$(document).on('keyup', '.chkQuantity',function(){
			var rel2 = $(this).attr('rel2');
			var quantity = $(this).attr('rel');
			var return_quantity = +this.value || 0;
			if(return_quantity > quantity){
				$('.tmderror'+rel2).html('Quantity can not be grater than '+quantity)
				$(this).val('0');
			}else{
				$('.tmderror'+rel2).html('')
			}
		})
	})
	$(document).on('click', '.searchorder',function(){
		var url = 'index.php?route=pos/returnorder&filter_data=true&tmd_order_id='+$('input[name=\'tmd_order_id\']').val()+'&user_token='+utoken;
		$('.tmdreturncontent').load(url);
		
	});
