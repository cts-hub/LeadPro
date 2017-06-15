<?php

class IndexController extends ControllerBase {
	public function initialize() {
		$this->tag->setTitle('Добро пожаловать');
		parent::initialize();
	}

	public function indexAction() {
		$language = $this->session->get('language');

		$exists = $this->view->getCache()->exists($language . 'index');
		if (!$exists) {

			$news = News::find(array("language='$language'", "limit" => 5, "order" => "published desc"));
			if (count($news) === 0) {
				$news = News::find(array("language='en'", "limit" => 5, "order" => "published desc"));
			}

			//Query the last 5 news
			// $this->view->setVar("news", $news);

		}

		//$this->view->cache(array("lifetime" => 86400, "key" => $language.'index'));
	}

	public function setLanguageAction($language = '') {
		//Change the language, reload translations if needed
		if ($language == 'en' || $language == 'es') {
			$this->session->set('language', $language);
			$this->loadMainTrans();
			$this->loadCustomTrans('index');
		}

		//Go to the last place
		$referer = $this->request->getHTTPReferer();
		if (strpos($referer, $this->request->getHttpHost() . "/") !== false) {
			return $this->response->setHeader("Location", $referer);
		} else {
			return $this->dispatcher->forward(array('controller' => 'index', 'action' => 'index'));
		}
	}

	public function profileAction() {
		$user = $this->chkSessionUser();
		if ($user == false) {
			return $this->redirect();
		}

		if (!$this->request->isPost()) {
			$this->tag->setDefault('name', $user->name);
			$this->tag->setDefault('email', $user->email);
		} else {
			$name        = $this->request->getPost('name', array('string', 'striptags'));
			$email       = $this->request->getPost('email', 'email');
			$user->name  = $name;
			$user->email = $email;
			if ($user->save() == false) {
				foreach ($user->getMessages() as $message) {
					$this->flash->error((string)$message);
				}
			} else {
				$this->flash->success('Your profile information was updated successfully');
			}
		}
	}
}