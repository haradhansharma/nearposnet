<?php
class ControllerPosPoscss extends Controller {

	public function index() {

		header("Content-type: text/css", true);

		$data['bodybg'] = $this->config->get('setting_bodycolor');
		$data['posmaincolor'] = $this->config->get('setting_maincolor');
		$data['posmaincolortext'] = $this->config->get('setting_maincolortxt');
		$data['topbarbg'] = $this->config->get('setting_topbar');
		$data['topbartext'] = $this->config->get('setting_topbartext');
		$data['boxbg'] = $this->config->get('setting_boxbgcolor');
		$data['tablebg'] = $this->config->get('setting_tablebgcolor');
		$data['tabletxt'] = $this->config->get('setting_tabletxtcolor');
		$data['probg'] = $this->config->get('setting_probgcolor');
		$data['clearcartbg'] = $this->config->get('setting_clearcartbg');
		$data['clearcart'] = $this->config->get('setting_clearcart');
		$data['btnscolorbg'] = $this->config->get('setting_btnscolorbg');
		$data['btnscolor'] = $this->config->get('setting_btnscolor');

		echo ".pospage #container,.pos-design1,.pos-design2,.pos-design3{background:".$data['bodybg']."!important;}";

		echo ".sidebar ul li .btn-primary,.boxbg2 .scrollbox3 .box4{border-color:".$data['posmaincolor']."!important;}";

		echo ".icon .btn-primary, .icon .btn-primary:active:hover, .icon .btn-primary.active:hover, .icon .open > .btn-primary.dropdown-toggle:hover, .icon .btn-primary:active:focus, .icon .btn-primary.active:focus, .icon .open > .btn-primary.dropdown-toggle:focus, .icon .btn-primary.focus:active, .icon .btn-primary.active.focus, .icon .open > .btn-primary.dropdown-toggle.focus,.sidebar li button, .clickexpend,.cartcoupon,#dash .rightside .value .paynow,.sidebar ul li .btn-primary,.off,.off1,.bgc1,.pos-design1 .btn-srch,.pos-design3 .categories li.active a, .pos-design3 .categories li:hover a,.scrollbox3 .box4 .buttons a:hover,label #btn,.boxbg2 .scrollbox3 .box4 .caption,.boxbg2 .iconbox,.noncate{background:".$data['posmaincolor']."!important;}";

		echo ".icon .btn-primary, .icon .btn-primary:active:hover, .icon .btn-primary.active:hover, .icon .open > .btn-primary.dropdown-toggle:hover, .icon .btn-primary:active:focus, .icon .btn-primary.active:focus, .icon .open > .btn-primary.dropdown-toggle:focus, .icon .btn-primary.focus:active, .icon .btn-primary.active.focus, .icon .open > .btn-primary.dropdown-toggle.focus,.sidebar li button, .clickexpend,.cartcoupon,#dash .rightside .value .paynow,#dash .icon a i,.sidebar i, .sidebar .custbox i, .sidebar .printbox i,.off span, .off1 span,.sidebar ul li .btn-primary,.breadcrumbsload,#dash .icon .breadcrumbsload .showsubcate:before,.bgc1,.pos-design3 .categories li.active a, .pos-design3 .categories li:hover a,.boxbg2 .scrollbox3 .box4 .caption h1,.boxbg2 .scrollbox3 .box4 .caption p,.boxbg2 .scrollbox3 .box4 .caption b,.noncate{color:".$data['posmaincolortext']."!important;}";

		echo "#dash .leftside,#dash .rightside{background:".$data['boxbg']."!important;}";

		echo ".handle3{background:".$data['posmaincolor']."!important;}";

		echo " .leftbox .tablebox .qty i,#dash .price,.pos-design1 .boxbg1 .tablebox .qty i, .pos-design3 .boxbg1 .tablebox .qty i,.categories .showsubcate1.active .catediv i, .categories .showsubcate1.active .catediv, .subcategories .showsubcate1.active .catediv i, .subcategories .showsubcate1.active .catediv,.scrollbox3 .box4 .caption p{color:".$data['posmaincolor']."!important;}";

		echo ".scrollbox3 .box4 .caption p .price-old{color:#333 !important;}";

		echo ".btn-primary,#dash .leftside .buttons a,.input-group-addon,.btn-hold{border-color:".$data['btnscolorbg']."!important;}";

		echo ".btn-primary,#dash .leftside .buttons a,.input-group-addon,.btn-hold,.pos-design3 #dash .dashbox .search .btn-srch{background:".$data['btnscolorbg']."!important;}";

		echo "label #cancel{color:".$data['btnscolorbg']."!important;}";

		echo ".btn-primary,#dash .leftside .buttons a,.input-group-addon,.btn-hold{color:".$data['btnscolor']."!important;}";

		echo ".btn-primary:hover, .btn-primary:focus, .btn-primary.focus,#dash .leftside .buttons a:hover,.input-group-addon:hover,.btn-hold:hover,.categories .showsubcate1.active .catediv, .subcategories .showsubcate1.active .catediv{border-color:".$data['posmaincolor']."!important;}";

		echo ".btn-primary:hover, .btn-primary:focus, .btn-primary.focus,#dash .leftside .buttons a:hover,.input-group-addon:hover,.btn-hold:hover{background:".$data['posmaincolor']."!important;}";

		echo ".btn-primary:hover, .btn-primary:focus, .btn-primary.focus,#dash .leftside .buttons a:hover,.input-group-addon:hover,.btn-hold:hover{color:".$data['posmaincolortext']."!important;}";

		echo "#dash .leftside ul li,#dash .leftside .box4,.sidebar3{background:".$data['probg']."!important;}";

		echo ".clearcart{background:".$data['clearcartbg']."!important;color:".$data['clearcart']."!important;}";

		echo ".clearcart:hover, .clearcart:focus, .clearcart.focus{background:".$data['clearcart']."!important;color:".$data['clearcartbg']."!important;}";

		echo "#dash .rightside table tr th,#dash .rightside .value,#dash .icon,#dash .search,.pos-design2 .tablebox .scrollbox4 table tr:nth-child(even) td,.pos-design3 .boxbg1 .btn-coupon, .pos-design2 .sub-totals .btn-coupon,.pos-design2 .tablebox table tr th{background:".$data['tablebg']."!important;}";

		echo "#dash .rightside table tr th,#dash .rightside .value ul li,.pospage .totalload tr td,.pos-design2 .tablebox .scrollbox4 table tr:nth-child(even) td,.pos-design3 .boxbg1 .btn-coupon, .pos-design2 .sub-totals .btn-coupon,.pos-design2 .tablebox table tr th{color:".$data['tabletxt']."!important;}";

		echo "#dash .leftside ul li{border-right:1px solid ".$data['tablebg']."!important;border-bottom:1px solid ".$data['tablebg']."!important;}";
		echo "#dash .leftside ul li i{color:".$data['tablebg']."!important;}";

		echo "#content > #dash .dashbox{background:".$data['topbarbg']."!important;}";

	}
}
