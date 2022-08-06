<?php
class ControllerCommonLogout extends Controller {
	public function index() {
		$this->user->logout();

		unset($this->session->data['user_token']);
		////sharma for pos
		unset($this->session->data['posuser']);
		unset($this->session->data['checkout_posapp_id']);
		unset($this->session->data['posapp_id']);
		unset($this->session->data['selected_order_id']);

		$this->response->redirect($this->url->link('pos/login', '', true));
		// $this->response->redirect($this->url->link('common/login', '', true));
	}
}