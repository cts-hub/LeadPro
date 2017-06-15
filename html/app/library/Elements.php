<?php

use Phalcon\Mvc\User\Component;

class Elements extends Component {

	private $_headerMenu = array(
		'navbar-left' => array(
			/*'index' => array(
				'caption' => 'Главная',
				'action' => 'index'
			),*/
			'export' => array(
				'caption' => 'Лиды',
				'action' => 'index'
			),
			'about' => array(
				'caption' => 'О системе',
				'action' => 'index'
			),
		),
		'navbar-right' => array(
			'session' => array(
				'caption' => 'Вход/Регистрация',
				'action' => 'index'
			),
		),
	);

	private $_tabs = array(
		'Экспорт' => array(
			'controller' => 'export',
			'action' => 'index',
			'any' => false
		),
		'Импорт' => array(
			'controller' => 'import',
			'action' => 'index',
			'any' => false
		),
		'Транзит' => array(
			'controller' => 'tranzit',
			'action' => 'index',
			'any' => false
		),
		'Отложеный импорт' => array(
			'controller' => 'delayedimport',
			'action' => 'index',
			'any' => false
		),
		'Профиль' => array(
			'controller' => 'index',
			'action' => 'profile',
			'any' => false
		)
	);

	public function getMenu() {

		$auth = $this->session->get('auth');
		if ($auth) {
			$this->_headerMenu['navbar-right']['session'] = array(
				'caption' => 'Выход',
				'action' => 'end'
			);
		} else {
			unset($this->_headerMenu['navbar-left']['export']);
		}

		$controllerName = $this->view->getControllerName();
		foreach ($this->_headerMenu as $position => $menu) {
			echo '<div class="nav-collapse">';
			echo '<ul class="nav navbar-nav ', $position, '">';
			foreach ($menu as $controller => $option) {
				if ($controllerName == $controller) {
					echo '<li class="active">';
				} else {
					echo '<li>';
				}
				echo $this->tag->linkTo($controller . '/' . $option['action'], $option['caption']);
				echo '</li>';
			}
			echo '</ul>';
			echo '</div>';
		}
	}

	public function getTabs() {
		$controllerName = $this->view->getControllerName();
		$actionName     = $this->view->getActionName();
		echo '<ul class="nav nav-tabs">';
		foreach ($this->_tabs as $caption => $option) {
			if ($option['controller'] == $controllerName && ($option['action'] == $actionName || $option['any'])) {
				echo '<li class="active">';
			} else {
				echo '<li>';
			}
			echo $this->tag->linkTo($option['controller'] . '/' . $option['action'], $caption), '</li>';
		}
		echo '</ul>';
	}
}