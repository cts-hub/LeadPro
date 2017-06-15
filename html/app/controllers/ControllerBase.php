<?php
use Phalcon\Mvc\Controller;

class ControllerBase extends Controller {

	protected function _getTransPath() {
		$translationPath = '../app/messages/';
		$language        = $this->session->get("language");
		if (!$language) {
			$this->session->set("language", "en");
		}
		if ($language === 'es' || $language === 'en') {
			return $translationPath . $language;
		} else {
			return $translationPath . 'en';
		}
	}

	public function loadMainTrans() {
		$translationPath = $this->_getTransPath();
		require $translationPath . "/main.php";

		//Return a translation object
		$mainTranslate = new Phalcon\Translate\Adapter\NativeArray(array(
			"content" => $messages
		));

		//Set $mt as main translation object
		$this->view->setVar("mt", $mainTranslate);
	}

	public function loadCustomTrans($transFile) {
		$translationPath = $this->_getTransPath();
		require $translationPath . '/' . $transFile . '.php';

		//Return a translation object
		$controllerTranslate = new Phalcon\Translate\Adapter\NativeArray(array(
			"content" => $messages
		));

		//Set $t as controller's translation object
		$this->view->setVar("t", $controllerTranslate);
	}

	public function initialize() {
		Phalcon\Tag::prependTitle('LeadPro | ');
		$this->loadMainTrans();
		$this->view->setTemplateAfter('main');
	}

	protected function chkSessionUser() {
		$auth = $this->session->get('auth');
		$user = Users::findFirst($auth['id']);
		return $user;
	}

	protected function redirect($controller) {
		$controller = $this->get_str_controller($controller);
		return $this->dispatcher->forward(
			[
				"controller" => "$controller",
				"action" => "index",
			]
		);
	}

	protected function get_str_controller($controller) {
		if (isset($controller) && is_string($controller)) {
			return $controller;
		} else {
			return 'index';
		}
	}

	public function sendToApi($data, $brand) {

		$url = 'http://' . $brand . '/back.php/affiliate/externalSorce/api?method=createLead&' . http_build_query($data);

 		echo "\n\nRequest:\n\n";
 		echo $url;
 		echo "\n\n";

		$ch      = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$page = curl_exec($ch);
		curl_close($ch);

		$data = json_decode($page, true);

// 		echo "\n\nResponse inside sendToApi:\n\n";
// 		echo '<pre>';
// 		print_r($data);
// 		echo '</pre>';
		return $data;
	}

	protected function chkResponseCode($data) {
		if (!empty ($data['createLead']['client_id'])) {
			$client_id = $data['createLead']['client_id'];
			$code      = 0;
			$msg       = '0';
		} else {
			$client_id = 0;
			$code      = $data['error']['code'];
			$msg       = $data['error']['message'];
		}
		return array($client_id, $code, $msg);
	}
}