<?php

class RegisterController extends ControllerBase {
	public function initialize() {
		$this->tag->setTitle('Подписаться/Войти');
		parent::initialize();
	}

	public function indexAction() {
		if ($this->request->isPost()) {
			$name           = $this->request->getPost('name', array('string', 'striptags'));
			$username       = $this->request->getPost('username', 'alphanum');
			$email          = $this->request->getPost('email', 'email');
			$password       = $this->request->getPost('password');
			$repeatPassword = $this->request->getPost('repeatPassword');

			if ($password != $repeatPassword) {
				$this->flash->error('Passwords are different');
				return false;
			}

			$user             = new Users();
			$user->username   = $username;
			$user->password   = sha1($password);
			$user->name       = $name;
			$user->email      = $email;
			$user->created_at = new Phalcon\Db\RawValue('now()');
			$user->active     = 'Y';
			if ($user->save() == false) {
				foreach ($user->getMessages() as $message) {
					$this->flash->error((string)$message);
				}
			} else {
				$this->tag->setDefault('email', '');
				$this->tag->setDefault('password', '');
				return $this->redirect("index");
			}
		}
	}
}