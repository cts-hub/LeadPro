<?php

class Bases extends Phalcon\Mvc\Model{

	public $db;

	public function initialize() {
		$this->db = $this->getDi()->get('db');
	}

	protected function getData($sql) {
		return $this->db->query($sql)->fetchAll();
	}
}